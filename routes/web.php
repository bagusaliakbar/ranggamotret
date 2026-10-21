<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProofingController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\KategoriLayananController;
use App\Http\Controllers\Admin\UserController;

// 1. PUBLIC ROUTES
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/services', [PublicController::class, 'services'])->name('public.services');
Route::get('/services/{id}', [PublicController::class, 'serviceDetail'])->name('public.services.detail');
Route::get('/portfolio', [PublicController::class, 'portfolio'])->name('public.portfolio');
Route::get('/courses', [PublicController::class, 'courses'])->name('public.courses');
Route::get('/courses/{id}', [PublicController::class, 'courseDetail'])->name('public.courses.detail');
Route::get('/consultation', [PublicController::class, 'consultation'])->name('public.consultation');
Route::post('/consultation', [PublicController::class, 'storeConsultation'])->name('public.consultation.store');
Route::get('/faq', [PublicController::class, 'faq'])->name('public.faq');
Route::get('/verify-certificate/{code}', [PublicController::class, 'verifyCertificate'])->name('public.verify-certificate');

// 2. AUTHENTICATION ROUTES
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 3. AUTHENTICATED SHARED ROUTES
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::get('/calendar', [BookingController::class, 'calendar'])->name('calendar');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::get('/payments/finish', [PaymentController::class, 'paymentFinish'])->name('payments.finish');
    Route::get('/invoices/{id}', [PaymentController::class, 'showInvoice'])->name('invoices.show');
});

// Public Midtrans Webhook Callback
Route::post('/payments/midtrans-callback', [PaymentController::class, 'handleCallback'])->name('payments.midtrans-callback');

// 4. CUSTOMER / MEMBER ROUTES
Route::middleware(['auth', 'role:customer,peserta,admin'])->group(function () {
    Route::get('/customer/dashboard', [BookingController::class, 'customerDashboard'])->name('customer.dashboard');
    Route::get('/customer/bookings', [BookingController::class, 'customerIndex'])->name('customer.bookings.index');
    Route::get('/customer/bookings/create', [BookingController::class, 'create'])->name('customer.bookings.create');
    Route::post('/customer/bookings', [BookingController::class, 'store'])->name('customer.bookings.store');
    Route::get('/customer/bookings/{id}', [BookingController::class, 'customerShow'])->name('customer.bookings.show');
    Route::get('/customer/proofing/{bookingId}', [ProofingController::class, 'customerShow'])->name('customer.proofing.show');
    Route::post('/customer/proofing/{id}/toggle', [ProofingController::class, 'toggleSelect'])->name('customer.proofing.toggle');
    Route::post('/customer/testimonials', [TestimonialController::class, 'store'])->name('customer.testimonials.store');
});

// 5. PESERTA (STUDENT LMS) ROUTES
// 5. STUDENT / LEARNING LMS ROUTES
Route::middleware(['auth', 'role:customer,peserta,admin'])->group(function () {
    Route::get('/student/dashboard', [CourseController::class, 'studentIndex'])->name('student.dashboard');
    Route::get('/student/courses/{kursusId}/learn', [CourseController::class, 'learn'])->name('student.learn');
    Route::post('/student/materi/{materiId}/complete', [CourseController::class, 'markMaterialComplete'])->name('student.materi.complete');
    Route::post('/student/courses/{kursusId}/issue-certificate', [CourseController::class, 'issueCertificate'])->name('student.certificate.issue');
    Route::get('/student/certificates/{id}', [CourseController::class, 'showCertificate'])->name('student.certificate.show');
    
    Route::post('/student/tugas/{tugasId}/submit', [AssignmentController::class, 'submit'])->name('student.tugas.submit');
    Route::get('/student/quiz/{quizId}', [QuizController::class, 'show'])->name('student.quiz.show');
    Route::post('/student/quiz/{quizId}/submit', [QuizController::class, 'submit'])->name('student.quiz.submit');
});

// 6. INSTRUKTUR ROUTES
Route::middleware(['auth', 'role:instruktur,admin'])->group(function () {
    Route::get('/instructor/dashboard', [CourseController::class, 'adminIndex'])->name('instructor.dashboard');
    Route::get('/instructor/assignments', [AssignmentController::class, 'instructorIndex'])->name('instructor.assignments.index');
    Route::post('/instructor/assignments/{id}/grade', [AssignmentController::class, 'grade'])->name('instructor.assignments.grade');
});

