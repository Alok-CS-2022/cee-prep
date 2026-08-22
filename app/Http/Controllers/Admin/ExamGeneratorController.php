<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamConfiguration;
use App\Services\ExamGeneratorService;
use Illuminate\Http\Request;

class ExamGeneratorController extends Controller
{
    public function index(ExamGeneratorService $generator)
    {
        $config = ExamConfiguration::where('is_active', true)->first();

        $shortages = [];
        $availability = [];

        if ($config) {
            $shortages = $generator->checkAvailability($config);

            foreach ($config->subject_distribution as $subjectName => $requiredCount) {
                $shortage = collect($shortages)->firstWhere('subject', $subjectName);
                $availability[] = [
                    'subject' => $subjectName,
                    'required' => $requiredCount,
                    'available' => $shortage ? $shortage['available'] : $requiredCount,
                    'ok' => ! $shortage,
                ];
            }
        }

        $recentExams = Exam::with('examConfiguration')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.exam-generator.index', compact('config', 'availability', 'shortages', 'recentExams'));
    }

    public function generate(Request $request, ExamGeneratorService $generator)
    {
        $config = ExamConfiguration::where('is_active', true)->first();

        if (! $config) {
            return redirect()->route('admin.exam-generator.index')
                ->with('error', 'No active exam configuration found. Please set one up first.');
        }

        try {
            $title = 'Mock Exam - ' . now()->format('d M Y, h:i A');
            $exam = $generator->generate($config, auth()->id(), $title, 'mock');

            return redirect()->route('admin.exam-generator.index')
                ->with('success', "Exam generated successfully! \"{$title}\" with {$config->total_questions} questions is ready.");
        } catch (\RuntimeException $e) {
            return redirect()->route('admin.exam-generator.index')
                ->with('error', $e->getMessage());
        }
    }
}