<?php

use Illuminate\Support\Facades\Route;

// Controllers Selaras Robustness Diagram
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\KursusController;
use App\Http\Controllers\MateriController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\ReservasiController;
use App\Http\Controllers\KonsultasiController;
use App\Http\Controllers\PortofolioController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\LaporanController;

// Specialized Utility Controllers
use App\Http\Controllers\PublicController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes — RanggaMotret.id
| Arsitektur MVC Selaras 100% Robustness Diagram & Unified Dashboard
|--------------------------------------------------------------------------
*/

// ==========================================
// 1. PUBLIC ROUTES (Katalog, Kursus, Portofolio, Konsultasi)
// ==========================================
Route::get('/', [PublicController::class, 'home'])->name('home');

// Boundary: Katalog Layanan & Paket
Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog.index');
Route::get('/katalog/{id}', [KatalogController::class, 'detail'])->name('katalog.detail');
Route::get('/services', [KatalogController::class, 'index'])->name('public.services');
Route::get('/services/{id}', [KatalogController::class, 'detail'])->name('public.services.detail');

// Boundary: Portofolio Foto
Route::get('/portofolio', [PortofolioController::class, 'index'])->name('portofolio.index');
Route::get('/portfolio', [PortofolioController::class, 'index'])->name('public.portfolio');

// Boundary: Kursus Akademi
Route::get('/kursus', [KursusController::class, 'index'])->name('kursus.index');
Route::get('/kursus/{id}', [KursusController::class, 'detail'])->name('kursus.detail');
Route::get('/courses', [KursusController::class, 'index'])->name('public.courses');
Route::get('/courses/{id}', [KursusController::class, 'detail'])->name('public.courses.detail');

// Boundary: Konsultasi Fotografer
Route::get('/konsultasi', [KonsultasiController::class, 'index'])->name('konsultasi.index');
Route::post('/konsultasi', [KonsultasiController::class, 'store'])->name('konsultasi.store');
Route::get('/consultation', [KonsultasiController::class, 'index'])->name('public.consultation');
Route::post('/consultation', [KonsultasiController::class, 'store'])->name('public.consultation.store');

Route::get('/verify-certificate/{code?}', [PublicController::class, 'verifyCertificate'])->name('public.verify-certificate');
Route::get('/certificate/verify/{code?}', [PublicController::class, 'verifyCertificate'])->name('public.certificate.verify');

// ==========================================
// 2. AUTHENTICATION ROUTES (Boundary: Login, Registrasi)
// ==========================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================================
// 3. AUTHENTICATED SHARED ROUTES (Dashboard, Profile, Jadwal, Pembayaran)
// ==========================================
Route::middleware(['auth'])->group(function () {
    // Boundary: Unified Adaptive Dashboard (Satu Pintu untuk Semua Role)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Backward-compatible alias dashboard routes
    Route::get('/customer/dashboard', [DashboardController::class, 'index'])->name('customer.dashboard');
    Route::get('/instructor/dashboard', [DashboardController::class, 'index'])->name('instructor.dashboard');

    // Boundary: Profile Pengguna
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Boundary: Jadwal & Kalender Pemotretan
    Route::get('/jadwal', [JadwalController::class, 'index'])->name('jadwal.index');
    Route::get('/calendar', [JadwalController::class, 'index'])->name('calendar');
    Route::put('/jadwal/{id}', [JadwalController::class, 'updateSchedule'])->name('jadwal.update');

    // Notifikasi
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

    // Boundary: Pembayaran & Invoice
    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pembayaran.index');
    Route::get('/pembayaran/create', [PembayaranController::class, 'create'])->name('pembayaran.create');
    Route::post('/pembayaran', [PembayaranController::class, 'store'])->name('pembayaran.store');
    Route::get('/pembayaran/finish', [PembayaranController::class, 'paymentFinish'])->name('pembayaran.finish');
    Route::get('/payments', [PembayaranController::class, 'index'])->name('payments.index');
    Route::get('/payments/create', [PembayaranController::class, 'create'])->name('payments.create');
    Route::post('/payments', [PembayaranController::class, 'store'])->name('payments.store');
    Route::get('/payments/finish', [PembayaranController::class, 'paymentFinish'])->name('payments.finish');
    Route::get('/payments/{id}/sync', [PembayaranController::class, 'syncStatus'])->name('payments.sync');
    Route::get('/invoices/{id}', [PembayaranController::class, 'showInvoice'])->name('invoices.show');
});

