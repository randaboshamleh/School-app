<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Student;

class SecondaryFemaleController extends Controller
{
    public function index()
    {
        // فلترة الطالبات: الصفوف من 1 إلى 6 فقط، والجنس أنثى
        $students = Student::whereBetween('class', [1, 6])
            ->where('gender', 'female')
            ->get();

        return view('dashboard.secondary_female.index', compact('students'));
    }
}
