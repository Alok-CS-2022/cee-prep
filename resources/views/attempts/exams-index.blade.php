<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Available Exams
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if ($exams->isEmpty())
                <div class="bg-white shadow-sm rounded-lg p-8 text-center text-gray-500">
                    No exams have been generated yet. Check back once your admin has set one up.
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($exams as $exam)
                        @php
                            $attempt = $myAttempts->get($exam->id);
                        @endphp
                        <div class="bg-white shadow-sm rounded-lg p-5 flex justify-between items-center">
                            <div>
                                <p class="font-medium text-gray-800">{{ $exam->title }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $exam->examConfiguration->total_questions ?? '-' }} questions
                                    &middot;
                                    {{ $exam->examConfiguration->duration_minutes ?? '-' }} minutes
                                </p>
                            </div>

                            @if (! $attempt)
                                <a href="{{ route('attempts.start', $exam) }}"
                                    class="bg-indigo-200 text-black font-semibold px-5 py-2.5 rounded-md shadow hover:bg-indigo-300 border border-indigo-400 whitespace-nowrap">
                                    Start Exam
                                </a>
                            @elseif ($attempt->status === 'in_progress')
                                <a href="{{ route('attempts.show', $attempt) }}"
                                    class="bg-yellow-200 text-black font-semibold px-5 py-2.5 rounded-md shadow hover:bg-yellow-300 border border-yellow-400 whitespace-nowrap">
                                    Resume Exam
                                </a>
                            @else
                                <a href="{{ route('attempts.results', $attempt) }}"
                                    class="bg-gray-200 text-black font-semibold px-5 py-2.5 rounded-md shadow hover:bg-gray-300 border border-gray-400 whitespace-nowrap">
                                    View Results
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>