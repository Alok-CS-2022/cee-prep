<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\User;
use App\Models\Attempt;
use App\Models\Exam;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_questions' => Question::count(),
            'active_questions' => Question::where('status', 'active')->count(),
            'total_users' => User::where('role', 'student')->count(),
            'total_attempts' => Attempt::count(),
            'total_mock_exams' => Exam::where('type', 'mock')->count(),
            'average_score' => round(Attempt::where('status', 'submitted')->avg('score') ?? 0, 2),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
