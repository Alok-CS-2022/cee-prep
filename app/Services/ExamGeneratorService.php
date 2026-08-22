<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamConfiguration;
use App\Models\ExamQuestion;
use App\Models\Question;
use Illuminate\Support\Facades\DB;

class ExamGeneratorService
{
    /**
     * Check if there are enough active questions to generate an exam
     * for the given configuration. Returns an array of shortage messages.
     * Empty array means everything is fine.
     */
    public function checkAvailability(ExamConfiguration $config): array
    {
        $shortages = [];

        foreach ($config->subject_distribution as $subjectName => $requiredCount) {
            $availableCount = Question::whereHas('subject', function ($query) use ($subjectName) {
                $query->where('name', $subjectName);
            })->where('status', 'active')->count();

            if ($availableCount < $requiredCount) {
                $shortages[] = [
                    'subject' => $subjectName,
                    'required' => $requiredCount,
                    'available' => $availableCount,
                    'missing' => $requiredCount - $availableCount,
                ];
            }
        }

        return $shortages;
    }

    /**
     * Generate a new exam for the given user based on the config.
     * Throws an exception with a clear message if there aren't enough questions.
     */
    public function generate(ExamConfiguration $config, int $userId, string $title, string $type = 'mock'): Exam
    {
        $shortages = $this->checkAvailability($config);

        if (! empty($shortages)) {
            $messages = array_map(function ($s) {
                return "{$s['subject']}: need {$s['required']}, only have {$s['available']} active (missing {$s['missing']})";
            }, $shortages);

            throw new \RuntimeException(
                "Not enough active questions to generate this exam. " . implode('; ', $messages)
            );
        }

        return DB::transaction(function () use ($config, $userId, $title, $type) {
            $exam = Exam::create([
                'exam_configuration_id' => $config->id,
                'user_id' => $userId,
                'title' => $title,
                'type' => $type,
            ]);

            $order = 1;

            foreach ($config->subject_distribution as $subjectName => $requiredCount) {
                $questionIds = Question::whereHas('subject', function ($query) use ($subjectName) {
                    $query->where('name', $subjectName);
                })
                    ->where('status', 'active')
                    ->inRandomOrder()
                    ->limit($requiredCount)
                    ->pluck('id');

                foreach ($questionIds as $questionId) {
                    ExamQuestion::create([
                        'exam_id' => $exam->id,
                        'question_id' => $questionId,
                        'order' => $order,
                    ]);
                    $order++;
                }
            }

            return $exam;
        });
    }
}