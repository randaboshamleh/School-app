@extends('layouts.admin')

@section('content')
<div class="container mt-5">
    <h3 class="mb-4 text-center">🎓 لوحة موجه الابتدائي إناث</h3>

    {{-- معلومات عن الموجه --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">الموجه: {{ auth()->user()->name }}</h5>
            <p class="card-text">يشرف على جميع الطالبات من الصف التاسع إلى البكالوريا</p>
        </div>
    </div>

    {{-- روابط سريعة --}}
    <div class="row text-center mb-4">
        <div class="col-md-2"><a href="{{ route('grades.index') }}" class="btn btn-outline-primary w-100">📘 العلامات</a></div>
        <div class="col-md-2"><a href="{{ route('notifications.index') }}" class="btn btn-outline-info w-100">🔔 الإشعارات</a></div>
        <div class="col-md-2"><a href="{{ route('students.index') }}" class="btn btn-outline-secondary w-100">👩‍🎓 الطالبات</a></div>
        <div class="col-md-2"><a href="{{ route('timetable.index') }}" class="btn btn-outline-success w-100">📅 الجدول الأسبوعي</a></div>
        <div class="col-md-2"><a href="{{ route('exams.index') }}" class="btn btn-outline-warning w-100">🧾 جدول الامتحانات</a></div>
        <div class="col-md-2"><a href="{{ route('payments.index') }}" class="btn btn-outline-dark w-100">💰 الدفعات</a></div>
    </div>

    {{-- جدول الطالبات المشمولات --}}
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0">👩‍🎓 الطالبات ضمن إشراف الموجه</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped text-center mb-0">
                <thead class="table-light">
                    <tr>
                        <th>الاسم الكامل</th>
                        <th>الصف</th>
                        <th>الشعبة</th>
                        <th>الرقم المدرسي</th>
                        <th>المعدل الفصلي</th>
                        <th>العنوان</th>
                        <th>الجنسية</th>
                        <th>الجنس</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td>{{ $student->full_name }}</td>
                            <td>{{ $student->class }}</td>
                            <td>{{ $student->section }}</td>
                            <td>{{ $student->student_id }}</td>
                            <td>{{ $student->gpa ?? '—' }}</td>
                            <td>{{ $student->address }}</td>
                            <td>{{ $student->nationality }}</td>
                            <td>{{ $student->gender }}</td>
                            <td>{{ $student->status }}</td>
                            <td>
                                <a href="{{ route('students.show', $student->id) }}" class="btn btn-sm btn-outline-secondary">عرض الملف</a>
                                <a href="{{ route('grades.edit', $student->id) }}" class="btn btn-sm btn-outline-primary">تعديل العلامات</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5">لا توجد طالبات حالياً</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
