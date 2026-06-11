@extends('layouts.admin')

@section('content')
<div class="container mt-5">
    <h3 class="mb-4">👤 الملف الشخصي للطالب</h3>

    <div class="card shadow-sm p-4">
        <h5 class="mb-3">{{ $student->full_name }}</h5>
        <p><strong>الاسم الكامل :</strong> {{ $student->name }}</p>
        <p><strong>الصف:</strong> {{ $student->classroom->name ?? $student->class }}</p>
        <p><strong>الشعبة:</strong> {{ $student->section }}</p>
         <p><strong> الرقم المدرسي :</strong> {{ $student->student_id }}</p>
        <p><strong>المعدل الفصلي:</strong> {{ $student->gpa }}</p>
        <p><strong>العنوان:</strong> {{ $student->address }}</p>
         <p><strong>الجنسية:</strong> {{ $student->nationality }}</p>
        <p><strong>الجنس:</strong> {{ $student->gender }}</p>
        <p><strong>الحالة:</strong> {{ $student->status }}</p>
        <a href="{{ route('students.edit', $student->id) }}" class="btn btn-primary">✏️ تعديل</a>
    </div>
</div>
@endsection
