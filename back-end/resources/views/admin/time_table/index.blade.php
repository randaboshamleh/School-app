@extends('layouts.admin')

@section('content')
<div class="container mt-5">
    <h3 class="mb-4">📅 الجدول الأسبوعي</h3>

    <table class="table table-bordered text-center align-middle">
        <thead class="table-light">
            <tr>
                <th>اليوم</th>
                <th>الحصة 1</th>
                <th>الحصة 2</th>
                <th>الحصة 3</th>
                <th>الحصة 4</th>
                <th>الحصة 5</th>
                <th>الحصة 6</th>
            </tr>
        </thead>
        <tbody>
            @foreach($schedule as $day => $subjects)
                <tr>
                    <td class="fw-bold">{{ $day }}</td>
                    @foreach($subjects as $subject)
                        <td>{{ $subject }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
