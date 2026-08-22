<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AttemptController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TopicController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\QuestionImportController;
use App\Http\Controllers\Admin\ExamConfigurationController;
use App\Http\Controllers\Admin\ExamGeneratorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/exams/{exam}/start', [AttemptController::class, 'start'])->name('attempts.start');
    Route::get('/attempts/{attempt}', [AttemptController::class, 'show'])->name('attempts.show');
    Route::post('/attempts/{attempt}/save-answer', [AttemptController::class, 'saveAnswer'])->name('attempts.save-answer');
    Route::post('/attempts/{attempt}/submit', [AttemptController::class, 'submit'])->name('attempts.submit');
    Route::get('/attempts/{attempt}/results', [AttemptController::class, 'results'])->name('attempts.results');
    Route::get('/attempts-history', [AttemptController::class, 'history'])->name('attempts.history');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('subjects', SubjectController::class)->except(['show']);
    Route::resource('topics', TopicController::class)->except(['show']);
    Route::resource('questions', QuestionController::class)->except(['show']);
    Route::get('/questions-import', [QuestionImportController::class, 'showUploadForm'])->name('questions.import');
    Route::post('/questions-import/preview', [QuestionImportController::class, 'preview'])->name('questions.import.preview');
    Route::post('/questions-import/confirm', [QuestionImportController::class, 'confirm'])->name('questions.import.confirm');
    Route::resource('exam-configurations', ExamConfigurationController::class)->except(['show']);
    Route::get('/exam-generator', [ExamGeneratorController::class, 'index'])->name('exam-generator.index');
    Route::post('/exam-generator/generate', [ExamGeneratorController::class, 'generate'])->name('exam-generator.generate');
});

require __DIR__.'/auth.php';
