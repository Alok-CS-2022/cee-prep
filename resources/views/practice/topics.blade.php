<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Practice: {{ $subject->name }}
        </h2>
    </x-slot>

    <div class="py-8" x-data="{ selected: [] }">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <p class="text-gray-600 mb-4">Pick the topics you want to practice.</p>

                <form action="{{ route('practice.start') }}" method="POST">
                    @csrf
                    <input type="hidden" name="subject_id" value="{{ $subject->id }}">

                    <div class="space-y-2 mb-6">
                        @foreach ($topics as $topic)
                            <label class="flex items-center justify-between border border-gray-200 rounded-md px-4 py-3 cursor-pointer hover:bg-gray-50">
                                <span class="flex items-center gap-3">
                                    <input type="checkbox" name="topic_ids[]" value="{{ $topic->id }}" x-model="selected" class="text-indigo-600 rounded">
                                    <span class="text-sm font-medium text-gray-800">{{ $topic->name }}</span>
                                </span>
                                <span class="text-xs text-gray-500">{{ $topic->questions_count }} questions</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit"
                            :disabled="selected.length === 0"
                            :class="selected.length === 0 ? 'opacity-40 cursor-not-allowed' : ''"
                            class="bg-indigo-200 text-black font-semibold px-5 py-2.5 rounded-md shadow hover:bg-indigo-300 border border-indigo-400">
                            Start Practice
                        </button>
                        <a href="{{ route('practice.index') }}" class="text-sm text-gray-500 hover:underline">Back to subjects</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>