// Midtrans Webhook Callback
Route::post('/payments/midtrans-callback', [PembayaranController::class, 'handleCallback'])->name('payments.midtrans-callback');

// ==========================================
// 4. CUSTOMER ROUTES (Boundary: Reservasi Foto & Proofing)
// ==========================================
Route::middleware(['auth', 'role:customer,peserta,admin'])->group(function () {
    // Menu Layanan Fotografi yang Telah Dipesan
    Route::get('/customer/fotografi', [ReservasiController::class, 'customerFotografi'])->name('customer.fotografi.index');
    Route::get('/fotografi-saya', [ReservasiController::class, 'customerFotografi'])->name('fotografi.saya');

    Route::get('/customer/bookings', [ReservasiController::class, 'customerIndex'])->name('customer.bookings.index');
    Route::get('/customer/bookings/create', [ReservasiController::class, 'create'])->name('customer.bookings.create');
    Route::post('/customer/bookings', [ReservasiController::class, 'store'])->name('customer.bookings.store');
    Route::get('/customer/bookings/{id}', [ReservasiController::class, 'customerShow'])->name('customer.bookings.show');

    // Canonical Reservasi Routes
    Route::get('/reservasi', [ReservasiController::class, 'customerIndex'])->name('reservasi.index');
    Route::get('/reservasi/create', [ReservasiController::class, 'create'])->name('reservasi.create');
    Route::post('/reservasi', [ReservasiController::class, 'store'])->name('reservasi.store');
    Route::get('/reservasi/check-jadwal', [ReservasiController::class, 'checkJadwal'])->name('reservasi.check-jadwal');
    Route::get('/reservasi/{id}', [ReservasiController::class, 'customerShow'])->name('reservasi.show');

    // Proofing Studio & Testimoni
    Route::get('/customer/proofing/{bookingId}', [ReservasiController::class, 'customerProofing'])->name('customer.proofing.show');
    Route::post('/customer/proofing/{id}/toggle', [ReservasiController::class, 'toggleProofingSelection'])->name('customer.proofing.toggle');
    Route::post('/customer/proofing/{bookingId}/submit', [ReservasiController::class, 'submitFinalProofing'])->name('customer.proofing.submit');
    Route::post('/customer/testimonials', [ReservasiController::class, 'storeTestimonial'])->name('customer.testimonials.store');

    // Riwayat Konsultasi Customer (Portal Dashboard)
    Route::get('/customer/consultations', [KonsultasiController::class, 'customerIndex'])->name('customer.consultations.index');
    Route::get('/riwayat-konsultasi', [KonsultasiController::class, 'customerIndex'])->name('konsultasi.riwayat');
});

// ==========================================
// 5. STUDENT LMS ROUTES (Boundary: Materi & Kursus Siswa)
// ==========================================
Route::middleware(['auth', 'role:customer,peserta,admin'])->group(function () {
    // Ruang Kursus & Pembelajaran Siswa (Tampilan Khusus Kursus Pelanggan)
    Route::get('/student/dashboard', [KursusController::class, 'studentIndex'])->name('student.dashboard');
    Route::get('/student/courses', [KursusController::class, 'studentIndex'])->name('student.courses');
    Route::get('/customer/courses', [KursusController::class, 'studentIndex'])->name('customer.courses.index');

    // Riwayat Reservasi Kursus (Boundary ICONIX: reservasi_kursus)
    Route::get('/customer/reservasi-kursus', [KursusController::class, 'customerCourseReservations'])->name('customer.courses.reservations');
    Route::get('/reservasi-kursus', [KursusController::class, 'customerCourseReservations'])->name('reservasi.kursus.index');

    Route::get('/student/courses/{kursusId}/learn', [KursusController::class, 'learn'])->name('student.learn');
    Route::post('/student/materi/{materiId}/complete', [MateriController::class, 'complete'])->name('student.materi.complete');
    Route::post('/student/courses/{kursusId}/issue-certificate', [KursusController::class, 'issueCertificate'])->name('student.certificate.issue');
    Route::get('/student/certificates/{id}', [KursusController::class, 'showCertificate'])->name('student.certificate.show');

    Route::post('/student/tugas/{tugasId}/submit', [MateriController::class, 'submitTugas'])->name('student.tugas.submit');
    Route::get('/student/quiz/{quizId}', [MateriController::class, 'showQuiz'])->name('student.quiz.show');
    Route::post('/student/quiz/{quizId}/submit', [MateriController::class, 'submitQuiz'])->name('student.quiz.submit');
});

