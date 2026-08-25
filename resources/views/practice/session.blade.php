<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Practice Session
        </h2>
    </x-slot>

    <div class="py-8" x-data="practiceApp()" x-init="init()">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            {{-- In progress --}}
            <div x-show="!finished" class="bg-white shadow-sm rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-sm text-gray-500">
                        Question <span x-text="currentIndex + 1"></span> of <span x-text="questions.length"></span>
                        &middot; <span x-text="questions[currentIndex] ? questions[currentIndex].subject : ''"></span>
                        &middot; <span x-text="questions[currentIndex] ? questions[currentIndex].topic : ''"></span>
                    </span>
                    <span class="text-sm font-semibold text-green-700" x-text="'Correct: ' + correctCount + '/' + answeredCount"></span>
                </div>

                <template x-if="questions[currentIndex]">
                    <div>
                        <p class="text-gray-800 font-medium mb-4" x-text="questions[currentIndex].question"></p>

                        <template x-if="questions[currentIndex].image_url">
                            <img :src="questions[currentIndex].image_url" alt="Question diagram" class="max-w-full mb-4 rounded border">
                        </template>

                        <div class="space-y-2 mb-4">
                            <template x-for="opt in ['A','B','C','D']" :key="opt">
                                <button type="button"
                                    @click="selectAnswer(opt)"
                                    :disabled="answered"
                                    class="w-full text-left flex items-center gap-3 p-3 border rounded-md"
                                    :class="optionClass(opt)">
                                    <strong x-text="opt"></strong>
                                    <span x-text="questions[currentIndex]['option_' + opt.toLowerCase()]"></span>
                                    <template x-if="answered && opt === questions[currentIndex].correct_option">
                                        <span class="ml-auto text-green-700 text-xs font-semibold">Correct answer</span>
                                    </template>
                                </button>
                            </template>
                        </div>

                        <template x-if="answered">
                            <div class="mb-4">
                                <div class="mb-3 p-3 rounded-md" :class="wasCorrect ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200'">
                                    <p class="text-sm font-semibold" :class="wasCorrect ? 'text-green-700' : 'text-red-700'" x-text="wasCorrect ? 'Correct!' : 'Not quite.'"></p>
                                    <template x-if="questions[currentIndex].explanation">
                                        <p class="text-sm text-gray-600 mt-1" x-text="questions[currentIndex].explanation"></p>
                                    </template>
                                </div>

                                <template x-if="!confidenceRecorded">
                                    <div class="mb-4">
                                        <p class="text-xs text-gray-500 mb-2">How sure were you?</p>
                                        <div class="flex gap-2">
                                            <button type="button" @click="setConfidence('guessed')" class="text-xs px-3 py-1.5 rounded border border-gray-300 hover:bg-gray-100">Guessed</button>
                                            <button type="button" @click="setConfidence('unsure')" class="text-xs px-3 py-1.5 rounded border border-gray-300 hover:bg-gray-100">Unsure</button>
                                            <button type="button" @click="setConfidence('confident')" class="text-xs px-3 py-1.5 rounded border border-gray-300 hover:bg-gray-100">Confident</button>
                                        </div>
                                    </div>
                                </template>

                                <button type="button" @click="next()"
                                    class="bg-indigo-200 text-black font-semibold px-5 py-2.5 rounded-md shadow hover:bg-indigo-300 border border-indigo-400">
                                    <span x-text="currentIndex + 1 < questions.length ? 'Next Question' : 'Finish'"></span>
                                </button>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            {{-- Finished summary --}}
            <div x-show="finished" class="bg-white shadow-sm rounded-lg p-8 text-center">
                <p class="text-sm text-gray-500 mb-1">Practice Complete</p>
                <div class="text-5xl font-bold text-indigo-600 mb-2" x-text="correctCount + '/' + questions.length"></div>
                <p class="text-sm text-gray-500 mb-8">questions correct</p>
                <div class="flex justify-center gap-3">
                    <a href="{{ route('practice.index') }}"
                        class="inline-block bg-indigo-200 text-black font-semibold px-5 py-2.5 rounded-md shadow hover:bg-indigo-300 border border-indigo-400">
                        Practice Again
                    </a>
                    <a href="{{ route('dashboard') }}"
                        class="inline-block bg-gray-100 text-gray-700 font-semibold px-5 py-2.5 rounded-md border border-gray-300">
                        Dashboard
                    </a>
                </div>
            </div>

        </div>
    </div>

    <script>
        function practiceApp() {
            return {
                questions: @json($questionsData),
                currentIndex: 0,
                answered: false,
                selectedOption: null,
                wasCorrect: false,
                confidenceRecorded: false,
                correctCount: 0,
                answeredCount: 0,
                finished: false,
                questionStartTime: null,
                recordUrl: '{{ route('practice.record-answer') }}',

                init() {
                    this.questionStartTime = Date.now();
                },

                optionClass(opt) {
                    if (!this.answered) return 'border-gray-300 hover:border-indigo-400 hover:bg-indigo-50';
                    const correct = this.questions[this.currentIndex].correct_option;
                    if (opt === correct) return 'border-green-500 bg-green-50';
                    if (opt === this.selectedOption && opt !== correct) return 'border-red-400 bg-red-50';
                    return 'border-gray-200 opacity-60';
                },

                selectAnswer(opt) {
                    if (this.answered) return;
                    this.selectedOption = opt;
                    this.answered = true;
                    this.confidenceRecorded = false;
                    const correct = this.questions[this.currentIndex].correct_option;
                    this.wasCorrect = opt === correct;
                    this.answeredCount++;
                    if (this.wasCorrect) this.correctCount++;

                    const timeSpent = Math.round((Date.now() - this.questionStartTime) / 1000);
                    this.submitAnswer(opt, this.wasCorrect, null, timeSpent);
                },

                setConfidence(level) {
                    this.confidenceRecorded = true;
                    this.submitAnswer(this.selectedOption, this.wasCorrect, level, null);
                },

                submitAnswer(selected, correct, confidence, timeSpent) {
                    const body = {
                        question_id: this.questions[this.currentIndex].id,
                        selected_option: selected,
                        is_correct: correct,
                    };
                    if (confidence) body.confidence = confidence;
                    if (timeSpent !== null) body.time_spent_seconds = timeSpent;

                    fetch(this.recordUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify(body),
                    }).catch(() => {});
                },

                next() {
                    if (this.currentIndex + 1 < this.questions.length) {
                        this.currentIndex++;
                        this.answered = false;
                        this.selectedOption = null;
                        this.confidenceRecorded = false;
                        this.questionStartTime = Date.now();
                    } else {
                        this.finished = true;
                    }
                },
            };
        }
    </script>
</x-app-layout>