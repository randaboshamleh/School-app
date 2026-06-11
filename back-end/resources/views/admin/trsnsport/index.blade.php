@extends('layouts.admin')

@section('content')
<div class="container mt-5">
    <h3 class="mb-4">🚌 إدارة خدمة النقل المدرسي</h3>

    {{-- رسائل نجاح / فشل --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- زر إضافة اشتراك جديد --}}
    <div class="mb-4">
        <form action="{{ route('transport.subscribe') }}" method="POST" class="row g-3">
            @csrf
            <div class="col-md-4">
                <label>اختر الطالب:</label>
                <select name="user_id" class="form-control" required>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}">{{ $student->full_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label>اختر خط النقل:</label>
                <select name="route_id" class="form-control" required>
                    @foreach($routes as $route)
                        <option value="{{ $route->id }}">{{ $route->name }} — {{ $route->price }} د.ل</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">➕ اشتراك</button>
            </div>
        </form>
    </div>

    {{-- جدول الاشتراكات --}}
    <table class="table table-striped text-center">
        <thead class="table-light">
            <tr>
                <th>الطالب</th>
                <th>خط النقل</th>
                <th>الحالة</th>
                <th>إجراء</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subscriptions as $sub)
                <tr>
                    <td>{{ $sub->user->name }}</td>
                    <td>{{ $sub->route->name }}</td>
                    <td>
                        @if($sub->status === 'active')
                            <span class="badge bg-success">مفعل</span>
                        @else
                            <span class="badge bg-secondary">ملغي</span>
                        @endif
                    </td>
                    <td>
                        @if($sub->status === 'active')
                            <form action="{{ route('transport.cancel', $sub->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-warning btn-sm">❌ إلغاء</button>
                            </form>
                        @else
                            <form action="{{ route('transport.activate', $sub->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success btn-sm">✅ تفعيل</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
