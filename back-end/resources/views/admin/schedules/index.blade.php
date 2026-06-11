@extends('layouts.admin')

@section('content')
<div class="container mt-5">
    <h3 class="mb-4">🧾 جدول الامتحانات</h3>

    <table class="table table-striped text-center">
        <thead class="table-light">
            <tr>
                <th>المادة</th>
                <th>اليوم</th>
                <th>التاريخ</th>
                <th>المدة</th>
                <th>المقرر</th>
            </tr>
        </thead>
        <tbody>
            @foreach($exams as $exam)
                @php
                    // استخراج اليوم من التاريخ
                    $dayName = \Carbon\Carbon::parse($exam->date)->locale('ar')->dayName;

                    // حساب المدة من start_time و end_time
                    $start = \Carbon\Carbon::parse($exam->start_time);
                    $end = \Carbon\Carbon::parse($exam->end_time);
                    $duration = $end->diff($start)->format('%h ساعات %i دقائق');
                @endphp

                <tr>
                    <td>{{ $exam->subject }}</td>
                    <td>{{ $dayName }}</td>
                    <td>{{ \Carbon\Carbon::parse($exam->date)->format('Y-m-d') }}</td>
                    <td>{{ $duration }}</td>
                    <td>{{ $exam->syllabus }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
