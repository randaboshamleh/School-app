<!doctype html>
<html>
<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <title>@yield('title','Admin')</title>
</head>
<body>
<nav class="navbar navbar-expand bg-light">
  <a class="navbar-brand" href="{{ route('exam_score.index') }}">Admin</a>
  <div class="ms-auto">
    <a href="{{ route('logout') }}" class="btn btn-sm btn-outline-secondary">Logout</a>
  </div>
</nav>
<div class="container-fluid">
  <div class="row">
    <div class="col-2 bg-light vh-100">
      <ul class="nav flex-column p-2">
        <li class="nav-item"><a class="nav-link" href="{{ route('students.index') }}">الطلاب</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('exam_score.index') }}">العلامات</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('payments.index') }}">الدفعات</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('notifications.index') }}">الإشعارات</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('exams.index') }}">جداول الامتحانات</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('time_table.index') }}">جداول الأسبوع</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('students.index') }}">الملف الشخصي</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('transport_subscribtions.index') }}">النقل</a></li>
      </ul>
    </div>
    <div class="col-10 p-4">
      @include('admin.partials.alerts')
      @yield('content')
    </div>
  </div>
</div>
</body>
</html>
