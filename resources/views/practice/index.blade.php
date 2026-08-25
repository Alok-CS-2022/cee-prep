<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Practice
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <p class="text-gray-600 mb-6">Choose a subject to practice. You'll get instant feedback on every question.</p>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                    @foreach ($subjects as $subject)
                        @if ($subject->questions_count > 0)
                            <a href="{{ route('practice.topics', $subject) }}"
                                class="border border-gray-200 rounded-lg p-4 text-center hover:border-indigo-400 hover:bg-indigo-50 transition">
                                <p class="font-semibold text-gray-800">{{ $subject->name }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $subject->questions_count }} questions</p>
                            </a>
                        @else
                            <div class="border border-gray-100 rounded-lg p-4 text-center opacity-40 cursor-not-allowed">
                                <p class="font-semibold text-gray-500">{{ $subject->name }}</p>
                                <p class="text-xs text-gray-400 mt-1">No questions yet</p>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>