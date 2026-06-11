@extends('layouts.admin')

@section('content')
<div class="container mt-5">
    <h3 class="mb-4">📊 إدارة الدفعات</h3>

    {{-- استيراد --}}
    <form action="{{ route('payments.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" required>
        <button type="submit" class="btn btn-primary">📤 استيراد الدفعات</button>
    </form>

    {{-- تصدير --}}
    <a href="{{ route('payments.export') }}" class="btn btn-success mt-3">📥 تنزيل الدفعات (CSV)</a>
</div>
@endsection
