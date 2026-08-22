<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Questions
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-4 flex justify-between items-center gap-3">
                <a href="{{ route('admin.questions.create') }}" class="inline-block bg-indigo-200 text-black font-semibold px-5 py-2.5 rounded-md shadow hover:bg-indigo-300 border border-indigo-400">
                    + Add Question
                </a>
                <a href="{{ route('admin.questions.import') }}" class="inline-block bg-indigo-200 text-black font-semibold px-5 py-2.5 rounded-md shadow hover:bg-indigo-300 border border-indigo-400">
                    Bulk Import (CSV)
                </a>
            </div>

            <form method="GET" class="bg-white shadow-sm rounded-lg p-4 mb-4 grid grid-cols-2 md:grid-cols-5 gap-3">
                <select name="subject_id" class="border-gray-300 rounded-md text-sm" onchange="this.form.submit()">
                    <option value="">All Subjects</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>{{ $subject->name }}</option>
                    @endforeach
                </select>

                <select name="topic_id" class="border-gray-300 rounded-md text-sm" onchange="this.form.submit()">
                    <option value="">All Topics</option>
                    @foreach ($topics as $topic)
                        <option value="{{ $topic->id }}" {{ request('topic_id') == $topic->id ? 'selected' : '' }}>{{ $topic->name }}</option>
                    @endforeach
                </select>

                <select name="difficulty" class="border-gray-300 rounded-md text-sm" onchange="this.form.submit()">
                    <option value="">All Difficulties</option>
                    @foreach (['easy', 'medium', 'hard'] as $level)
                        <option value="{{ $level }}" {{ request('difficulty') == $level ? 'selected' : '' }}>{{ ucfirst($level) }}</option>
                    @endforeach
                </select>

                <select name="cognitive_level" class="border-gray-300 rounded-md text-sm" onchange="this.form.submit()">
                    <option value="">All Cognitive Levels</option>
                    @foreach (['recall', 'understanding', 'application'] as $level)
                        <option value="{{ $level }}" {{ request('cognitive_level') == $level ? 'selected' : '' }}>{{ ucfirst($level) }}</option>
                    @endforeach
                </select>

                <select name="status" class="border-gray-300 rounded-md text-sm" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    @foreach (['draft', 'active', 'inactive'] as $s)
                        <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </form>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 font-semibold text-gray-600">Question</th>
                            <th class="px-4 py-3 font-semibold text-gray-600">Subject</th>
                            <th class="px-4 py-3 font-semibold text-gray-600">Topic</th>
                            <th class="px-4 py-3 font-semibold text-gray-600">Difficulty</th>
                            <th class="px-4 py-3 font-semibold text-gray-600">Status</th>
                            <th class="px-4 py-3 font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($questions as $question)
                            <tr class="border-b">
                                <td class="px-4 py-3 max-w-md truncate">{{ Str::limit($question->question, 80) }}</td>
                                <td class="px-4 py-3">{{ $question->subject->name }}</td>
                                <td class="px-4 py-3">{{ $question->topic->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ ucfirst($question->difficulty) }}</td>
                                <td class="px-4 py-3">
                                    <span @class([
                                        'px-2 py-1 rounded text-xs font-semibold',
                                        'bg-green-100 text-green-800' => $question->status === 'active',
                                        'bg-yellow-100 text-yellow-800' => $question->status === 'draft',
                                        'bg-gray-200 text-gray-700' => $question->status === 'inactive',
                                    ])>
                                        {{ ucfirst($question->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 space-x-2">
                                    <a href="{{ route('admin.questions.edit', $question) }}" class="text-indigo-600 hover:underline">Edit</a>
                                    <form action="{{ route('admin.questions.destroy', $question) }}" method="POST" class="inline" onsubmit="return confirm('Delete this question?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-4 text-gray-500 text-center">No questions yet. Add your first one above.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $questions->links() }}
            </div>

        </div>
    </div>
</x-app-layout>

