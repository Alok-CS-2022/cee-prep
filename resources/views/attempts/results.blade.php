<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Exam Results
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-8 text-center">

                <p class="text-sm text-gray-500 mb-1">{{ $attempt->exam->title }}</p>
                <p class="text-xs text-gray-400 mb-6">
                    Status: {{ ucfirst($attempt->status) }}
                    @if ($attempt->submitted_at)
                        &middot; Submitted {{ $attempt->submitted_at->format('d M Y, h:i A') }}
                    @endif
                </p>

                <div class="text-5xl font-bold text-indigo-600 mb-2">
                    {{ number_format($attempt->score, 2) }}
                </div>
                <p class="text-sm text-gray-500 mb-8">
                    out of {{ $attempt->exam->examConfiguration->total_questions ?? '-' }} marks
                </p>

                <div class="grid grid-cols-3 gap-4 mb-8">
                    <div class="bg-green-50 rounded-md p-4">
                        <p class="text-2xl font-bold text-green-700">{{ $attempt->correct_count }}</p>
                        <p class="text-xs text-gray-600">Correct</p>
                    </div>
                    <div class="bg-red-50 rounded-md p-4">
                        <p class="text-2xl font-bold text-red-700">{{ $attempt->wrong_count }}</p>
                        <p class="text-xs text-gray-600">Wrong</p>
                    </div>
                    <div class="bg-gray-50 rounded-md p-4">
                        <p class="text-2xl font-bold text-gray-700">{{ $attempt->unanswered_count }}</p>
                        <p class="text-xs text-gray-600">Unanswered</p>
                    </div>
                </div>

                <a href="{{ route('dashboard') }}"
                    class="inline-block bg-indigo-200 text-black font-semibold px-5 py-2.5 rounded-md shadow hover:bg-indigo-300 border border-indigo-400">
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</x-app-layout>