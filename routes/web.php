<?php

use App\Http\Controllers\WeclomePage;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MarksController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\HomeworkController;
use App\Http\Controllers\NectaApiController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\TimeTableController;
use App\Http\Controllers\AssingmentController;
use App\Http\Controllers\AttendencyController;
use App\Http\Controllers\users\UserController;
use App\Http\Controllers\StudentMarkController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\Api\V1\ClassController;
use App\Http\Controllers\ClassteacherController;
use App\Http\Controllers\FullCalenderController;
use App\Http\Controllers\TanzaniaDataController;
use App\Http\Controllers\Api\V1\SchoolController;
use App\Http\Controllers\Api\V1\StreamController;
use App\Http\Controllers\ReportDownladController;
use App\Http\Controllers\StreamSubjectController;
use App\Http\Controllers\Api\V1\StudentController;
use App\Http\Controllers\Api\V1\TeacherController;
use App\Http\Controllers\ExaminationResultController;
use App\Http\Controllers\marks\MarksUploadController;
use App\Http\Controllers\Api\V1\AnnouncementController;
use App\Http\Controllers\Api\V1\SchoolCustomController;
use App\Http\Controllers\Api\V1\OnlineApplicationController;
use App\Http\Controllers\Api\V1\SchoolApplicationController;
use App\Http\Controllers\AdminPanel as ControllersAdminPanel;



