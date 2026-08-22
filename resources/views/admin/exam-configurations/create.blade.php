<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Create Exam Configuration
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if ($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-md p-4 text-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.exam-configurations.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Program</label>
                        <select name="program_id" class="block w-full border-gray-300 rounded-md text-sm">
                            @foreach ($programs as $program)
                                <option value="{{ $program->id }}" {{ old('program_id') == $program->id ? 'selected' : '' }}>
                                    {{ $program->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Configuration Name</label>
                        <input type="text" name="name" value="{{ old('name', 'MECEE-BL Standard Mock') }}"
                               class="block w-full border-gray-300 rounded-md text-sm">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes)</label>
                        <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 180) }}"
                               class="block w-full border-gray-300 rounded-md text-sm">
                    </div>

                    <div class="mb-6 border-t pt-4">
                        <label class="block text-sm font-semibold text-gray-800 mb-3">Marking Scheme (per question)</label>
                        <div class="grid grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Correct Answer</label>
                                <input type="number" step="0.01" name="marks_correct" value="{{ old('marks_correct', 1) }}"
                                       class="block w-full border-gray-300 rounded-md text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Wrong Answer</label>
                                <input type="number" step="0.01" name="marks_wrong" value="{{ old('marks_wrong', -0.25) }}"
                                       class="block w-full border-gray-300 rounded-md text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Unanswered</label>
                                <input type="number" step="0.01" name="marks_unanswered" value="{{ old('marks_unanswered', 0) }}"
                                       class="block w-full border-gray-300 rounded-md text-sm">
                            </div>
                        </div>
                    </div>

                    <div class="mb-6 border-t pt-4">
                        <label class="block text-sm font-semibold text-gray-800 mb-3">Questions per Subject</label>
                        <div class="space-y-3">
                            @foreach ($subjects as $subject)
                                <div class="flex items-center gap-3">
                                    <label class="w-32 text-sm text-gray-700">{{ $subject->name }}</label>
                                    <input type="number" min="0"
                                           name="subject_distribution[{{ $subject->name }}]"
                                           value="{{ old('subject_distribution.' . $subject->name, 0) }}"
                                           class="subject-count-input block w-32 border-gray-300 rounded-md text-sm">
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 text-sm font-semibold text-gray-800">
                            Total: <span id="total-questions">0</span> questions
                        </div>
                    </div>

                    <div class="mb-6 flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}
                               class="rounded border-gray-300">
                        <label for="is_active" class="text-sm text-gray-700">Set as active configuration</label>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit"
                            class="bg-indigo-200 text-black font-semibold px-5 py-2.5 rounded-md shadow hover:bg-indigo-300 border border-indigo-400">
                            Save Configuration
                        </button>
                        <a href="{{ route('admin.exam-configurations.index') }}" class="text-sm text-gray-600 hover:underline">
                            Cancel
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        function updateTotal() {
            const inputs = document.querySelectorAll('.subject-count-input');
            let total = 0;
            inputs.forEach(input => {
                total += parseInt(input.value) || 0;
            });
            document.getElementById('total-questions').textContent = total;
        }
        document.querySelectorAll('.subject-count-input').forEach(input => {
            input.addEventListener('input', updateTotal);
        });
        document.addEventListener('DOMContentLoaded', updateTotal);
    </script>
</x-app-layout>
