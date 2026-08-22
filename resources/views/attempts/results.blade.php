<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Exam Results
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Score summary --}}
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
                <div class="grid grid-cols-3 gap-4 mb-4">
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

            {{-- Subject-by-subject breakdown --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Subject-wise Performance</h3>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b">
                            <th class="py-2">Subject</th>
                            <th class="py-2 text-center">Correct</th>
                            <th class="py-2 text-center">Wrong</th>
                            <th class="py-2 text-center">Unanswered</th>
                            <th class="py-2 text-right">Accuracy</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($subjectStats as $subject => $stats)
                            <tr class="border-b last:border-0">
                                <td class="py-2 font-medium">{{ $subject }}</td>
                                <td class="py-2 text-center text-green-700">{{ $stats['correct'] }}</td>
                                <td class="py-2 text-center text-red-700">{{ $stats['wrong'] }}</td>
                                <td class="py-2 text-center text-gray-500">{{ $stats['unanswered'] }}</td>
                                <td class="py-2 text-right font-semibold">{{ $stats['accuracy'] }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Weak topics --}}
            @if (count($weakTopics) > 0)
                <div class="bg-yellow-50 border border-yellow-200 shadow-sm rounded-lg p-6">
                    <h3 class="font-semibold text-gray-800 mb-1">Topics to Focus On</h3>
                    <p class="text-xs text-gray-500 mb-4">Topics where accuracy was below 50%</p>
                    <ul class="space-y-2">
                        @foreach ($weakTopics as $topic)
                            <li class="flex justify-between items-center bg-white rounded-md px-4 py-2 border border-yellow-100">
                                <span class="font-medium text-sm">{{ $topic['name'] }}</span>
                                <span class="text-xs text-gray-500">
                                    {{ $topic['correct'] }}/{{ $topic['total'] }} correct
                                    &middot;
                                    <span class="font-semibold text-yellow-700">{{ $topic['accuracy'] }}%</span>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Question-by-question review --}}
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Question Review</h3>
                <div class="space-y-4">
                    @foreach ($reviewData as $i => $item)
                        <div class="border rounded-md p-4
                            @if ($item['outcome'] === 'correct') border-green-200 bg-green-50
                            @elseif ($item['outcome'] === 'wrong') border-red-200 bg-red-50
                            @else border-gray-200 bg-gray-50
                            @endif
                        ">
                            <div class="flex justify-between items-start mb-2">
                                <p class="text-sm font-medium">Q{{ $i + 1 }}. {{ $item['question'] }}</p>
                                <span class="text-xs px-2 py-1 rounded-full font-semibold
                                    @if ($item['outcome'] === 'correct') bg-green-200 text-green-800
                                    @elseif ($item['outcome'] === 'wrong') bg-red-200 text-red-800
                                    @else bg-gray-200 text-gray-700
                                    @endif
                                ">
                                    {{ ucfirst($item['outcome']) }}
                                </span>
                            </div>
                            <div class="text-xs text-gray-500 mb-2">{{ $item['subject'] }}</div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-1 text-sm mb-2">
                                @foreach (['A' => 'option_a', 'B' => 'option_b', 'C' => 'option_c', 'D' => 'option_d'] as $letter => $field)
                                    <div class="px-2 py-1 rounded
                                        @if ($letter === $item['correct_option']) bg-green-200 font-semibold
                                        @elseif ($letter === $item['selected_option'] && $item['outcome'] === 'wrong') bg-red-200
                                        @endif
                                    ">
                                        {{ $letter }}. {{ $item[$field] }}
                                        @if ($letter === $item['correct_option'])
                                            <span class="text-green-700">&#10003;</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            @if ($item['selected_option'])
                                <p class="text-xs text-gray-600 mb-1">Your answer: <span class="font-semibold">{{ $item['selected_option'] }}</span></p>
                            @else
                                <p class="text-xs text-gray-600 mb-1">Your answer: <span class="italic">Not answered</span></p>
                            @endif
                            @if ($item['explanation'])
                                <div class="text-xs text-gray-600 bg-white rounded p-2 mt-2 border">
                                    <span class="font-semibold">Explanation:</span> {{ $item['explanation'] }}
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</x-app-layout>