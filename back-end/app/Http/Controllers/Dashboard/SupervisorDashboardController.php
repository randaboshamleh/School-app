<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ExamScore;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Student;

class SupervisorDashboardController extends Controller
{
    public function index()
    {
        return response()->json([
            'students_count' => Student::count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'exam_scores_count' => ExamScore::count(),
            'notifications_count' => Notification::count(),
        ]);
    }
}
