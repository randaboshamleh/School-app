@extends('layouts.admin')

@section('content')
<div class="container mt-5">
    <h3 class="mb-4">🔔 الإشعارات</h3>

    @forelse($notifications as $note)
        <div class="alert alert-info mb-2">
            <strong>{{ $note->title }}</strong><br>
            <span>{{ $note->message }}</span><br>
            <small class="text-muted">{{ $note->created_at->diffForHumans() }}</small>
        </div>
    @empty
        <p class="text-muted">لا توجد إشعارات حالياً</p>
    @endforelse
</div>
@endsection
