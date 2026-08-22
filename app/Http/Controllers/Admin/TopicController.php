<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function index()
    {
        $topics = Topic::with('subject')->withCount('questions')->orderBy('name')->get();

        return view('admin.topics.index', compact('topics'));
    }

    public function create()
    {
        $subjects = Subject::orderBy('name')->get();

        return view('admin.topics.create', compact('subjects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:255',
        ]);

        Topic::create($validated);

        return redirect()->route('admin.topics.index')->with('success', 'Topic created successfully.');
    }

    public function edit(Topic $topic)
    {
        $subjects = Subject::orderBy('name')->get();

        return view('admin.topics.edit', compact('topic', 'subjects'));
    }

    public function update(Request $request, Topic $topic)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:255',
        ]);

        $topic->update($validated);

        return redirect()->route('admin.topics.index')->with('success', 'Topic updated successfully.');
    }

    public function destroy(Topic $topic)
    {
        $topic->delete();

        return redirect()->route('admin.topics.index')->with('success', 'Topic deleted successfully.');
    }
}
