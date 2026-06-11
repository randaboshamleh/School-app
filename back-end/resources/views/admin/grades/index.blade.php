@extends('layouts.admin')

@section('content')
<div class="container mt-5">
    <h3 class="mb-4">استيراد العلامات من ملف CSV</h3>

    {{-- رسالة نجاح --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- رسالة خطأ --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- فورم رفع الملف --}}
    <form action="{{ route('grades.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group mb-3">
            <label for="file">اختر ملف CSV:</label>
            <input type="file" name="file" id="file" class="form-control" required>
            <small class="form-text text-muted">
                يجب أن يحتوي الملف على الأعمدة:
                <code>student_id,name,marks_obtained,component_name</code>
            </small>
        </div>
        <button type="submit" class="btn btn-primary">📤 استيراد العلامات</button>
    </form>
</div>
{{-- زر تنزيل CSV --}}
<a href="{{ route('grades.export') }}" class="btn btn-success mt-3">📥 تنزيل العلامات (CSV)</a>
@endsection