// 7. ADMIN ROUTES
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Layanan & Paket Management Routes
    Route::get('/layanan', [LayananController::class, 'index'])->name('layanan.index');
    Route::post('/layanan', [LayananController::class, 'store'])->name('layanan.store');
    Route::put('/layanan/{id}', [LayananController::class, 'update'])->name('layanan.update');
    Route::delete('/layanan/{id}', [LayananController::class, 'destroy'])->name('layanan.destroy');
    Route::post('/layanan/{id}/toggle-status', [LayananController::class, 'toggleStatus'])->name('layanan.toggle-status');
    
    Route::post('/layanan/{layananId}/paket', [LayananController::class, 'storePaket'])->name('layanan.paket.store');
    Route::put('/paket/{id}', [LayananController::class, 'updatePaket'])->name('layanan.paket.update');
    Route::delete('/paket/{id}', [LayananController::class, 'destroyPaket'])->name('layanan.paket.destroy');
    Route::post('/paket/{id}/toggle-status', [LayananController::class, 'togglePaketStatus'])->name('layanan.paket.toggle-status');

    // Kategori Layanan Management Routes
    Route::get('/kategori-layanan', [KategoriLayananController::class, 'index'])->name('kategori-layanan.index');
    Route::post('/kategori-layanan', [KategoriLayananController::class, 'store'])->name('kategori-layanan.store');
    Route::put('/kategori-layanan/{id}', [KategoriLayananController::class, 'update'])->name('kategori-layanan.update');
    Route::delete('/kategori-layanan/{id}', [KategoriLayananController::class, 'destroy'])->name('kategori-layanan.destroy');
    Route::post('/kategori-layanan/{id}/toggle-status', [KategoriLayananController::class, 'toggleStatus'])->name('kategori-layanan.toggle-status');

    Route::get('/bookings', [BookingController::class, 'adminIndex'])->name('bookings.index');
    Route::get('/bookings/{id}', [BookingController::class, 'adminShow'])->name('bookings.show');
    Route::post('/bookings/{id}/status', [BookingController::class, 'adminUpdateStatus'])->name('bookings.update-status');
    
    Route::post('/proofing/{bookingId}/upload', [ProofingController::class, 'adminUpload'])->name('proofing.upload');
    Route::post('/proofing/{id}/delete', [ProofingController::class, 'adminDeletePhoto'])->name('proofing.delete');
    
    Route::get('/payments', [PaymentController::class, 'adminIndex'])->name('payments.index');
    Route::post('/payments/{id}/verify', [PaymentController::class, 'adminVerify'])->name('payments.verify');
    
    // Course & LMS Management Routes
    Route::get('/courses', [CourseController::class, 'adminIndex'])->name('courses.index');
    Route::post('/courses', [CourseController::class, 'storeCourse'])->name('courses.store');
    Route::put('/courses/{id}', [CourseController::class, 'updateCourse'])->name('courses.update');
    Route::delete('/courses/{id}', [CourseController::class, 'destroyCourse'])->name('courses.destroy');
    Route::post('/courses/{id}/toggle-status', [CourseController::class, 'toggleCourseStatus'])->name('courses.toggle-status');
    
    Route::post('/courses/{kursusId}/modules', [CourseController::class, 'storeModule'])->name('courses.modules.store');
    Route::put('/modules/{id}', [CourseController::class, 'updateModule'])->name('courses.modules.update');
    Route::delete('/modules/{id}', [CourseController::class, 'destroyModule'])->name('courses.modules.destroy');
    
    Route::post('/modules/{modulId}/materis', [CourseController::class, 'storeMateri'])->name('modules.materis.store');
    Route::put('/materis/{id}', [CourseController::class, 'updateMateri'])->name('modules.materis.update');
    Route::delete('/materis/{id}', [CourseController::class, 'destroyMateri'])->name('modules.materis.destroy');
    
    Route::get('/consultations', [ConsultationController::class, 'adminIndex'])->name('consultations.index');
    Route::post('/consultations/{id}/reply', [ConsultationController::class, 'adminReply'])->name('consultations.reply');
    
    Route::get('/testimonials', [TestimonialController::class, 'adminIndex'])->name('testimonials.index');
    Route::post('/testimonials/{id}/status', [TestimonialController::class, 'adminUpdateStatus'])->name('testimonials.update-status');
    
    // User Management Routes
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});
