<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Performance History
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if ($attempts->isEmpty())
                <div class="bg-white shadow-sm rounded-lg p-8 text-center text-gray-500">
                    You haven't completed any exams yet. Once you finish an exam, it will show up here.
                </div>
            @else
                <div class="bg-white shadow-sm rounded-lg p-6 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Exams Completed</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $attempts->count() }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Average Score</p>
                        <p class="text-2xl font-bold text-indigo-600">{{ $averageScore }}</p>
                    </div>
                </div>

                @if ($chartData->count() > 1)
                    @php
                        $scores = $chartData->pluck('score');
                        $maxScore = max($scores->max(), 1);
                        $minScore = min($scores->min(), 0);
                        $range = ($maxScore - $minScore) > 0 ? ($maxScore - $minScore) : 1;
                        $width = 600;
                        $height = 200;
                        $stepX = $chartData->count() > 1 ? $width / ($chartData->count() - 1) : 0;

                        $points = $chartData->values()->map(function ($point, $i) use ($stepX, $height, $minScore, $range) {
                            $x = $i * $stepX;
                            $y = $height - ((($point['score'] - $minScore) / $range) * $height);
                            return round($x, 1) . ',' . round($y, 1);
                        })->implode(' ');
                    @endphp
                    <div class="bg-white shadow-sm rounded-lg p-6">
                        <h3 class="font-semibold text-gray-800 mb-4">Score Trend</h3>
                        <svg viewBox="0 0 {{ $width }} {{ $height + 30 }}" class="w-full h-48">
                            <polyline
                                fill="none"
                                stroke="#4f46e5"
                                stroke-width="3"
                                points="{{ $points }}"
                            />
                            @foreach ($chartData as $i => $point)
                                <circle
                                    cx="{{ round($i * $stepX, 1) }}"
                                    cy="{{ round($height - ((($point['score'] - $minScore) / $range) * $height), 1) }}"
                                    r="4"
                                    fill="#4f46e5"
                                />
                                <text
                                    x="{{ round($i * $stepX, 1) }}"
                                    y="{{ $height + 20 }}"
                                    font-size="10"
                                    text-anchor="middle"
                                    fill="#6b7280"
                                >{{ $point['date'] }}</text>
                            @endforeach
                        </svg>
                    </div>
                @endif

                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">Past Attempts</h3>
                    <div class="space-y-3">
                        @foreach ($attempts as $attempt)
                            <a href="{{ route('attempts.results', $attempt) }}"
                                class="flex justify-between items-center border rounded-md p-4 hover:bg-gray-50 transition">
                                <div>
                                    <p class="font-medium text-sm">{{ $attempt->exam->title }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $attempt->submitted_at ? $attempt->submitted_at->format('d M Y, h:i A') : '-' }}
                                        &middot;
                                        {{ ucfirst($attempt->status) }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-indigo-600">{{ number_format($attempt->score, 2) }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $attempt->correct_count }}C &middot; {{ $attempt->wrong_count }}W &middot; {{ $attempt->unanswered_count }}U
                                    </p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <a href="{{ route('dashboard') }}"
                class="inline-block bg-indigo-200 text-black font-semibold px-5 py-2.5 rounded-md shadow hover:bg-indigo-300 border border-indigo-400">
                Back to Dashboard
            </a>

        </div>
    </div>
</x-app-layout>