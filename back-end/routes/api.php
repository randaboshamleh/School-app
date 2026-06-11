<?php

use App\Http\Controllers\AdvertisementsController;
use App\Http\Controllers\Api\AcademicYearController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\ClassRoomController;
use App\Http\Controllers\Api\ClassRoomsSubjectTeacherController;
use App\Http\Controllers\Api\DeviceTokenController;
use App\Http\Controllers\Api\EnrollmentController;
use App\Http\Controllers\Api\ExamComponentController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\ExamScoreController;
use App\Http\Controllers\Api\HomeworkController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\TimeTableController;
use App\Http\Controllers\Api\TransportRouteController;
use App\Http\Controllers\Api\TransportSubscriptionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Auth\SupervisorAuthController;
use App\Http\Controllers\Dashboard\SupervisorDashboardController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ✅ Test Route
Route::get('/test', fn () => response()->json(['message' => 'API is working!']));

// ✅ Auth Routes (خارج الـ Middleware)
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/auth', [UserController::class, 'registerOrLogin']);
Route::post('/forgot-password', [UserController::class, 'forgotPassword']);
Route::post('/reset-password', [UserController::class, 'resetPassword']);
Route::post('/save-fcm-token', [UserController::class, 'saveFcmToken']);

// ✅ Ads Routes
Route::get('/ads', [AdvertisementsController::class, 'index']);
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('/ads', [AdvertisementsController::class, 'store']);
    Route::put('/ads/{id}', [AdvertisementsController::class, 'update']);
    Route::delete('/ads/{id}', [AdvertisementsController::class, 'destroy']);
});

// ✅ Public Routes
Route::get('/timetable', [TimeTableController::class, 'index']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/students', [StudentController::class, 'index']);
    Route::get('/students/{id}', [StudentController::class, 'show']);
    Route::post('/students', [StudentController::class, 'store']);
    Route::put('/students/{id}', [StudentController::class, 'update']);
    Route::delete('/students/{id}', [StudentController::class, 'destroy']);
});
Route::get('/exams', [ExamController::class, 'index']);
Route::post('/exams', [ExamController::class, 'store']);

// ✅ Protected Routes (auth:sanctum)
Route::middleware('auth:sanctum')->group(function () {

    // Debug user info
    Route::get('/whoami', function (Request $request) {
        return [
            'id' => $request->user()->id,
            'roles' => $request->user()->getRoleNames(),
            'guard' => Auth::getDefaultDriver(),
        ];
    });

    // ✅ Route للطلاب فقط
    Route::get('/exam-scores', [ExamScoreController::class, 'myScores']);

    // ✅ Routes للأدمن فقط
    Route::middleware('role:admin')->group(function () {
        Route::get('grades', [ExamScoreController::class, 'managementIndex']);
        Route::apiResource('grades', ExamScoreController::class)->except(['index']);
        Route::apiResource('users', UserController::class);
        Route::apiResource('roles', RoleController::class);
        Route::post('/payments', [PaymentController::class, 'store']);
        Route::put('/payments/{payment}', [PaymentController::class, 'update']);
        Route::delete('/payments/{payment}', [PaymentController::class, 'destroy']);
        Route::apiResource('notifications', NotificationController::class)->only(['store', 'destroy']);
    });

    // ✅ Protected resources accessible by both (admin + student)
    Route::get('/profile', fn (Request $request) => $request->user());
    Route::get('/user', fn (Request $request) => $request->user());
    Route::apiResource('teachers', TeacherController::class);
    Route::apiResource('subjects', SubjectController::class);
    Route::apiResource('exam_component', ExamComponentController::class);
    Route::apiResource('academic-years', AcademicYearController::class);
    Route::apiResource('attendances', AttendanceController::class);
    Route::apiResource('class-rooms', ClassRoomController::class);
    Route::apiResource('class-rooms-subject-teachers', ClassRoomsSubjectTeacherController::class);
    Route::apiResource('enrollments', EnrollmentController::class);
    Route::apiResource('transport-routes', TransportRouteController::class);
    Route::apiResource('homeworks', HomeworkController::class);

    // Payments accessible by both (index/show)
    Route::get('/payments', [PaymentController::class, 'index']);
    Route::get('/payments/{payment}', [PaymentController::class, 'show']);

    // FCM tokens
    Route::post('/fcm-token', [DeviceTokenController::class, 'store']);
    Route::delete('/fcm-token', [DeviceTokenController::class, 'destroy']);

    // Notifications flexible
    Route::middleware('can:send-notifications')->group(function () {
        Route::post('/notifications-flexible', [NotificationController::class, 'sendFlexible']);
    });

    // Import/Export
    Route::post('/grades/import', [ExamScoreController::class, 'import'])->name('grades.import');
    Route::get('/grades/export', [ExamScoreController::class, 'export'])->name('grades.export');
    Route::post('/payments/import', [PaymentController::class, 'import'])->name('payments.import');
    Route::get('/payments/export', [PaymentController::class, 'export'])->name('payments.export');
});

