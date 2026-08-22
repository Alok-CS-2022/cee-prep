<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Generate Mock Exam
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @if (! $config)
                <div class="bg-white shadow-sm rounded-lg p-6 text-center text-gray-500">
                    No active exam configuration found.
                    <a href="{{ route('admin.exam-configurations.index') }}" class="text-indigo-600 hover:underline">
                        Set one up here.
                    </a>
                </div>
            @else
                <div class="bg-white shadow-sm rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-1">{{ $config->name }}</h3>
                    <p class="text-sm text-gray-500 mb-4">
                        {{ $config->total_questions }} questions &middot; {{ $config->duration_minutes }} minutes &middot;
                        +{{ $config->marks_correct }} / {{ $config->marks_wrong }} marking
                    </p>

                    <table class="w-full text-sm border">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-left border">Subject</th>
                                <th class="px-3 py-2 text-left border">Required</th>
                                <th class="px-3 py-2 text-left border">Available (Active)</th>
                                <th class="px-3 py-2 text-left border">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($availability as $row)
                                <tr class="{{ $row['ok'] ? 'bg-green-50' : 'bg-red-50' }}">
                                    <td class="px-3 py-2 border">{{ $row['subject'] }}</td>
                                    <td class="px-3 py-2 border">{{ $row['required'] }}</td>
                                    <td class="px-3 py-2 border">{{ $row['available'] }}</td>
                                    <td class="px-3 py-2 border">
                                        @if ($row['ok'])
                                            <span class="text-green-700 font-semibold">&#10003; Ready</span>
                                        @else
                                            <span class="text-red-700 font-semibold">&#10007; Need {{ $row['required'] - $row['available'] }} more</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-6">
                        @if (empty($shortages))
                            <form method="POST" action="{{ route('admin.exam-generator.generate') }}">
                                @csrf
                                <button type="submit"
                                    class="bg-indigo-200 text-black font-semibold px-5 py-2.5 rounded-md shadow hover:bg-indigo-300 border border-indigo-400">
                                    Generate New Mock Exam
                                </button>
                            </form>
                        @else
                            <button type="button" disabled
                                class="bg-gray-200 text-gray-500 font-semibold px-5 py-2.5 rounded-md border border-gray-300 cursor-not-allowed">
                                Generate New Mock Exam
                            </button>
                            <p class="text-sm text-red-600 mt-2">
                                Add more active questions to the subjects marked above before generating a full exam.
                            </p>
                        @endif
                    </div>
                </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Recently Generated Exams</h3>
                @forelse ($recentExams as $exam)
                    <div class="flex justify-between items-center py-2 border-b last:border-b-0">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $exam->title }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $exam->examConfiguration->name ?? '-' }} &middot; {{ $exam->created_at->format('d M Y, h:i A') }}
                            </p>
                        </div>
                        <span class="text-xs bg-gray-100 text-gray-700 px-2 py-1 rounded">{{ ucfirst($exam->type) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No exams generated yet.</p>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>