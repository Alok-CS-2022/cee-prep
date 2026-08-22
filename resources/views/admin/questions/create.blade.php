<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Add Question
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6">

                <form action="{{ route('admin.questions.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    @include('admin.questions._form')

                    <div class="flex gap-3">
                        <button type="submit" class="bg-indigo-200 text-black font-semibold px-5 py-2.5 rounded-md shadow hover:bg-indigo-300 border border-indigo-400">
                            Save Question
                        </button>
                        <a href="{{ route('admin.questions.index') }}" class="text-gray-600 px-4 py-2">
                            Cancel
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
