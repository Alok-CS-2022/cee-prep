<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Exam Configurations
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
                <a href="{{ route('admin.exam-configurations.create') }}"
                   class="inline-block bg-indigo-200 text-black font-semibold px-5 py-2.5 rounded-md shadow hover:bg-indigo-300 border border-indigo-400">
                    + New Exam Configuration
                </a>
            </div>

            @forelse ($configurations as $config)
                <div class="bg-white shadow-sm rounded-lg p-6 mb-4">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">{{ $config->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $config->program->name ?? 'No program' }}</p>
                        </div>
                        @if ($config->is_active)
                            <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded">Active</span>
                        @else
                            <span class="bg-gray-200 text-gray-700 text-xs font-semibold px-2 py-1 rounded">Inactive</span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm mb-4">
                        <div>
                            <span class="text-gray-500">Total Questions</span>
                            <p class="font-semibold text-gray-800">{{ $config->total_questions }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Duration</span>
                            <p class="font-semibold text-gray-800">{{ $config->duration_minutes }} min</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Marks (Correct / Wrong)</span>
                            <p class="font-semibold text-gray-800">+{{ $config->marks_correct }} / {{ $config->marks_wrong }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Unanswered</span>
                            <p class="font-semibold text-gray-800">{{ $config->marks_unanswered }}</p>
                        </div>
                    </div>

                    <div class="border-t pt-3">
                        <span class="text-xs text-gray-500 uppercase font-semibold">Subject Distribution</span>
                        <div class="flex flex-wrap gap-2 mt-2">
                            @foreach ($config->subject_distribution ?? [] as $subjectName => $count)
                                <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">
                                    {{ $subjectName }}: {{ $count }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="border-t mt-4 pt-3 flex gap-3">
                        <a href="{{ route('admin.exam-configurations.edit', $config) }}" class="text-indigo-600 hover:underline text-sm">Edit</a>
                        <form action="{{ route('admin.exam-configurations.destroy', $config) }}" method="POST" onsubmit="return confirm('Delete this configuration?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline text-sm">Delete</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="bg-white shadow-sm rounded-lg p-6 text-center text-gray-500">
                    No exam configurations yet. Create your first one above.
                </div>
            @endforelse

        </div>
    </div>
</x-app-layout>
