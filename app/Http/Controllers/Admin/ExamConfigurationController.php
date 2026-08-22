<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExamConfiguration;
use App\Models\Program;
use App\Models\Subject;
use Illuminate\Http\Request;

class ExamConfigurationController extends Controller
{
    public function index()
    {
        $configurations = ExamConfiguration::with('program')->orderBy('created_at', 'desc')->get();

        return view('admin.exam-configurations.index', compact('configurations'));
    }

    public function create()
    {
        $programs = Program::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();

        return view('admin.exam-configurations.create', compact('programs', 'subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'name' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:1',
            'marks_correct' => 'required|numeric',
            'marks_wrong' => 'required|numeric',
            'marks_unanswered' => 'required|numeric',
            'is_active' => 'nullable|boolean',
            'subject_distribution' => 'required|array',
            'subject_distribution.*' => 'required|integer|min:0',
        ]);

        $totalQuestions = array_sum($validated['subject_distribution']);

        ExamConfiguration::create([
            'program_id' => $validated['program_id'],
            'name' => $validated['name'],
            'duration_minutes' => $validated['duration_minutes'],
            'total_questions' => $totalQuestions,
            'marks_correct' => $validated['marks_correct'],
            'marks_wrong' => $validated['marks_wrong'],
            'marks_unanswered' => $validated['marks_unanswered'],
            'subject_distribution' => $validated['subject_distribution'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.exam-configurations.index')
            ->with('success', 'Exam configuration created successfully.');
    }

    public function edit(ExamConfiguration $examConfiguration)
    {
        $programs = Program::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();

        return view('admin.exam-configurations.edit', compact('examConfiguration', 'programs', 'subjects'));
    }

    public function update(Request $request, ExamConfiguration $examConfiguration)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'name' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:1',
            'marks_correct' => 'required|numeric',
            'marks_wrong' => 'required|numeric',
            'marks_unanswered' => 'required|numeric',
            'is_active' => 'nullable|boolean',
            'subject_distribution' => 'required|array',
            'subject_distribution.*' => 'required|integer|min:0',
        ]);

        $totalQuestions = array_sum($validated['subject_distribution']);

        $examConfiguration->update([
            'program_id' => $validated['program_id'],
            'name' => $validated['name'],
            'duration_minutes' => $validated['duration_minutes'],
            'total_questions' => $totalQuestions,
            'marks_correct' => $validated['marks_correct'],
            'marks_wrong' => $validated['marks_wrong'],
            'marks_unanswered' => $validated['marks_unanswered'],
            'subject_distribution' => $validated['subject_distribution'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.exam-configurations.index')
            ->with('success', 'Exam configuration updated successfully.');
    }

    public function destroy(ExamConfiguration $examConfiguration)
    {
        $examConfiguration->delete();

        return redirect()->route('admin.exam-configurations.index')
            ->with('success', 'Exam configuration deleted successfully.');
    }
}
