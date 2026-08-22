<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $exam->title }}
            </h2>
            <div id="timer" class="text-lg font-bold text-red-600 bg-red-50 px-4 py-1 rounded-md border border-red-200">
                --:--:--
            </div>
        </div>
    </x-slot>

    <div class="py-6" x-data="examApp()" x-init="init()">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                {{-- Main question panel --}}
                <div class="md:col-span-3 bg-white shadow-sm rounded-lg p-6">

                    <template x-for="(q, index) in questions" :key="q.id">
                        <div x-show="currentIndex === index">
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-sm text-gray-500">
                                    Question <span x-text="index + 1"></span> of <span x-text="questions.length"></span>
                                    &middot; <span x-text="q.subject"></span>
                                </span>
                                <button type="button"
                                    @click="toggleReview(q.id)"
                                    class="text-xs font-semibold px-3 py-1 rounded border"
                                    :class="answers[q.id] && answers[q.id].marked ? 'bg-yellow-100 border-yellow-400 text-yellow-800' : 'bg-gray-50 border-gray-300 text-gray-600'">
                                    <span x-text="answers[q.id] && answers[q.id].marked ? 'Marked for Review' : 'Mark for Review'"></span>
                                </button>
                            </div>

                            <p class="text-gray-800 font-medium mb-4" x-text="q.question"></p>

                            <div class="space-y-2">
                                <template x-for="opt in ['A','B','C','D']" :key="opt">
                                    <label class="flex items-center gap-3 p-3 border rounded-md cursor-pointer"
                                           :class="answers[q.id] && answers[q.id].selected === opt ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300'">
                                        <input type="radio" :name="'q_' + q.id" :value="opt"
                                               :checked="answers[q.id] && answers[q.id].selected === opt"
                                               @change="selectAnswer(q.id, opt)"
                                               class="text-indigo-600">
                                        <span class="text-sm text-gray-700">
                                            <strong x-text="opt"></strong> &mdash; <span x-text="q['option_' + opt.toLowerCase()]"></span>
                                        </span>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </template>

                    <div class="flex justify-between items-center mt-6 pt-4 border-t">
                        <button type="button" @click="prev()" :disabled="currentIndex === 0"
                            class="bg-gray-100 text-gray-700 font-semibold px-4 py-2 rounded-md border border-gray-300 disabled:opacity-40">
                            &larr; Previous
                        </button>

                        <button type="button" @click="clearAnswer()"
                            class="text-sm text-gray-500 hover:underline">
                            Clear Answer
                        </button>

                        <button type="button" @click="next()" :disabled="currentIndex === questions.length - 1"
                            class="bg-gray-100 text-gray-700 font-semibold px-4 py-2 rounded-md border border-gray-300 disabled:opacity-40">
                            Next &rarr;
                        </button>
                    </div>
                </div>

                {{-- Question navigator grid --}}
                <div class="bg-white shadow-sm rounded-lg p-4">
                    <div class="text-xs text-gray-500 mb-3 space-y-1">
                        <div><span class="inline-block w-3 h-3 bg-green-400 rounded-sm mr-1"></span> Answered</div>
                        <div><span class="inline-block w-3 h-3 bg-yellow-300 rounded-sm mr-1"></span> Marked for Review</div>
                        <div><span class="inline-block w-3 h-3 bg-gray-200 rounded-sm mr-1"></span> Not Answered</div>
                    </div>
                    <div class="grid grid-cols-5 gap-2 max-h-96 overflow-y-auto">
                        <template x-for="(q, index) in questions" :key="q.id">
                            <button type="button" @click="goTo(index)"
                                class="text-xs font-semibold py-2 rounded border"
                                :class="{
                                    'border-indigo-600 ring-2 ring-indigo-300': currentIndex === index,
                                    'bg-yellow-300 border-yellow-400': answers[q.id] && answers[q.id].marked,
                                    'bg-green-400 border-green-500 text-white': answers[q.id] && answers[q.id].selected && !answers[q.id].marked,
                                    'bg-gray-100 border-gray-300': !(answers[q.id] && (answers[q.id].selected || answers[q.id].marked))
                                }"
                                x-text="index + 1">
                            </button>
                        </template>
                    </div>

                    <form id="examSubmitForm" :action="submitUrl" method="POST" class="mt-4" @submit="return confirmSubmit($event)">
                        @csrf
                        <button type="submit"
                            class="w-full bg-indigo-200 text-black font-semibold px-4 py-2.5 rounded-md shadow hover:bg-indigo-300 border border-indigo-400">
                            Submit Exam
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <script>
        function examApp() {
            return {
                questions: @json($questionsData),
                answers: @json($answersData),
                currentIndex: 0,
                attemptId: {{ $attempt->id }},
                expiresAt: '{{ $attempt->expires_at->toIso8601String() }}',
                submitUrl: '{{ route('attempts.submit', $attempt) }}',
                saveUrl: '{{ route('attempts.save-answer', $attempt) }}',

                init() {
                    this.startTimer();
                },

                selectAnswer(questionId, option) {
                    if (!this.answers[questionId]) this.answers[questionId] = { selected: null, marked: false };
                    this.answers[questionId].selected = option;
                    this.saveAnswer(questionId);
                },

                clearAnswer() {
                    const q = this.questions[this.currentIndex];
                    if (this.answers[q.id]) {
                        this.answers[q.id].selected = null;
                        this.saveAnswer(q.id);
                    }
                },

                toggleReview(questionId) {
                    if (!this.answers[questionId]) this.answers[questionId] = { selected: null, marked: false };
                    this.answers[questionId].marked = !this.answers[questionId].marked;
                    this.saveAnswer(questionId);
                },

                saveAnswer(questionId) {
                    const a = this.answers[questionId];
                    fetch(this.saveUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify({
                            question_id: questionId,
                            selected_option: a.selected,
                            marked_for_review: a.marked,
                        }),
                    }).catch(() => {});
                },

                next() {
                    if (this.currentIndex < this.questions.length - 1) this.currentIndex++;
                },
                prev() {
                    if (this.currentIndex > 0) this.currentIndex--;
                },
                goTo(index) {
                    this.currentIndex = index;
                },

                confirmSubmit(event) {
                    const total = this.questions.length;
                    const answered = Object.values(this.answers).filter(a => a.selected).length;
                    const unanswered = total - answered;
                    if (!confirm(`You have ${unanswered} unanswered question(s). Submit anyway?`)) {
                        event.preventDefault();
                        return false;
                    }
                    return true;
                },

                startTimer() {
                    const expires = new Date(this.expiresAt).getTime();
                    const timerEl = document.getElementById('timer');

                    const tick = () => {
                        const now = new Date().getTime();
                        const diff = expires - now;

                        if (diff <= 0) {
                            timerEl.textContent = '00:00:00';
                            document.getElementById('examSubmitForm').submit();
                            return;
                        }

                        const h = Math.floor(diff / 3600000);
                        const m = Math.floor((diff % 3600000) / 60000);
                        const s = Math.floor((diff % 60000) / 1000);
                        timerEl.textContent =
                            String(h).padStart(2, '0') + ':' +
                            String(m).padStart(2, '0') + ':' +
                            String(s).padStart(2, '0');

                        setTimeout(tick, 1000);
                    };
                    tick();
                },
            };
        }
    </script>
</x-app-layout>