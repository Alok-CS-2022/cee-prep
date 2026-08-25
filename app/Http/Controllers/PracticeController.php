<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Topic;
use App\Models\Question;
use App\Models\PracticeAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PracticeController extends Controller
{
    public function index()
    {
        $subjects = Subject::withCount(['questions' => function ($q) {
            $q->where('status', 'active');
        }])->get();

        return view('practice.index', compact('subjects'));
    }

    public function topics(Subject $subject)
    {
        $topics = Topic::where('subject_id', $subject->id)
            ->withCount(['questions' => function ($q) {
                $q->where('status', 'active');
            }])
            ->get()
            ->filter(function ($topic) {
                return $topic->questions_count > 0;
            })
            ->values();

        return view('practice.topics', compact('subject', 'topics'));
    }

    public function start(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'topic_ids' => 'required|array|min:1',
            'topic_ids.*' => 'exists:topics,id',
        ]);

        $questions = Question::with(['subject', 'topic'])
            ->where('subject_id', $validated['subject_id'])
            ->whereIn('topic_id', $validated['topic_ids'])
            ->where('status', 'active')
            ->inRandomOrder()
            ->get();

        if ($questions->isEmpty()) {
            return back()->with('error', 'No active questions found for the selected topics.');
        }

        $questionsData = $questions->map(function ($q) {
            return [
                'id' => $q->id,
                'question' => $q->question,
                'option_a' => $q->option_a,
                'option_b' => $q->option_b,
                'option_c' => $q->option_c,
                'option_d' => $q->option_d,
                'correct_option' => $q->correct_option,
                'explanation' => $q->explanation,
                'image_url' => $q->image_path ? \Illuminate\Support\Facades\Storage::url($q->image_path) : null,
                'subject' => $q->subject->name ?? '',
                'topic' => $q->topic->name ?? '',
            ];
        });

        return view('practice.session', [
            'questionsData' => $questionsData,
        ]);
    }

    public function recordAnswer(Request $request)
    {
        $validated = $request->validate([
            'question_id' => 'required|exists:questions,id',
            'selected_option' => 'nullable|string|max:1',
            'is_correct' => 'required|boolean',
            'confidence' => 'nullable|string|max:20',
            'time_spent_seconds' => 'nullable|integer',
        ]);

        $existing = PracticeAnswer::where('user_id', Auth::id())
            ->where('question_id', $validated['question_id'])
            ->latest('id')
            ->first();

        if ($existing && !isset($validated['selected_option']) && isset($validated['confidence'])) {
            $existing->update(['confidence' => $validated['confidence']]);
        } else {
            PracticeAnswer::create([
                'user_id' => Auth::id(),
                'question_id' => $validated['question_id'],
                'selected_option' => $validated['selected_option'] ?? null,
                'is_correct' => $validated['is_correct'],
                'confidence' => $validated['confidence'] ?? null,
                'time_spent_seconds' => $validated['time_spent_seconds'] ?? null,
                'answered_at' => now(),
            ]);
        }

        return response()->json(['status' => 'ok']);
    }
}