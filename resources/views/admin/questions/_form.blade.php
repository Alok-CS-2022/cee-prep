<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Question</label>
    <textarea name="question" rows="3" class="w-full border-gray-300 rounded-md shadow-sm">{{ old('question', $question->question ?? '') }}</textarea>
    @error('question')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
        <select name="subject_id" class="w-full border-gray-300 rounded-md shadow-sm">
            <option value="">-- Select Subject --</option>
            @foreach ($subjects as $subject)
                <option value="{{ $subject->id }}" {{ old('subject_id', $question->subject_id ?? '') == $subject->id ? 'selected' : '' }}>
                    {{ $subject->name }}
                </option>
            @endforeach
        </select>
        @error('subject_id')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Topic (optional)</label>
        <select name="topic_id" class="w-full border-gray-300 rounded-md shadow-sm">
            <option value="">-- No Topic --</option>
            @foreach ($topics as $topic)
                <option value="{{ $topic->id }}" {{ old('topic_id', $question->topic_id ?? '') == $topic->id ? 'selected' : '' }}>
                    {{ $topic->name }} ({{ $topic->subject->name }})
                </option>
            @endforeach
        </select>
        @error('topic_id')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Option A</label>
        <input type="text" name="option_a" value="{{ old('option_a', $question->option_a ?? '') }}" class="w-full border-gray-300 rounded-md shadow-sm">
        @error('option_a')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Option B</label>
        <input type="text" name="option_b" value="{{ old('option_b', $question->option_b ?? '') }}" class="w-full border-gray-300 rounded-md shadow-sm">
        @error('option_b')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Option C</label>
        <input type="text" name="option_c" value="{{ old('option_c', $question->option_c ?? '') }}" class="w-full border-gray-300 rounded-md shadow-sm">
        @error('option_c')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Option D</label>
        <input type="text" name="option_d" value="{{ old('option_d', $question->option_d ?? '') }}" class="w-full border-gray-300 rounded-md shadow-sm">
        @error('option_d')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Correct Option</label>
        <select name="correct_option" class="w-full border-gray-300 rounded-md shadow-sm">
            <option value="">--</option>
            @foreach (['A', 'B', 'C', 'D'] as $opt)
                <option value="{{ $opt }}" {{ old('correct_option', $question->correct_option ?? '') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
        </select>
        @error('correct_option')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Difficulty</label>
        <select name="difficulty" class="w-full border-gray-300 rounded-md shadow-sm">
            @foreach (['easy', 'medium', 'hard'] as $level)
                <option value="{{ $level }}" {{ old('difficulty', $question->difficulty ?? 'medium') == $level ? 'selected' : '' }}>{{ ucfirst($level) }}</option>
            @endforeach
        </select>
        @error('difficulty')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Cognitive Level</label>
        <select name="cognitive_level" class="w-full border-gray-300 rounded-md shadow-sm">
            <option value="">-- None --</option>
            @foreach (['recall', 'understanding', 'application'] as $level)
                <option value="{{ $level }}" {{ old('cognitive_level', $question->cognitive_level ?? '') == $level ? 'selected' : '' }}>{{ ucfirst($level) }}</option>
            @endforeach
        </select>
        @error('cognitive_level')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
        <select name="status" class="w-full border-gray-300 rounded-md shadow-sm">
            @foreach (['draft', 'active', 'inactive'] as $s)
                <option value="{{ $s }}" {{ old('status', $question->status ?? 'draft') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        @error('status')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>
</div>

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 mb-1">Explanation (optional)</label>
    <textarea name="explanation" rows="2" class="w-full border-gray-300 rounded-md shadow-sm">{{ old('explanation', $question->explanation ?? '') }}</textarea>
    @error('explanation')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Source (optional)</label>
        <input type="text" name="source" value="{{ old('source', $question->source ?? '') }}" class="w-full border-gray-300 rounded-md shadow-sm">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Year (optional)</label>
        <input type="text" name="year" value="{{ old('year', $question->year ?? '') }}" class="w-full border-gray-300 rounded-md shadow-sm">
    </div>
</div>