// ✅ Test route للطلاب
Route::get('test-role', fn () => 'Hello Student!')->middleware('role:student');

// ✅ Test closure مشابه للـ Controller
Route::get('/exam_scores_test', function (Request $request) {
    return [
        'id' => $request->user()->id,
        'roles' => $request->user()->getRoleNames(),
        'hasRole' => $request->user()->hasRole('student'),
    ];
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('transport/status', [TransportSubscriptionController::class, 'status']);
    Route::post('transport/subscribe', [TransportSubscriptionController::class, 'subscribe']);
    Route::delete('transport/subscribe/{routeId}', [TransportSubscriptionController::class, 'cancel']);
});

Route::prefix('supervisor')->name('supervisor.')->group(function () {
    // 🔐 تسجيل الدخول
    Route::post('/login', [SupervisorAuthController::class, 'login']);

    // 🧭 كل شيء بعد تسجيل الدخول يتطلب التوكن
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [SupervisorAuthController::class, 'logout']);
        Route::get('/dashboard', [SupervisorDashboardController::class, 'index']);

        // عرض العلامات
        Route::get('/grades', [ExamScoreController::class, 'managementIndex'])->name('grades.index');

        // تعديل علامة
        Route::put('/grades/{examScore}', [ExamScoreController::class, 'update'])->name('grades.update');

        // إنشاء علامة جديدة
        Route::post('/grades', [ExamScoreController::class, 'store'])->name('grades.store');

        // حذف علامة
        Route::delete('/grades/{examScore}', [ExamScoreController::class, 'destroy'])->name('grades.destroy');

        // الإشعارات
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications', [NotificationController::class, 'store'])->name('notifications.store');
        Route::delete('/notifications', [NotificationController::class, 'destroy'])->name('notifications.destroy');

        // الطالبات
        Route::get('/students', [StudentController::class, 'index'])->name('students.index');
        Route::get('/students/{id}', [StudentController::class, 'show'])->name('students.show');
        Route::post('/students/{id}', [StudentController::class, 'store'])->name('students.store');
        Route::put('/students/{id}', [StudentController::class, 'update'])->name('students.update');
        Route::delete('/students/{id}', [StudentController::class, 'destroy'])->name('students.destroy');

        // عرض جميع الدفعات (أو حسب الطالب)
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');

        // عرض دفعة واحدة
        Route::get('/payments/{id}', [PaymentController::class, 'show'])->name('payments.show');

        // إنشاء دفعة جديدة
        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');

        // تعديل دفعة موجودة
        Route::put('/payments/{id}', [PaymentController::class, 'update'])->name('payments.update');

        // حذف دفعة
        Route::delete('/payments/{id}', [PaymentController::class, 'destroy'])->name('payments.destroy');

        // تحديث آخر مهلة دفع
        Route::put('/payments/{id}/due-date', [PaymentController::class, 'updateDueDate'])->name('payments.updateDueDate');

        // وضع دفعة كمدفوعة
        Route::put('/payments/{id}/mark-paid', [PaymentController::class, 'markPaid'])->name('payments.markPaid');

        // تنفيذ الدفع (مع الغرامة)
        Route::post('/payments/{id}/pay', [PaymentController::class, 'pay'])->name('payments.pay');

        // إرسال إشعار تذكير بموعد الدفع
        Route::post('/payments/{id}/notify', [PaymentController::class, 'notifyStudent'])->name('payments.notify');

        // استيراد CSV
        Route::post('/payments/import', [PaymentController::class, 'import'])->name('payments.import');

        // تصدير CSV
        Route::get('/payments/export', [PaymentController::class, 'export'])->name('payments.export');

        // 📘 برامج الامتحانات (النصفية والنهائية)
        Route::get('/exams', [ExamController::class, 'index'])->name('exams.index');
        Route::get('/exams/{id}', [ExamController::class, 'show'])->name('exams.show');
        Route::post('/exams', [ExamController::class, 'store'])->name('exams.store');
        Route::put('/exams/{id}', [ExamController::class, 'update'])->name('exams.update');
        Route::delete('/exams/{id}', [ExamController::class, 'destroy'])->name('exams.destroy');

        // 📅 الجدول الأسبوعي (Time Table)
        Route::get('/timetables', [TimeTableController::class, 'index'])->name('timetables.index');
        Route::get('/timetables/{id}', [TimeTableController::class, 'show'])->name('timetables.show');
        Route::post('/timetables', [TimeTableController::class, 'store'])->name('timetables.store');
        Route::put('/timetables/{id}', [TimeTableController::class, 'update'])->name('timetables.update');
        Route::delete('/timetables/{id}', [TimeTableController::class, 'destroy'])->name('timetables.destroy');
    });
});
Route::get('/ping', function () {
    return response()->json(['status' => 'ok']);
});
