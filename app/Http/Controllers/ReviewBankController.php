<?php

namespace App\Http\Controllers;

use App\Models\AttemptAnswer;
use App\Models\PracticeAnswer;
use Illuminate\Support\Facades\Auth;

class ReviewBankController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $wrongPractice = PracticeAnswer::with('question.subject', 'question.topic')
            ->where('user_id', $userId)
            ->where('is_correct', false)
            ->latest('answered_at')
            ->get()
            ->unique('question_id');

        $guessedCorrect = PracticeAnswer::with('question.subject', 'question.topic')
            ->where('user_id', $userId)
            ->where('is_correct', true)
            ->where('confidence', 'guessed')
            ->latest('answered_at')
            ->get()
            ->unique('question_id');

        $wrongExam = AttemptAnswer::with('question.subject', 'question.topic')
            ->whereHas('attempt', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('is_correct', false)
            ->latest('answered_at')
            ->get()
            ->unique('question_id');

        $seenIds = [];
        $reviewItems = collect();

        foreach ($wrongPractice as $pa) {
            if ($pa->question && !in_array($pa->question_id, $seenIds)) {
                $reviewItems->push([
                    'question' => $pa->question,
                    'reason' => 'Missed in practice',
                    'source' => 'practice',
                ]);
                $seenIds[] = $pa->question_id;
            }
        }

        foreach ($wrongExam as $aa) {
            if ($aa->question && !in_array($aa->question_id, $seenIds)) {
                $reviewItems->push([
                    'question' => $aa->question,
                    'reason' => 'Missed in mock exam',
                    'source' => 'exam',
                ]);
                $seenIds[] = $aa->question_id;
            }
        }

        foreach ($guessedCorrect as $pa) {
            if ($pa->question && !in_array($pa->question_id, $seenIds)) {
                $reviewItems->push([
                    'question' => $pa->question,
                    'reason' => 'Got lucky guessing',
                    'source' => 'guessed',
                ]);
                $seenIds[] = $pa->question_id;
            }
        }

        $bySubject = $reviewItems->groupBy(function ($item) {
            return $item['question']->subject->name ?? 'Unknown';
        });

        return view('review-bank.index', compact('bySubject'));
    }
}