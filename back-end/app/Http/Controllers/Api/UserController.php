<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return UserResource::collection(User::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return new UserResource($user);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with('role')->find($id);

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return new UserResource($user);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $data = $request->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return new UserResource($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if (! $user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:5|max:255',
            'student_id' => 'required',
            'password' => 'required|min:6|confirmed',
            'password_confirmation' => 'required|min:6',
        ], [
            'name.required' => 'الاسم مطلوب',
            'name.min' => 'الاسم يجب ان يكون 5 احرف على الأقل',
            'student_id.required' => 'رقم الطالب مطلوب',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
            'password_confirmation.required' => 'تأكيد كلمة المرور مطلوبة',
        ]);

        $studentById = User::where('student_id', $request->student_id)->first();
        $studentByName = User::where('name', $request->name)->first();

        if ($studentById && $studentById->name === $request->name) {
            return response()->json([
                'status' => 'error',
                'code' => 'ALREADY_REGISTERED',
                'message' => 'لديك حساب بالفعل، يرجى تسجيل الدخول',
            ], 422);
        }

        if ($studentById && $studentById->name !== $request->name) {
            return response()->json([
                'status' => 'error',
                'code' => 'NAME_NOT_MATCH',
                'message' => 'يرجى إدخال اسم صحيح',
            ], 422);
        }

        if ($studentByName && $studentByName->student_id !== $request->student_id) {
            return response()->json([
                'status' => 'error',
                'code' => 'STUDENT_ID_NOT_MATCH',
                'message' => 'يرجى إدخال رقم طالب صحيح',
            ], 422);
        }

        if (! $studentById && ! $studentByName) {
            return response()->json([
                'status' => 'error',
                'code' => 'INVALID',
                'message' => 'يرجى إدخال معلومات صحيحة',
            ], 422);
        }

        $student = User::create([
            'name' => $request->name,
            'student_id' => $request->student_id,
            'role' => 'student',
            'password' => Hash::make(trim($request->password)), // توحيد التشفير واستخدام trim
        ]);

        $token = $student->createToken('flutter-app')->plainTextToken;
        $student->assignRole('student');

        return response()->json([
            'message' => 'تم إنشاء الحساب بنجاح',
            'token' => $token,
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'class' => $student->class->name ?? null,
                'student_id' => $student->student_id,
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'student_id' => 'required',
            'password' => 'required|min:6',
        ], [
            'student_id.required' => 'رقم الطالب مطلوب',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 6 أحرف على الأقل',
        ]);

        $identifier = trim($request->student_id);
        $password = trim($request->password);

        // 🔹 محاولة تسجيل دخول كمستخدم (موجه / أدمن)
        $user = User::where('student_id', $identifier)
            ->orWhere('email', $identifier)
            ->first();

        if ($user && Hash::check($password, $user->password)) {
            $token = $user->createToken('flutter-app')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'تم تسجيل الدخول بنجاح ',
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'student_id' => $user->student_id,
                    'role' => $user->roles->first()->name ?? 'student',
                    'roles' => $user->roles->map(fn ($r) => [
                        'id' => $r->id,
                        'name' => $r->name,
                        'display_name' => $r->display_name,
                        'description' => $r->description,
                        'guard_name' => $r->guard_name,
                    ]),
                ],
            ], 200);
        }

        // 🔹 محاولة تسجيل دخول كطالب
        $student = User::where('student_id', $identifier)->first();

        if (! $student) {
            return response()->json([
                'status' => 'error',
                'code' => 'STUDENT_ID_NOT_FOUND',
                'message' => 'يرجى إدخال رقم طالب صحيح',
            ], 422);
        }

        if (! Hash::check($password, $student->password)) {
            return response()->json([
                'status' => 'error',
                'code' => 'PASSWORD_INCORRECT',
                'message' => 'يرجى إدخال كلمة مرور صحيحة',
            ], 422);
        }

        $token = $student->createToken('flutter-app')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'تم تسجيل الدخول بنجاح ',
            'token' => $token,
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'class' => $student->class->name ?? null,
                'student_id' => $student->student_id,
            ],
        ], 200);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,student_id',
            'otp' => 'required|digits:6',
            'password' => 'required|confirmed|min:6',
        ]);

        // أولاً نجيب الطالب من جدول students
        $student = Student::where('student_id', $request->student_id)->first();

        // تحقق من صحة الـ OTP وصلاحيته
        if (! $student->otp || $student->otp != $request->otp || $student->otp_expires_at < now()) {
            return response()->json(['message' => 'رمز غير صالح أو منتهي الصلاحية'], 422);
        }

        // نجيب المستخدم المرتبط بالطالب من جدول users
        $user = User::where('student_id', $student->student_id)->first();

        if (! $user) {
            return response()->json(['message' => 'المستخدم غير موجود في جدول users'], 404);
        }

        // تحديث كلمة المرور في users
        $user->password = Hash::make(trim($request->password));
        $user->save();

        // تصفير بيانات الـ OTP في students
        $student->otp = null;
        $student->otp_expires_at = null;
        $student->save();

        return response()->json(['message' => 'تم تغيير كلمة المرور بنجاح']);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'تم تسجيل الخروج']);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,student_id',
        ]);

        $student = Student::where('student_id', $request->student_id)->first();

        // إنشاء OTP عشوائي
        $otp = rand(100000, 999999);

        // حفظ OTP مؤقتًا (مثلاً في حقل otp و otp_expires_at)
        $student->otp = $otp;
        $student->otp_expires_at = now()->addMinutes(5);
        $student->save();

        // إرسال إشعار FCM
        $fcmToken = $student->fcm_token;
        if ($fcmToken) {
            $this->sendFcmNotification($fcmToken, $otp);
        }

        return response()->json([
            'message' => 'تم إرسال رمز إعادة التعيين عبر إشعار التطبيق',
        ]);
    }

    private function sendFcmNotification($fcmToken, $otp)
    {
        $credentialsPath = base_path(env('GOOGLE_APPLICATION_CREDENTIALS'));
        if (! file_exists($credentialsPath)) {
            \Log::error('Firebase credentials file not found.');

            return;
        }

        // تحميل الاعتماديات من ملف الخدمة
        $client = new \Google\Client;
        $client->setAuthConfig($credentialsPath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];

        // تحديد Project ID من ملف JSON
        $projectId = json_decode(file_get_contents($credentialsPath), true)['project_id'];

        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        $data = [
            'message' => [
                'token' => $fcmToken,
                'notification' => [
                    'title' => 'رمز إعادة تعيين كلمة المرور',
                    'body' => "رمز OTP الخاص بك هو: {$otp}",
                ],
            ],
        ];

        $response = Http::withToken($accessToken)
            ->post($url, $data);

        if ($response->failed()) {
            \Log::error('FCM Error: '.$response->body());
        }
    }

    public function saveFcmToken(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,student_id',
            'fcm_token' => 'required|string',
        ]);

        $student = \App\Models\Student::where('student_id', $request->student_id)->first();

        $student->fcm_token = $request->fcm_token;
        $student->save();

        return response()->json(['message' => 'تم حفظ FCM Token بنجاح']);
    }
}
