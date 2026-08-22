<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    public function index(Request $request)
    {
        $query = Question::with(['subject', 'topic']);

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('topic_id')) {
            $query->where('topic_id', $request->topic_id);
        }

        if ($request->filled('difficulty')) {
            $query->where('difficulty', $request->difficulty);
        }

        if ($request->filled('cognitive_level')) {
            $query->where('cognitive_level', $request->cognitive_level);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $questions = $query->latest()->paginate(20)->withQueryString();

        $subjects = Subject::orderBy('name')->get();
        $topics = Topic::orderBy('name')->get();

        return view('admin.questions.index', compact('questions', 'subjects', 'topics'));
    }

    public function create()
    {
        $subjects = Subject::orderBy('name')->get();
        $topics = Topic::orderBy('name')->get();

        return view('admin.questions.create', compact('subjects', 'topics'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateQuestion($request);

        Question::create($validated);

        return redirect()->route('admin.questions.index')->with('success', 'Question created successfully.');
    }

    public function edit(Question $question)
    {
        $subjects = Subject::orderBy('name')->get();
        $topics = Topic::orderBy('name')->get();

        return view('admin.questions.edit', compact('question', 'subjects', 'topics'));
    }

    public function update(Request $request, Question $question)
    {
        $validated = $this->validateQuestion($request);

        $question->update($validated);

        return redirect()->route('admin.questions.index')->with('success', 'Question updated successfully.');
    }

    public function destroy(Question $question)
    {
        $question->delete();

        return redirect()->route('admin.questions.index')->with('success', 'Question deleted successfully.');
    }

    private function validateQuestion(Request $request): array
    {
        return $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'topic_id' => 'nullable|exists:topics,id',
            'question' => 'required|string',
            'option_a' => 'required|string|max:500',
            'option_b' => 'required|string|max:500',
            'option_c' => 'required|string|max:500',
            'option_d' => 'required|string|max:500',
            'correct_option' => 'required|in:A,B,C,D',
            'difficulty' => 'required|in:easy,medium,hard',
            'cognitive_level' => 'nullable|in:recall,understanding,application',
            'explanation' => 'nullable|string',
            'source' => 'nullable|string|max:255',
            'year' => 'nullable|string|max:10',
            'status' => 'required|in:draft,active,inactive',
        ]);
    }
}
