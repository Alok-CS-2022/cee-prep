<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Admin Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Total Questions</div>
                    <div class="text-3xl font-bold text-gray-800">{{ $stats['total_questions'] }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Active Questions</div>
                    <div class="text-3xl font-bold text-green-600">{{ $stats['active_questions'] }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Total Students</div>
                    <div class="text-3xl font-bold text-gray-800">{{ $stats['total_users'] }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Total Attempts</div>
                    <div class="text-3xl font-bold text-gray-800">{{ $stats['total_attempts'] }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Total Mock Exams</div>
                    <div class="text-3xl font-bold text-gray-800">{{ $stats['total_mock_exams'] }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Average Score</div>
                    <div class="text-3xl font-bold text-blue-600">{{ $stats['average_score'] }}</div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
