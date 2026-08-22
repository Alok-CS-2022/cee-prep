<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Subjects
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-4">
                <a href="{{ route('admin.subjects.create') }}" class="inline-block bg-indigo-200 text-black font-semibold px-5 py-2.5 rounded-md shadow hover:bg-indigo-300 border border-indigo-400">
                    + Add Subject
                </a>
            </div>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-6 py-3 text-sm font-semibold text-gray-600">Name</th>
                            <th class="px-6 py-3 text-sm font-semibold text-gray-600">Code</th>
                            <th class="px-6 py-3 text-sm font-semibold text-gray-600">Topics</th>
                            <th class="px-6 py-3 text-sm font-semibold text-gray-600">Questions</th>
                            <th class="px-6 py-3 text-sm font-semibold text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($subjects as $subject)
                            <tr class="border-b">
                                <td class="px-6 py-4">{{ $subject->name }}</td>
                                <td class="px-6 py-4">{{ $subject->code }}</td>
                                <td class="px-6 py-4">{{ $subject->topics_count }}</td>
                                <td class="px-6 py-4">{{ $subject->questions_count }}</td>
                                <td class="px-6 py-4 space-x-2">
                                    <a href="{{ route('admin.subjects.edit', $subject) }}" class="text-indigo-600 hover:underline">Edit</a>
                                    <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST" class="inline" onsubmit="return confirm('Delete this subject?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-gray-500 text-center">No subjects yet. Add your first one above.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
