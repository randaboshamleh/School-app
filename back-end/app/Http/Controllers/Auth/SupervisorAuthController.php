<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SupervisorAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        // ابحث عن المستخدم بالاسم
        $user = User::where('name', trim($request->name))->first();

        // تحقق من وجود المستخدم وصحة كلمة المرور
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'بيانات تسجيل الدخول غير صحيحة',
            ], 401);
        }

        // تأكد أنه يملك أحد أدوار المشرفين
        if (! $user->hasAnyRole(['admin', 'primary_male_supervisor', 'primary_female_supervisor', 'secondary_female_supervisor', 'secondary_male_supervisor', 'thirdy_female_supervisor', 'thirdy_male_supervisor'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'ليس لديك صلاحية كمشرف',
            ], 403);
        }

        // حذف التوكنات القديمة (اختياري)
        $user->tokens()->delete();

        // إنشاء توكن جديد
        $token = $user->createToken('supervisor_token')->plainTextToken;

        // ✅ إرجاع بيانات مرتبة وواضحة
        return response()->json([
            'status' => 'success',
            'message' => 'تم تسجيل الدخول بنجاح',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'student_id' => $user->student_id,
                'role' => $user->roles->first()->name ?? null,
                'roles' => $user->roles->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'name' => $role->name,
                        'display_name' => $role->display_name ?? '',
                        'description' => $role->description ?? '',
                    ];
                })->values(),
            ],
        ], 200);
    }
}
