<?php

use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\ExamScoreController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\TimeTableController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'name' => config('app.name'),
        'status' => 'ok',
    ]);
});

// Route::get('/test-firebase', function () {
//     dd(app('firebase.messaging'));
// });
// Route::middleware([
//     'auth',
//     'role:primary_female_supervisor|primary_male_supervisor|secondary_female_supervisor|secondary_male_supervisor|thirdy_female_supervisor|thirdy_male_supervisor'
// ])->prefix('supervisor')->group(function () {

//     // 📊 Dashboard
//     Route::get('/dashboard', function () {
//         return view('dashboard.supervisor');
//     })->name('dashboard.supervisor');

//              // عرض العلامات
//         Route::get('/grades', [ExamScoreController::class, 'index'])->name('grades.index');

//         // تعديل علامة
//         Route::put('/grades/{examScore}', [ExamScoreController::class, 'update'])->name('grades.update');

//         // إنشاء علامة جديدة
//         Route::post('/grades', [ExamScoreController::class, 'store'])->name('grades.store');

//         // حذف علامة
//         Route::delete('/grades/{examScore}', [ExamScoreController::class, 'destroy'])->name('grades.destroy');

//     // الإشعارات
//     Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
//     Route::post('/notifications', [NotificationController::class, 'store'])->name('notifications.store');
//     Route::delete('/notifications', [NotificationController::class, 'destroy'])->name('notifications.destroy');

//     // الطالبات
//     Route::get('/students', [StudentController::class, 'index'])->name('students.index');
//     Route::get('/students/{id}', [StudentController::class, 'show'])->name('students.show');
//      Route::post('/students/{id}', [StudentController::class, 'store'])->name('students.store');
//     Route::put('/students/{id}', [StudentController::class, 'update'])->name('students.update');
//     Route::delete('/students/{id}', [StudentController::class, 'destroy'])->name('students.destroy');

//         // عرض جميع الدفعات (أو حسب الطالب)
//         Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');

//         // عرض دفعة واحدة
//         Route::get('/payments/{id}', [PaymentController::class, 'show'])->name('payments.show');

//         // إنشاء دفعة جديدة
//         Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');

//         // تعديل دفعة موجودة
//         Route::put('/payments/{id}', [PaymentController::class, 'update'])->name('payments.update');

//         // حذف دفعة
//         Route::delete('/payments/{id}', [PaymentController::class, 'destroy'])->name('payments.destroy');

//         // تحديث آخر مهلة دفع
//         Route::put('/payments/{id}/due-date', [PaymentController::class, 'updateDueDate'])->name('payments.updateDueDate');

//         // وضع دفعة كمدفوعة
//         Route::put('/payments/{id}/mark-paid', [PaymentController::class, 'markPaid'])->name('payments.markPaid');

//         // تنفيذ الدفع (مع الغرامة)
//         Route::post('/payments/{id}/pay', [PaymentController::class, 'pay'])->name('payments.pay');

//         // إرسال إشعار تذكير بموعد الدفع
//         Route::post('/payments/{id}/notify', [PaymentController::class, 'notifyStudent'])->name('payments.notify');

//         // استيراد CSV
//         Route::post('/payments/import', [PaymentController::class, 'import'])->name('payments.import');

//         // تصدير CSV
//         Route::get('/payments/export', [PaymentController::class, 'export'])->name('payments.export');

//       // 📘 برامج الامتحانات (النصفية والنهائية)
//         Route::get('/exams', [ExamController::class, 'index'])->name('exams.index');
//         Route::get('/exams/{id}', [ExamController::class, 'show'])->name('exams.show');
//         Route::post('/exams', [ExamController::class, 'store'])->name('exams.store');
//         Route::put('/exams/{id}', [ExamController::class, 'update'])->name('exams.update');
//         Route::delete('/exams/{id}', [ExamController::class, 'destroy'])->name('exams.destroy');

//         // 📅 الجدول الأسبوعي (Time Table)
//         Route::get('/timetables', [TimeTableController::class, 'index'])->name('timetables.index');
//         Route::get('/timetables/{id}', [TimeTableController::class, 'show'])->name('timetables.show');
//         Route::post('/timetables', [TimeTableController::class, 'store'])->name('timetables.store');
//         Route::put('/timetables/{id}', [TimeTableController::class, 'update'])->name('timetables.update');
//         Route::delete('/timetables/{id}', [TimeTableController::class, 'destroy'])->name('timetables.destroy');
// });

// Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
