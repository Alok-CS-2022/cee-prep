<?php

namespace App\Http\Controllers;

use App\Models\Attempt;
use App\Models\AttemptAnswer;
use App\Models\Exam;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AttemptController extends Controller
{
    public function start(Exam $exam)
    {
        // If there's already an in-progress attempt for this exam by this user, resume it.
        $existing = Attempt::where('exam_id', $exam->id)
            ->where('user_id', auth()->id())
            ->where('status', 'in_progress')
            ->first();

        if ($existing) {
            return redirect()->route('attempts.show', $existing);
        }

        $config = $exam->examConfiguration;

        $attempt = Attempt::create([
            'exam_id' => $exam->id,
            'user_id' => auth()->id(),
            'started_at' => now(),
            'expires_at' => now()->addMinutes($config->duration_minutes),
            'status' => 'in_progress',
        ]);

        return redirect()->route('attempts.show', $attempt);
    }

    public function show(Attempt $attempt)
    {
        abort_if($attempt->user_id !== auth()->id(), 403);

        // If time has run out but it wasn't auto-submitted yet, submit it now.
        if ($attempt->status === 'in_progress' && $attempt->expires_at->isPast()) {
            $this->finalizeSubmit($attempt, 'expired');
            return redirect()->route('attempts.results', $attempt);
        }

        if ($attempt->status !== 'in_progress') {
            return redirect()->route('attempts.results', $attempt);
        }

        $exam = $attempt->exam()->with(['examQuestions.question.subject'])->first();
        $questions = $exam->examQuestions()->with('question.subject')->orderBy('order')->get();

        $existingAnswers = AttemptAnswer::where('attempt_id', $attempt->id)
            ->get()
            ->keyBy('question_id');

        $questionsData = $questions->map(function ($eq) {
            $q = $eq->question;
            return [
                'id' => $q->id,
                'question' => $q->question,
                'option_a' => $q->option_a,
                'option_b' => $q->option_b,
                'option_c' => $q->option_c,
                'option_d' => $q->option_d,
                'subject' => $q->subject->name ?? '',
            ];
        })->values();

        $answersData = $existingAnswers->mapWithKeys(function ($a) {
            return [$a->question_id => [
                'selected' => $a->selected_option,
                'marked' => (bool) $a->marked_for_review,
            ]];
        });

        return view('attempts.show', compact('attempt', 'exam', 'questionsData', 'answersData'));
    }

    public function saveAnswer(Request $request, Attempt $attempt)
    {
        abort_if($attempt->user_id !== auth()->id(), 403);

        if ($attempt->status !== 'in_progress' || $attempt->expires_at->isPast()) {
            return response()->json(['error' => 'This attempt is no longer active.'], 422);
        }

        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'selected_option' => 'nullable|in:A,B,C,D',
            'marked_for_review' => 'nullable|boolean',
            'time_spent_seconds' => 'nullable|integer|min:0',
        ]);

        $answer = AttemptAnswer::updateOrCreate(
            [
                'attempt_id' => $attempt->id,
                'question_id' => $validated['question_id'],
            ],
            [
                'selected_option' => $validated['selected_option'] ?? null,
                'marked_for_review' => $validated['marked_for_review'] ?? false,
                'time_spent_seconds' => $validated['time_spent_seconds'] ?? 0,
                'answered_at' => $validated['selected_option'] ? now() : null,
            ]
        );

        return response()->json(['success' => true]);
    }

    public function submit(Attempt $attempt)
    {
        abort_if($attempt->user_id !== auth()->id(), 403);

        if ($attempt->status === 'in_progress') {
            $this->finalizeSubmit($attempt, 'submitted');
        }

        return redirect()->route('attempts.results', $attempt);
    }

    public function results(Attempt $attempt)
    {
        abort_if($attempt->user_id !== auth()->id(), 403);

        $exam = $attempt->exam()->with(['examQuestions.question.subject', 'examQuestions.question.topic'])->first();
        $examQuestions = $exam->examQuestions()->with(['question.subject', 'question.topic'])->orderBy('order')->get();

        $answers = AttemptAnswer::where('attempt_id', $attempt->id)->get()->keyBy('question_id');

        $subjectStats = [];
        $topicStats = [];
        $reviewData = [];

        foreach ($examQuestions as $eq) {
            $q = $eq->question;
            $answer = $answers->get($q->id);
            $selected = $answer->selected_option ?? null;

            if (! $selected) {
                $outcome = 'unanswered';
            } elseif ($selected === $q->correct_option) {
                $outcome = 'correct';
            } else {
                $outcome = 'wrong';
            }

            $subjectName = $q->subject->name ?? 'Unknown';
            if (! isset($subjectStats[$subjectName])) {
                $subjectStats[$subjectName] = ['correct' => 0, 'wrong' => 0, 'unanswered' => 0, 'total' => 0];
            }
            $subjectStats[$subjectName][$outcome]++;
            $subjectStats[$subjectName]['total']++;

            $topicName = $q->topic->name ?? null;
            if ($topicName) {
                if (! isset($topicStats[$topicName])) {
                    $topicStats[$topicName] = ['correct' => 0, 'wrong' => 0, 'unanswered' => 0, 'total' => 0];
                }
                $topicStats[$topicName][$outcome]++;
                $topicStats[$topicName]['total']++;
            }

            $reviewData[] = [
                'question' => $q->question,
                'option_a' => $q->option_a,
                'option_b' => $q->option_b,
                'option_c' => $q->option_c,
                'option_d' => $q->option_d,
                'correct_option' => $q->correct_option,
                'selected_option' => $selected,
                'outcome' => $outcome,
                'explanation' => $q->explanation,
                'subject' => $subjectName,
            ];
        }

        foreach ($subjectStats as $name => $stats) {
            $subjectStats[$name]['accuracy'] = $stats['total'] > 0
                ? round(($stats['correct'] / $stats['total']) * 100, 1)
                : 0;
        }

        $weakTopics = [];
        foreach ($topicStats as $name => $stats) {
            $accuracy = $stats['total'] > 0 ? round(($stats['correct'] / $stats['total']) * 100, 1) : 0;
            $topicStats[$name]['accuracy'] = $accuracy;
            if ($accuracy < 50) {
                $weakTopics[] = ['name' => $name, 'accuracy' => $accuracy] + $stats;
            }
        }
        usort($weakTopics, fn ($a, $b) => $a['accuracy'] <=> $b['accuracy']);

        return view('attempts.results', compact('attempt', 'subjectStats', 'topicStats', 'weakTopics', 'reviewData'));
    }

    private function finalizeSubmit(Attempt $attempt, string $status): void
    {
        $exam = $attempt->exam;
        $questionIds = $exam->examQuestions()->pluck('question_id');

        $answers = AttemptAnswer::where('attempt_id', $attempt->id)->get()->keyBy('question_id');

        $correct = 0;
        $wrong = 0;
        $unanswered = 0;

        $questions = \App\Models\Question::whereIn('id', $questionIds)->get()->keyBy('id');

        foreach ($questionIds as $qid) {
            $answer = $answers->get($qid);
            $question = $questions->get($qid);

            if (! $answer || ! $answer->selected_option) {
                $unanswered++;
                continue;
            }

            $isCorrect = $answer->selected_option === $question->correct_option;
            $answer->update(['is_correct' => $isCorrect]);

            if ($isCorrect) {
                $correct++;
            } else {
                $wrong++;
            }
        }

        $config = $exam->examConfiguration;
        $score = ($correct * $config->marks_correct)
            + ($wrong * $config->marks_wrong)
            + ($unanswered * $config->marks_unanswered);

        $attempt->update([
            'status' => $status,
            'submitted_at' => now(),
            'score' => $score,
            'correct_count' => $correct,
            'wrong_count' => $wrong,
            'unanswered_count' => $unanswered,
        ]);
    }
}