Route::get('/sms', [SmsController::class, 'sendSms']);
Route::get('/', [WeclomePage::class, 'index'])->name('home');
Route::get('/login', [LoginController::class, 'view'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'store'])->middleware('guest');
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout')->middleware('auth');
Route::get('/results', [NectaApiController::class, 'getResults']);
// Route::get('/results', [NectaController::class]);
Route::get('/forgot-password', [PasswordController::class, 'index'])->name('password-forgot')->middleware('guest');
Route::post('/forgot-password', [PasswordController::class, 'store'])->name('password.email')->middleware('guest');
Route::get('/reset-password/{token}', function (string $token) {
    return view('auth.reset-password', ['token' => $token]);
})->middleware('guest')->name('password.reset');
Route::post('/reset-password', [PasswordController::class, 'reset'])->name('password.update')->middleware('guest');
Route::get('/register-school', [SchoolApplicationController::class, 'create'])->name('register.school');
Route::post('/register-school', [SchoolApplicationController::class, 'store'])->name('register.school.store');
Route::get('/waiting/approve', [SchoolApplicationController::class, 'waiting'])->name('application.waiting');
Route::get('/online-application', [OnlineApplicationController::class, 'createOnlineApplication'])->name('online.application');
Route::middleware(['auth', 'adminOnly'])->group(function () {

    

    // Route to handle scheduling the application
    Route::patch('/application/{application}/schedule', [SchoolApplicationController::class, 'scheduleApplication'])->name('application.schedule');
    Route::get('/application/review', [SchoolApplicationController::class, 'review'])->name('application.review');
    Route::get('/application/{application}', [SchoolApplicationController::class, 'show'])->name('application.show');
    Route::get('/applications/{application}/verify', [SchoolApplicationController::class, 'showVerifyForm'])->name('application.showVerifyForm');
    Route::put('/applications/{application}/verify', [SchoolApplicationController::class, 'verifyApplication'])->name('application.verify');
    Route::patch('/application/{application}/update-status', [SchoolApplicationController::class, 'updateStatus'])->name('application.updateStatus');
    Route::get('/admin', [ControllersAdminPanel::class, 'index'])->name('admin.home');
    Route::get('/school/list', [SchoolController::class, 'index'])->name('school.list');
    Route::get('/school/{school}', [SchoolController::class, 'show'])->name('school.view');
    Route::put('/school/{school}', [SchoolController::class, 'changeStatus'])->name('school.updateStatus');
    Route::get('/appliaction/list', [SchoolApplicationController::class, 'index'])->name('application.list');
});
Route::get('students/export', [StudentController::class, 'exportStudents']);
Route::get('/geo-location-data',  [TanzaniaDataController::class, 'fetchRegions'])->name('regions');
Route::get('/get-districts', [TanzaniaDataController::class, 'getDistricts'])->name('district');
Route::get('/get-wards', [TanzaniaDataController::class, 'getWards'])->name('getWards');
Route::middleware('auth')->group(function () {
    Route::patch('/teachers/{teacher}/toggle-status', [TeacherController::class, 'toggleStatus'])->name('teachers.toggle-status');

    Route::get('/notifications/mark-as-read', function () {
        if (auth()->check()) {
            auth()->user()->unreadNotifications->markAsRead();
        }
        return redirect()->back();
    })->name('mark.notifications');
    Route::get('students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::put('students/{student}', [StudentController::class, 'update'])->name('students.update');

    Route::get('/teacher/subjects', [TeacherController::class, 'teacherSubject'])->name('teacher.subject');

    Route::get('/teacher/assignment/edit/{id}', [TeacherController::class, 'editAssigment'])->name('teacher.assignment.edit');
    Route::put('/teacher/stream/edit/{id}', [StreamController::class, 'editStreamTeacher'])->name('teacher.stream.edit');
    Route::put('streamSubjects/update/{id}', [StreamSubjectController::class, 'update'])->name('streamSubjects.update');
    Route::get('streamSubjects/edit/{id}/{classId}', [StreamSubjectController::class, 'edit'])->name('streamSubjects.edit');
    Route::get('/attendance/create', [AttendencyController::class, 'create'])->name('attendance.create');

    // Download template rout
    Route::get('/marks/upload', [MarksController::class, 'showUploadForm'])->name('marks.upload.form');
    Route::get('/marks/class/upload', [MarksController::class, 'showClassUploadForm'])->name('marks.class.upload.form');
    Route::get('/marks/setup', [MarksUploadController::class, 'index'])->name('marks.upload.setup');
    Route::post('/marks/setup', [MarksUploadController::class, 'store'])->name('marks.upload.store');
    Route::get('/marks/{studentId}/download', [MarksController::class, 'downloadReport'])->name('marks.download');
    Route::get('/marks/download-template', [MarksController::class, 'downloadTemplate'])->name('marks.download.template');
    Route::get('/marks', [MarksController::class, 'index'])->name('marks.index');
    Route::post('/marks/upload', [MarksController::class, 'upload'])->name('marks.upload');
    Route::get('/marks/class/reset', [MarksController::class, 'resetMarksUpload'])->name('marks.upload.reset');
    
    // 
    Route::resource('homeworks', HomeworkController::class);
    Route::resource('assignments', AssingmentController::class);
    Route::get('/students/{id}', [StudentController::class, 'show'])->name('students.show');


    Route::resource('grades', GradeController::class);

    Route::get('/grade/update/{grade}', [GradeController::class, 'editGrade'])->name('grade.edit');
    Route::put('/grade/update/{grade}', [GradeController::class, 'UpdateGrade'])->name('grade.update');
    Route::post('/combination/add', [SubjectController::class, 'addCombination'])->name('combination.add');

    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/student/create', [StudentController::class, 'create'])->name('students.create');
    Route::post('/student/assign', [StudentController::class, 'assignCombination'])->name('students.assign.combination');
    Route::post('/students/store', [StudentController::class, 'store'])->name('students.store');
    Route::post('/students/import', [StudentController::class, 'import'])->name('students.import');
    Route::post('/teacher/search', [TeacherController::class, 'searchTeacher'])->name('teacher.search');
    Route::post('/students/upload', [StudentController::class, 'upload'])->name('students.upload');
    Route::delete('/student/{student}', [StudentController::class, 'destroy'])->name('student.destroy');
    Route::get('/student/download/sample', [StudentController::class, 'downloadStudentSample'])->name('students.download.sample');
    Route::prefix('attendance')->group(function () {
    Route::get('/', [AttendencyController::class, 'dailyIndex'])->name('attendance.daily.index'); // Daily attendance form
    Route::post('/', [AttendencyController::class, 'dailyStore'])->name('attendance.daily.store'); // Store daily attendance
    Route::get('/weekly', [AttendencyController::class, 'weeklyIndex'])->name('attendance.weekly.index'); // Weekly attendance report form
    Route::get('/weekly/report', [AttendencyController::class, 'weeklyReport'])->name('attendance.weekly.report'); // Generate weekly report
    Route::get('/monthly/report', [AttendencyController::class, 'monthlyIndex'])->name('attendance.monthly.index'); // Generate weekly report
    Route::get('/monthly/report', [AttendencyController::class, 'monthlyIndex'])->name('attendance.monthly.index');
    Route::get('/student', [AttendencyController::class, 'dailyCalanderStudent'])->name('attendance.monthly.index.student');

        // Generate weekly report
    });
    Route::get('/attendance/monthly-report', [AttendencyController::class, 'showMonthlyReport'])->name('attendance.monthly.report');
    Route::get('/get-classes', [ClassController::class, 'getClasses'])->name('class.view');
    Route::get('/student/panel', [StudentController::class, 'panel'])->name('student.panel');

    Route::resource('announcements',  AnnouncementController::class);
    Route::resource('streams', StreamController::class);
    Route::resource('classes', ClassController::class);
    Route::resource('examinations', ExaminationResultController::class);

Route::get('/combinations/{id}/subjects', [SubjectController::class, 'getSubjects']);
Route::put('/combination/update', [SubjectController::class, 'updateCombination'])->name('combination.update');
Route::delete('combination/delete', [SubjectController::class, 'deleteCombination'])->name('combination.delete');

    Route::post('/timetable/store/{class_id}', [TimeTableController::class, 'store'])->name('timetable.store');
    Route::get('/timetable/{classId}/show', [TimeTableController::class, 'show'])->name('timetable.student.show');
    Route::delete('/timetable-reset/{classId}', [TimeTableController::class, 'reset'])->name('timetable.reset');

    Route::get('/student/{student}/results', [ExaminationResultController::class, 'studentResults'])->name('student.examination.results');
    Route::get('/student/{student}/teacher', [TeacherController::class, 'subjectTeacher'])->name('student.subject.teacher');
    Route::get('/student/{student}/assigments', [AssingmentController::class, 'studentAssignment'])->name('student.assignment');
    Route::get('/student/{student}/attendance', [AttendencyController::class, 'attandanceShow'])->name('student.attandancy.show');
    Route::post('subject/assigment', [TeacherController::class, 'assignSubjectTeacher'])->name('streamSubjects.store');
    Route::post('/announcements/toggle-status', [AnnouncementController::class, 'toggleStatus'])->name('announcements.toggleStatus');
    Route::get('/timetable/download-template/{classId}', [TimeTableController::class, 'downloadTemplate'])->name('timetable.downloadTemplate');
    Route::get('/marks/{studentId}/edit/{marksId}', [StudentMarkController::class, 'edit'])->name('marks.edit');
    Route::get('/marks/{studentId}/edit/{academicYearId}/{examTypeId}', [StudentMarkController::class, 'editClassMarks'])->name('marks.edit.class');
    Route::patch('/marks/{studentId}/update/{marksId}', [StudentMarkController::class, 'update'])->name('marks.update');
    Route::patch('marks', [StudentMarkController::class, 'updateClassMarks'])->name('marks.update.class');
    // Route::get('/marks/{studentId}/edit/{marksId}', [MarksController::class, 'edit'])->name('marks.edit');

    // routes/web.php
    Route::get('/user-manual', [UserController::class, 'showUserManual'])->middleware('auth')->name('user.manual'); // Protect with 'auth' middleware
    // teacher routes
    Route::get('/teacher/{id}/edit', [TeacherController::class, 'edit'])->name('teacher.update');
    Route::get('/teacher/{id}/update', [TeacherController::class, 'update']);
    Route::delete('/teachers/{teacher}/detach-stream-subject/{streamSubject}', [TeacherController::class, 'detachTeacherFromStreamSubject'])->name('teachers.detach.stream_subject');
    Route::get('/teacher/panel', [ClassteacherController::class, 'index'])->name('teacher.panel');
    Route::resource('teachers',  TeacherController::class);
    Route::get('/teachers/search', [TeacherController::class, 'search'])->name('teachers.search');
    Route::post('teachers/import', [TeacherController::class, 'import'])->name('teachers.import');
    Route::get('teachers/excel/sample', [TeacherController::class, 'downloadSample'])->name('excel.sample');
    Route::get('/user/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/teacher/dashboard', [TeacherController::class, 'index'])->name('welcome.head.teacher');
    Route::put('/profile/picture/update', action: [ProfileController::class, 'updateProfilePicture'])->name('profile.picture.update');
    Route::put('/profile/pone/update', action: [ProfileController::class, 'updateProfilePhone'])->name('profile.phone.update');
    Route::put('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.changePassword');
    Route::resource('academic-years', AcademicYearController::class);
});

Route::resource('subjects', SubjectController::class);

Route::group(['middleware' => 'auth'], function () {

    Route::get('student/export/{student}',  ReportDownladController::class)->name('report.export');
    Route::get('/parent/announcements', [AnnouncementController::class, 'parentAnnouncements'])->name('parent.announcements');


    Route::resource('subjects.resources', ResourceController::class)->names([
        'index'   => 'subjects.resources.index',
        'create'  => 'subjects.resources.create',
        'store'   => 'subjects.resources.store',
        'show'    => 'subjects.resources.show',
        'edit'    => 'subjects.resources.edit',
        'update'  => 'subjects.resources.update',
        'destroy' => 'subjects.resources.destroy',
    ])->parameters([
        'subjects' => 'subject'
    ]);
});

Route::get('/payment/subscribe', [PaymentController::class, 'showPaymentForm'])->name('payments.form');
Route::post('/payments/subscribe', [PaymentController::class, 'subscribe'])->name('payments.subscribe');
Route::resource('parents', ParentController::class);
Route::get('payment/parents/', [ParentController::class, 'payment'])->name('parent.payment');
Route::resource('payments', PaymentController::class);

Route::controller(FullCalenderController::class)->group(function () {
    Route::get('fullcalender', 'index')->name('school-calander');
    Route::post('fullcalenderAjax', 'ajax');
});

Route::get('/subscription/status', [PaymentController::class, 'showSubscriptionPage'])->name('subscription.status');
Route::get('/subscription/create', [PaymentController::class, 'showPaymentForm'])->name('subscription.create');
Route::get('/subscriptions', [PaymentController::class, 'viewSubscriptions'])->name('subscriptions.index');

Route::get('/paypal/payment-success', [PayPalController::class, 'paymentSuccess'])->name('paypal.payment.success');
Route::get('/paypal/payment-cancel', [PayPalController::class, 'paymentCancel'])->name('paypal.payment.cancel');

Route::get('/bright-future-schools', [SchoolCustomController::class, 'bfIndex']);
Route::get('/bright-future-schools/application', [SchoolCustomController::class, 'getApplicationForm']);
Route::post('/bright-future-schools/application', [SchoolCustomController::class, 'getApplicationForm']);