// ==========================================
// 6. OWNER EVALUATION ROUTES (Penilaian Tugas Siswa oleh Owner)
// ==========================================
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/instructor/assignments', [MateriController::class, 'instructorAssignmentsIndex'])->name('instructor.assignments.index');
    Route::post('/instructor/assignments/{id}/grade', [MateriController::class, 'gradeAssignment'])->name('instructor.assignments.grade');
});

// ==========================================
// 7. ADMIN ROUTES (Boundary: Laporan, Kelola Booking, Layanan, Modul)
// ==========================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Manajemen Katalog Layanan & Paket
    Route::get('/layanan', [KatalogController::class, 'adminIndex'])->name('layanan.index');
    Route::post('/layanan', [KatalogController::class, 'store'])->name('layanan.store');
    Route::put('/layanan/{id}', [KatalogController::class, 'update'])->name('layanan.update');
    Route::delete('/layanan/{id}', [KatalogController::class, 'destroy'])->name('layanan.destroy');
    Route::post('/layanan/{id}/toggle-status', [KatalogController::class, 'toggleStatus'])->name('layanan.toggle-status');

    Route::post('/layanan/{layananId}/paket', [KatalogController::class, 'storePaket'])->name('layanan.paket.store');
    Route::put('/paket/{id}', [KatalogController::class, 'updatePaket'])->name('layanan.paket.update');
    Route::delete('/paket/{id}', [KatalogController::class, 'destroyPaket'])->name('layanan.paket.destroy');
    Route::post('/paket/{id}/toggle-status', [KatalogController::class, 'togglePaketStatus'])->name('layanan.paket.toggle-status');

    // Kategori Layanan
    Route::get('/kategori-layanan', [KatalogController::class, 'kategoriIndex'])->name('kategori-layanan.index');
    Route::post('/kategori-layanan', [KatalogController::class, 'kategoriStore'])->name('kategori-layanan.store');
    Route::put('/kategori-layanan/{id}', [KatalogController::class, 'kategoriUpdate'])->name('kategori-layanan.update');
    Route::delete('/kategori-layanan/{id}', [KatalogController::class, 'kategoriDestroy'])->name('kategori-layanan.destroy');
    Route::post('/kategori-layanan/{id}/toggle-status', [KatalogController::class, 'kategoriToggleStatus'])->name('kategori-layanan.toggle-status');

    // Kelola Reservasi / Booking
    Route::get('/bookings', [ReservasiController::class, 'adminIndex'])->name('bookings.index');
    Route::get('/bookings/{id}', [ReservasiController::class, 'adminShow'])->name('bookings.show');
    Route::post('/bookings/{id}/status', [ReservasiController::class, 'adminUpdateStatus'])->name('bookings.update-status');

    // Proofing Upload & Moderasi
    Route::post('/proofing/{bookingId}/upload', [ReservasiController::class, 'adminUploadProofing'])->name('proofing.upload');
    Route::post('/proofing/{id}/delete', [ReservasiController::class, 'adminDeleteProofingPhoto'])->name('proofing.delete');

    // Verifikasi Pembayaran
    Route::get('/payments', [PembayaranController::class, 'adminIndex'])->name('payments.index');
    Route::post('/payments/{id}/verify', [PembayaranController::class, 'adminVerify'])->name('payments.verify');

    // Kursus & Modul LMS
    Route::get('/courses', [KursusController::class, 'adminIndex'])->name('courses.index');
    Route::get('/courses/reservations', [KursusController::class, 'adminReservations'])->name('courses.reservations.index');
    Route::post('/courses/reservations/{id}/status', [KursusController::class, 'adminUpdateReservationStatus'])->name('courses.reservations.update-status');
    Route::post('/courses', [KursusController::class, 'storeCourse'])->name('courses.store');
    Route::put('/courses/{id}', [KursusController::class, 'updateCourse'])->name('courses.update');
    Route::delete('/courses/{id}', [KursusController::class, 'destroyCourse'])->name('courses.destroy');
    Route::post('/courses/{id}/toggle-status', [KursusController::class, 'toggleCourseStatus'])->name('courses.toggle-status');

    Route::post('/courses/{kursusId}/modules', [KursusController::class, 'storeModule'])->name('courses.modules.store');
    Route::put('/modules/{id}', [KursusController::class, 'updateModule'])->name('courses.modules.update');
    Route::delete('/modules/{id}', [KursusController::class, 'destroyModule'])->name('courses.modules.destroy');

    Route::post('/modules/{modulId}/materis', [KursusController::class, 'storeMateri'])->name('modules.materis.store');
    Route::put('/materis/{id}', [KursusController::class, 'updateMateri'])->name('modules.materis.update');
    Route::delete('/materis/{id}', [KursusController::class, 'destroyMateri'])->name('modules.materis.destroy');

    Route::post('/modules/{modulId}/quizzes', [KursusController::class, 'storeQuiz'])->name('modules.quizzes.store');
    Route::delete('/quizzes/{id}', [KursusController::class, 'destroyQuiz'])->name('quizzes.destroy');

    // Konsultasi & Testimoni
    Route::get('/consultations', [KonsultasiController::class, 'adminIndex'])->name('consultations.index');
    Route::post('/consultations/{id}/reply', [KonsultasiController::class, 'adminReply'])->name('consultations.reply');

    Route::get('/testimonials', [ReservasiController::class, 'adminTestimonialsIndex'])->name('testimonials.index');
    Route::post('/testimonials/{id}/status', [ReservasiController::class, 'adminUpdateTestimonialStatus'])->name('testimonials.update-status');

    // Kelola Portofolio (Owner / Admin)
    Route::get('/portofolio', [PortofolioController::class, 'adminIndex'])->name('portofolio.index');
    Route::post('/portofolio', [PortofolioController::class, 'store'])->name('portofolio.store');
    Route::put('/portofolio/{id}', [PortofolioController::class, 'update'])->name('portofolio.update');
    Route::delete('/portofolio/{id}', [PortofolioController::class, 'destroy'])->name('portofolio.destroy');
    Route::post('/portofolio/{id}/toggle-status', [PortofolioController::class, 'toggleStatus'])->name('portofolio.toggle-status');

    // Manajemen Pengguna
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

    // Boundary: Laporan Keuangan & Operasional
    Route::get('/reports', [LaporanController::class, 'index'])->name('reports.index');
    Route::get('/reports/pendapatan', [LaporanController::class, 'pendapatan'])->name('reports.pendapatan');
    Route::get('/reports/pendapatan/pdf', [LaporanController::class, 'pendapatanPdf'])->name('reports.pendapatan.pdf');
    Route::get('/reports/pendapatan/excel', [LaporanController::class, 'pendapatanExcel'])->name('reports.pendapatan.excel');

    Route::get('/reports/reservasi', [LaporanController::class, 'reservasi'])->name('reports.reservasi');
    Route::get('/reports/reservasi/pdf', [LaporanController::class, 'reservasiPdf'])->name('reports.reservasi.pdf');
    Route::get('/reports/reservasi/excel', [LaporanController::class, 'reservasiExcel'])->name('reports.reservasi.excel');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
});
