<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Bulk Import Questions (CSV)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="mb-6 bg-blue-50 border border-blue-200 rounded-md p-4 text-sm text-gray-700">
                    <p class="font-semibold mb-2">Your CSV file must have these column headers (exact names):</p>
                    <code class="block bg-white border rounded p-2 text-xs overflow-x-auto">
                        question, option_a, option_b, option_c, option_d, correct_answer, subject, topic, difficulty, cognitive_level, explanation, source, year
                    </code>
                    <ul class="list-disc list-inside mt-3 space-y-1">
                        <li><strong>correct_answer</strong> must be A, B, C, or D (uppercase or lowercase is fine).</li>
                        <li><strong>subject</strong> must match an existing subject name exactly (not case-sensitive).</li>
                        <li><strong>topic</strong> is optional, but if given it must match an existing topic name.</li>
                        <li><strong>difficulty</strong> should be easy, medium, or hard. Leave blank to default to medium.</li>
                        <li><strong>cognitive_level</strong> is optional: recall, understanding, or application.</li>
                        <li>All imported questions will be saved as <strong>draft</strong> so you can review before activating them.</li>
                    </ul>
                </div>

                @if ($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-md p-4 text-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.questions.import.preview') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-1">
                            Choose CSV file
                        </label>
                        <input type="file" name="csv_file" id="csv_file" accept=".csv,.txt"
                               class="block w-full text-sm text-gray-700 border border-gray-300 rounded-md p-2">
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit"
                            class="bg-indigo-200 text-black font-semibold px-5 py-2.5 rounded-md shadow hover:bg-indigo-300 border border-indigo-400">
                            Upload &amp; Preview
                        </button>
                        <a href="{{ route('admin.questions.index') }}" class="text-sm text-gray-600 hover:underline">
                            Cancel
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
