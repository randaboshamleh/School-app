@extends('layouts.app')

@section('content')
<div class="p-6 max-w-6xl mx-auto">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">
        Dashboard {{ auth()->user()->name }}
    </h2>

    <div class="bg-white shadow rounded-2xl p-4 mb-6">
        <p class="font-semibold text-gray-700 mb-2">يشرف على الصفوف:</p>
        <ul class="list-disc list-inside text-gray-600">
            @foreach(auth()->user()->classrooms as $classroom)
                <li>{{ $classroom->name }}</li>
            @endforeach
        </ul>
    </div>

    <div class="bg-white shadow rounded-2xl p-4">
        <h3 class="text-lg font-semibold text-gray-700 mb-3">قائمة الطلاب</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-700 text-left">
                        <th class="py-2 px-3 border-b">#</th>
                        <th class="py-2 px-3 border-b">اسم الطالب</th>
                        <th class="py-2 px-3 border-b">الصف</th>
                        <th class="py-2 px-3 border-b">الشعبة</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700">
                    @foreach($students as $student)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-2 px-3 border-b">{{ $loop->iteration }}</td>
                            <td class="py-2 px-3 border-b">{{ $student->name }}</td>
                            <td class="py-2 px-3 border-b">{{ $student->classroom->name }}</td>
                            <td class="py-2 px-3 border-b">{{ $student->section ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
