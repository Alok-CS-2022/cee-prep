<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Review Bank</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-8">

            @if ($bySubject->isEmpty())
                <div class="bg-white p-6 rounded-md shadow text-center text-gray-600">
                    Nothing to review yet. Once you practice or take exams, questions you missed
                    (or guessed right on) will show up here.
                </div>
            @endif

            @foreach ($bySubject as $subjectName => $items)
                <div class="bg-white rounded-md shadow overflow-hidden">
                    <div class="bg-gray-100 px-5 py-3 border-b border-gray-200">
                        <h3 class="font-semibold text-gray-800">{{ $subjectName }} ({{ count($items) }})</h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        @foreach ($items as $item)
                            @php
                                $question = $item['question'];
                                $reason = $item['reason'];
                                $badgeClass = match($item['source']) {
                                    'exam' => 'bg-red-100 text-red-700 border border-red-300',
                                    'practice' => 'bg-yellow-100 text-yellow-800 border border-yellow-300',
                                    'guessed' => 'bg-orange-100 text-orange-700 border border-orange-300',
                                    default => 'bg-gray-100 text-gray-700 border border-gray-300',
                                };
                            @endphp
                            <div class="px-5 py-4 flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $badgeClass }}">
                                            {{ $reason }}
                                        </span>
                                        @if ($question->topic)
                                            <span class="text-xs text-gray-400">{{ $question->topic->name }}</span>
                                        @endif
                                    </div>
                                    <p class="text-gray-800 text-sm">
                                        {{ \Illuminate\Support\Str::limit($question->question, 140) }}
                                    </p>
                                </div>
                                @if ($question->topic)
                                    <a href="{{ route('practice.topics', $question->subject) }}"
                                       class="shrink-0 bg-indigo-200 text-black font-semibold px-4 py-2 rounded-md shadow hover:bg-indigo-300 border border-indigo-400 text-sm whitespace-nowrap">
                                        Practice this topic
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</x-app-layout>