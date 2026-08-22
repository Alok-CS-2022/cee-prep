<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Import Preview
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="mb-6 flex flex-wrap gap-4">
                    <div class="bg-green-50 border border-green-300 rounded-md px-4 py-3 text-sm">
                        <span class="font-bold text-green-700 text-lg">{{ $validCount }}</span>
                        <span class="text-gray-700"> valid rows ready to import</span>
                    </div>
                    <div class="bg-red-50 border border-red-300 rounded-md px-4 py-3 text-sm">
                        <span class="font-bold text-red-700 text-lg">{{ $errorCount }}</span>
                        <span class="text-gray-700"> rows with errors (these will be skipped)</span>
                    </div>
                </div>

                @if ($errorCount > 0)
                    <div class="mb-6 bg-yellow-50 border border-yellow-300 rounded-md p-4 text-sm text-gray-800">
                        <strong>Heads up:</strong> {{ $errorCount }} row(s) have problems and will NOT be imported if you continue.
                        Check the red rows below. If these are typos, it's safer to fix your CSV and re-upload
                        rather than continuing now.
                    </div>
                @endif

                <div class="overflow-x-auto mb-6">
                    <table class="min-w-full text-sm border">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-3 py-2 text-left border">Row</th>
                                <th class="px-3 py-2 text-left border">Status</th>
                                <th class="px-3 py-2 text-left border">Question</th>
                                <th class="px-3 py-2 text-left border">Subject</th>
                                <th class="px-3 py-2 text-left border">Topic</th>
                                <th class="px-3 py-2 text-left border">Correct</th>
                                <th class="px-3 py-2 text-left border">Difficulty</th>
                                <th class="px-3 py-2 text-left border">Errors</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $row)
                                <tr class="{{ $row['is_valid'] ? 'bg-green-50' : 'bg-red-50' }}">
                                    <td class="px-3 py-2 border">{{ $row['row_number'] }}</td>
                                    <td class="px-3 py-2 border">
                                        @if ($row['is_valid'])
                                            <span class="text-green-700 font-bold">&#10003; Valid</span>
                                        @else
                                            <span class="text-red-700 font-bold">&#10007; Error</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 border max-w-xs truncate" title="{{ $row['question'] }}">
                                        {{ $row['question'] }}
                                    </td>
                                    <td class="px-3 py-2 border">{{ $row['subject_name'] }}</td>
                                    <td class="px-3 py-2 border">{{ $row['topic_name'] }}</td>
                                    <td class="px-3 py-2 border">{{ $row['correct_option'] }}</td>
                                    <td class="px-3 py-2 border">{{ $row['difficulty'] }}</td>
                                    <td class="px-3 py-2 border text-red-700">
                                        @if (! empty($row['errors']))
                                            <ul class="list-disc list-inside">
                                                @foreach ($row['errors'] as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center gap-3">
                    <form method="POST" action="{{ route('admin.questions.import.confirm') }}"
                          onsubmit="return confirm('Import {{ $validCount }} valid question(s)? {{ $errorCount }} row(s) with errors will be skipped.');">
                        @csrf
                        <button type="submit"
                            class="bg-indigo-200 text-black font-semibold px-5 py-2.5 rounded-md shadow hover:bg-indigo-300 border border-indigo-400"
                            @if ($validCount === 0) disabled @endif>
                            Confirm Import ({{ $validCount }} question(s))
                        </button>
                    </form>
                    <a href="{{ route('admin.questions.import') }}" class="text-sm text-gray-600 hover:underline">
                        Cancel &amp; go back
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
