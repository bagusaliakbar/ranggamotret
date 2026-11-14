<?php

use App\Models\Customer;
use App\Models\GaleriFoto;
use App\Models\Invoice;
use App\Models\Jadwal;
use App\Models\Katalog;
use App\Models\KategoriLayanan;
use App\Models\Konsultasi;
use App\Models\Kursus;
use App\Models\Materi;
use App\Models\MateriProgress;
use App\Models\Modul;
use App\Models\Notifikasi;
use App\Models\Paket;
use App\Models\Pembayaran;
use App\Models\Portofolio;
use App\Models\PortofolioFoto;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizSoal;
use App\Models\Reservasi;
use App\Models\Sertifikat;
use App\Models\User;
use App\Services\MidtransService;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

/**
 * Helper pembuat akun Owner dan Pelanggan uji agar ID referensi notifikasi valid.
 */
function createSrsUser(string $role = 'pelanggan', string $status = 'active', ?string $email = null): User
{
    static $seq = 1;
    $seq++;

    return User::create([
        'name' => $role === 'owner' ? 'Owner Rangga Uji ' . $seq : 'Pelanggan Uji ' . $seq,
        'email' => $email ?? "{$role}.srs.{$seq}@example.com",
        'password' => Hash::make('password123'),
        'role' => $role,
        'status' => $status,
        'phone' => '08123456789' . ($seq % 10),
        'address' => 'Jl. Fotografi No. ' . $seq,
    ]);
}

/**
 * Helper pembuat katalog layanan + paket aktif.
 *
 * @return array{kategori: KategoriLayanan, layanan: Katalog, paket: Paket}
 */
function createActiveServiceAndPackage(int $harga = 1500000, string $durasi = '2 Jam', string $status = 'aktif'): array
{
    $kategori = KategoriLayanan::firstOrCreate(
        ['nama' => 'Wedding'],
        ['slug' => 'wedding', 'icon' => 'fa-ring', 'status' => 'aktif']
    );

    $layanan = Katalog::create([
        'nama' => 'Wedding Documentary ' . uniqid(),
        'kategori_layanan_id' => $kategori->id,
        'kategori' => $kategori->nama,
        'deskripsi' => 'Dokumentasi sesi fotografi profesional.',
        'harga_mulai' => $harga,
        'thumbnail' => 'https://images.unsplash.com/photo-1519741497674-611481863552',
        'status' => $status,
    ]);

    $paket = Paket::create([
        'layanan_id' => $layanan->id,
        'nama_paket' => 'Gold Signature',
        'harga' => $harga,
        'durasi' => $durasi,
        'jumlah_fotografer' => 2,
        'jumlah_foto' => 100,
        'fasilitas' => '100 Foto Edit + Album',
        'status' => $status,
    ]);

    return compact('kategori', 'layanan', 'paket');
}

/**
 * Helper pembuat kursus beserta modul, materi, dan kuis.
 *
 * @return array{kursus: Kursus, modul: Modul, materi: Materi, quiz: Quiz, soal: QuizSoal}
 */
function createCourseWithSyllabus(User $owner, string $status = 'aktif'): array
{
    $kursus = Kursus::create([
        'nama' => 'Teknik Fotografi Dasar ' . uniqid(),
        'deskripsi' => 'Kursus fotografi fundamental.',
        'harga' => 250000,
        'durasi' => 'Akses Seumur Hidup',
        'level' => 'Pemula',
        'instruktur_id' => $owner->id,
        'thumbnail' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32',
        'status' => $status,
    ]);

    $modul = Modul::create([
        'kursus_id' => $kursus->id,
        'judul' => 'Modul 1: Segitiga Exposure',
        'urutan' => 1,
        'deskripsi' => 'Aperture, Shutter Speed, ISO.',
    ]);

    $materi = Materi::create([
        'modul_id' => $modul->id,
        'judul' => 'Pengenalan Aperture',
        'tipe' => 'teks',
        'isi' => 'Aperture mengatur bukaan diafragma lensa.',
        'urutan' => 1,
    ]);

    $quiz = Quiz::create([
        'modul_id' => $modul->id,
        'judul' => 'Kuis Evaluasi Exposure',
        'durasi_menit' => 15,
        'passing_grade' => 70,
    ]);

    $soal = QuizSoal::create([
        'quiz_id' => $quiz->id,
        'pertanyaan' => 'Komponen yang mengatur sensitivitas sensor terhadap cahaya adalah?',
        'pilihan_a' => 'ISO',
        'pilihan_b' => 'White Balance',
        'pilihan_c' => 'Focal Length',
        'pilihan_d' => 'Aspect Ratio',
        'jawaban_benar' => 'A',
        'bobot_nilai' => 100,
        'pembahasan' => 'ISO mengatur sensitivitas sensor.',
    ]);

    return compact('kursus', 'modul', 'materi', 'quiz', 'soal');
}

// =========================================================================
// MODUL 1: AUTENTIKASI & PROFIL (SRS-F-01 s.d. SRS-F-04)
// =========================================================================

test('SRS-F-01 TC-F-01.1..3: registrasi valid berhasil, email duplikat & password tidak valid ditolak', function () {
    // TC-F-01.1: Registrasi valid
    $this->post('/register', [
        'name' => 'Andi Pratama',
        'email' => 'andi.register@example.com',
        'phone' => '081234500001',
        'address' => 'Jakarta Selatan',
        'password' => 'rahasia123',
        'password_confirmation' => 'rahasia123',
    ])->assertRedirect('/dashboard');

    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'email' => 'andi.register@example.com',
        'role' => 'pelanggan',
        'status' => 'active',
    ]);
    $this->assertDatabaseHas('customers', ['phone' => '081234500001']);

    $this->post('/logout');

    // TC-F-01.2: Email duplikat ditolak
    $this->from('/register')->post('/register', [
        'name' => 'Andi Duplikat',
        'email' => 'andi.register@example.com',
        'password' => 'rahasia123',
        'password_confirmation' => 'rahasia123',
    ])->assertRedirect('/register')->assertSessionHasErrors('email');

    // TC-F-01.3: Password < 8 karakter atau konfirmasi beda ditolak
    $this->from('/register')->post('/register', [
        'name' => 'Pendek',
        'email' => 'pendek@example.com',
        'password' => '12345',
        'password_confirmation' => '54321',
    ])->assertRedirect('/register')->assertSessionHasErrors('password');

    $this->assertDatabaseMissing('users', ['email' => 'pendek@example.com']);
});

test('SRS-F-02 & SRS-F-03 TC-F-02.1..3 & TC-F-03.1: login valid/gagal/nonaktif dan logout', function () {
    $owner = createSrsUser('owner', 'active', 'owner.login@example.com');
    $pelanggan = createSrsUser('pelanggan', 'active', 'client.login@example.com');
    $inactive = createSrsUser('pelanggan', 'inactive', 'inactive.login@example.com');

    // TC-F-02.1: Login pelanggan valid
    $this->post('/login', [
        'email' => $pelanggan->email,
        'password' => 'password123',
    ])->assertRedirect('/dashboard');
    $this->actingAs($pelanggan)->get('/dashboard')->assertStatus(200);

    // TC-F-03.1: Logout mengakhiri sesi
    $this->post('/logout')->assertRedirect('/');
    $this->assertGuest();
    $this->get('/dashboard')->assertRedirect('/login');

    // TC-F-02.1: Login owner valid & akses dashboard owner
    $this->post('/login', [
        'email' => $owner->email,
        'password' => 'password123',
    ])->assertRedirect('/dashboard');
    $this->get('/dashboard')->assertStatus(200);
    $this->post('/logout');

    // TC-F-02.2: Password salah ditolak
    $this->from('/login')->post('/login', [
        'email' => $pelanggan->email,
        'password' => 'salah-password',
    ])->assertRedirect('/login')->assertSessionHasErrors('email');
    $this->assertGuest();

    // TC-F-02.3: Akun nonaktif ditolak login & ditolak RoleMiddleware
    $this->from('/login')->post('/login', [
        'email' => $inactive->email,
        'password' => 'password123',
    ])->assertSessionHasErrors('email');
    $this->assertGuest();

    $this->actingAs($inactive)->get('/reservasi')->assertRedirect('/login');
});

test('SRS-F-04 TC-F-04.1..3: lihat profil, ubah data & sinkron Customer, serta ganti password', function () {
    $pelanggan = createSrsUser('pelanggan');

    // TC-F-04.1: Lihat profil
    $this->actingAs($pelanggan)->get('/profile')
        ->assertStatus(200)
        ->assertSee($pelanggan->name)
        ->assertSee($pelanggan->email);

    // TC-F-04.2: Ubah profil tanpa ubah password
    $this->actingAs($pelanggan)->post('/profile', [
        'name' => 'Nama Baru Klien',
        'phone' => '085566778899',
        'address' => 'Bandung Utara',
    ])->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'id' => $pelanggan->id,
        'name' => 'Nama Baru Klien',
        'phone' => '085566778899',
        'address' => 'Bandung Utara',
    ]);
    $this->assertDatabaseHas('customers', [
        'user_id' => $pelanggan->id,
        'phone' => '085566778899',
    ]);

    // TC-F-04.3: Ubah password dan uji login dengan password baru
    $this->actingAs($pelanggan)->post('/profile', [
        'name' => 'Nama Baru Klien',
        'phone' => '085566778899',
        'address' => 'Bandung Utara',
        'password' => 'passwordbaru99',
        'password_confirmation' => 'passwordbaru99',
    ])->assertSessionHas('success');

    $this->post('/logout');
    $this->post('/login', [
        'email' => $pelanggan->email,
        'password' => 'passwordbaru99',
    ])->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($pelanggan);
});

// =========================================================================
// MODUL 2: KATALOG LAYANAN & KURSUS (SRS-F-05 s.d. SRS-F-06)
// =========================================================================

test('SRS-F-05 TC-F-05.1..6: katalog publik, RBAC admin layanan, CRUD layanan, paket & proteksi hapus kategori', function () {
    $owner = createSrsUser('owner');
    $pelanggan = createSrsUser('pelanggan');

    // TC-F-05.2: Pelanggan dilarang mengakses admin layanan (HTTP 403)
    $this->actingAs($pelanggan)->get('/admin/layanan')->assertStatus(403);

    // TC-F-05.3: Owner tambah layanan baru beserta kategori custom & upload gambar
    $fakeThumb = UploadedFile::fake()->image('katalog-test.jpg', 600, 400);
    $this->actingAs($owner)->post('/admin/layanan', [
        'nama' => 'Editorial Studio Portrait',
        'kategori' => 'custom',
        'kategori_custom' => 'Editorial Studio',
        'deskripsi' => 'Sesi studio portrait eksklusif.',
        'harga_mulai' => 1200000,
        'thumbnail_file' => $fakeThumb,
        'status' => 'aktif',
    ])->assertRedirect('/admin/layanan')->assertSessionHas('success');

    $layanan = Katalog::where('nama', 'Editorial Studio Portrait')->first();
    expect($layanan)->not->toBeNull();
    $kategori = KategoriLayanan::where('nama', 'Editorial Studio')->first();
    expect($kategori)->not->toBeNull();

    // TC-F-05.4: Tambah paket lebih murah otomatis memperbarui harga_mulai layanan
    $this->actingAs($owner)->post("/admin/layanan/{$layanan->id}/paket", [
        'nama_paket' => 'Paket Hemat Studio',
        'harga' => 750000,
        'durasi' => '2 Jam',
        'jumlah_fotografer' => 1,
        'jumlah_foto' => 40,
        'fasilitas' => '40 Foto Edit',
        'status' => 'aktif',
    ])->assertRedirect('/admin/layanan')->assertSessionHas('success');

    $layanan->refresh();
    expect((float) $layanan->harga_mulai)->toBe(750000.0);
    $paket = Paket::where('layanan_id', $layanan->id)->first();

    // TC-F-05.1: Halaman katalog publik menampilkan layanan aktif & filter kategori
    $this->get('/katalog?kategori=Editorial+Studio')
        ->assertStatus(200)
        ->assertSee('Editorial Studio Portrait');
    $this->get("/katalog/{$layanan->id}")
        ->assertStatus(200)
        ->assertSee('Paket Hemat Studio');

    // TC-F-05.5: Ubah layanan, paket & toggle status menjadi nonaktif menyembunyikan dari publik
    $this->actingAs($owner)->put("/admin/layanan/{$layanan->id}", [
        'nama' => 'Editorial Studio Portrait Revised',
        'kategori' => 'Editorial Studio',
        'deskripsi' => 'Sesi studio portrait eksklusif revisi.',
        'harga_mulai' => 750000,
        'status' => 'aktif',
    ])->assertSessionHas('success');

    $this->actingAs($owner)->put("/admin/paket/{$paket->id}", [
        'nama_paket' => 'Paket Hemat Rebranded',
        'harga' => 800000,
        'durasi' => '2 Jam',
        'jumlah_fotografer' => 1,
        'jumlah_foto' => 50,
        'status' => 'aktif',
    ])->assertSessionHas('success');

    $this->actingAs($owner)->post("/admin/layanan/{$layanan->id}/toggle-status")
        ->assertSessionHas('success');

    $layanan->refresh();
    expect($layanan->status)->toBe('nonaktif');
    $this->post('/logout');
    $this->get('/katalog')->assertStatus(200)->assertDontSee('Editorial Studio Portrait Revised');

    // TC-F-05.6: Kategori yang masih dipakai layanan ditolak saat dihapus, lalu berhasil setelah layanan dihapus
    $this->actingAs($owner)->delete("/admin/kategori-layanan/{$kategori->id}")
        ->assertSessionHas('error');
    $this->assertDatabaseHas('kategori_layanan', ['id' => $kategori->id]);

    $uploadedPath = $layanan->thumbnail ? public_path(ltrim($layanan->thumbnail, '/')) : null;
    $this->actingAs($owner)->delete("/admin/paket/{$paket->id}")->assertSessionHas('success');
    $this->actingAs($owner)->delete("/admin/layanan/{$layanan->id}")->assertSessionHas('success');

    if ($uploadedPath && File::exists($uploadedPath)) {
        File::delete($uploadedPath);
    }

    $this->actingAs($owner)->delete("/admin/kategori-layanan/{$kategori->id}")
        ->assertSessionHas('success');
    $this->assertDatabaseMissing('kategori_layanan', ['id' => $kategori->id]);
});

test('SRS-F-06 TC-F-06.1..5: katalog kursus publik, CRUD kursus, toggle status, aturan modal & hapus kursus', function () {
    $owner = createSrsUser('owner');
    $pelanggan = createSrsUser('pelanggan');

    $this->actingAs($pelanggan)->get('/admin/courses')->assertStatus(403);

    // TC-F-06.2: Tambah kursus oleh Owner
    $this->actingAs($owner)->post('/admin/courses', [
        'nama' => 'Lighting Masterclass',
        'deskripsi' => 'Teknik pencahayaan studio.',
        'harga' => 450000,
        'durasi' => 'Akses Seumur Hidup',
        'level' => 'Menengah',
        'status' => 'aktif',
        'thumbnail_url' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32',
    ])->assertSessionHas('success');

    $kursus = Kursus::where('nama', 'Lighting Masterclass')->first();
    expect($kursus)->not->toBeNull();

    // TC-F-06.1: Publik melihat kursus aktif & filter level
    $this->get('/kursus?level=Menengah')->assertStatus(200)->assertSee('Lighting Masterclass');
    $this->get("/kursus/{$kursus->id}")->assertStatus(200)->assertSee('Lighting Masterclass');

    // TC-F-06.4: Cek markup modal di /admin/courses tidak memakai click.away / click.self close pada backdrop
    $adminCoursesRes = $this->actingAs($owner)->get('/admin/courses');
    $adminCoursesRes->assertStatus(200);
    $html = $adminCoursesRes->getContent();
    expect($html)->not->toContain('@click.self="openAddCourse = false"');
    expect($html)->not->toContain('@click.self="openEditCourse = false"');
    expect($html)->toContain('openAddCourse = false');

    // TC-F-06.3: Update & toggle status menjadi draft menyembunyikan dari katalog publik
    $this->actingAs($owner)->put("/admin/courses/{$kursus->id}", [
        'nama' => 'Lighting Masterclass Pro',
        'deskripsi' => 'Diperbarui.',
        'harga' => 500000,
        'durasi' => 'Akses Seumur Hidup',
        'level' => 'Mahir',
        'status' => 'aktif',
    ])->assertSessionHas('success');

    $this->actingAs($owner)->post("/admin/courses/{$kursus->id}/toggle-status")
        ->assertSessionHas('success');
    $kursus->refresh();
    expect($kursus->status)->toBe('draft');
    $this->get('/kursus')->assertDontSee('Lighting Masterclass Pro');

    // TC-F-06.5: Hapus kursus
    $this->actingAs($owner)->delete("/admin/courses/{$kursus->id}")->assertSessionHas('success');
    $this->assertDatabaseMissing('kursus', ['id' => $kursus->id]);
});

// =========================================================================
// MODUL 3: MATERI KURSUS, KUIS & LMS (SRS-F-07)
// =========================================================================

test('SRS-F-07 TC-F-07.1..6: akses LMS berizin, progres materi, kuis, sertifikat & CRUD modul/materi/kuis', function () {
    $owner = createSrsUser('owner');
    $pelanggan = createSrsUser('pelanggan');
    $pelangganLain = createSrsUser('pelanggan');

    // TC-F-07.4: Owner membuat kursus, modul, materi, dan kuis multi-soal
    $kursus = Kursus::create([
        'nama' => 'Kursus Komposisi Visual',
        'deskripsi' => 'Silabus terstruktur.',
        'harga' => 300000,
        'durasi' => 'Akses Seumur Hidup',
        'level' => 'Pemula',
        'instruktur_id' => $owner->id,
        'status' => 'aktif',
    ]);

    $this->actingAs($owner)->post("/admin/courses/{$kursus->id}/modules", [
        'judul' => 'Modul 1: Rule of Thirds',
        'deskripsi' => 'Prinsip komposisi.',
        'urutan' => 1,
    ])->assertSessionHas('success');
    $modul = Modul::where('kursus_id', $kursus->id)->first();

    $this->actingAs($owner)->post("/admin/modules/{$modul->id}/materis", [
        'judul' => 'Video Grid Komposisi',
        'tipe' => 'video',
        'isi' => 'Penjelasan rule of thirds.',
        'video_url' => 'https://example.com/video.mp4',
        'urutan' => 1,
    ])->assertSessionHas('success');
    $materi = Materi::where('modul_id', $modul->id)->first();

    $this->actingAs($owner)->post("/admin/modules/{$modul->id}/quizzes", [
        'judul' => 'Kuis Evaluasi Komposisi',
        'durasi_menit' => 20,
        'passing_grade' => 70,
        'soal' => [
            [
                'pertanyaan' => 'Berapa jumlah garis pada grid Rule of Thirds?',
                'pilihan_a' => '2 vertikal & 2 horizontal',
                'pilihan_b' => '1 garis saja',
                'pilihan_c' => '5 vertikal',
                'pilihan_d' => '4 diagonal',
                'jawaban_benar' => 'A',
                'bobot_nilai' => 50,
            ],
            [
                'pertanyaan' => 'Titik potong grid disebut?',
                'pilihan_a' => 'Dead spot',
                'pilihan_b' => 'Power points',
                'pilihan_c' => 'Vignette',
                'pilihan_d' => 'Bokeh',
                'jawaban_benar' => 'B',
                'bobot_nilai' => 50,
            ],
        ],
    ])->assertSessionHas('success');

    $quiz = Quiz::where('modul_id', $modul->id)->first();
    expect($quiz->soals()->count())->toBe(2);

    // TC-F-07.1: Pelanggan belum terdaftar ditolak (403); Owner diizinkan preview (200)
    $this->actingAs($pelanggan)->get("/student/courses/{$kursus->id}/learn")->assertStatus(403);
    $this->actingAs($owner)->get("/student/courses/{$kursus->id}/learn")->assertStatus(200);

    // Daftarkan pelanggan sebagai peserta aktif ('Dibayar')
    $serviceRef = createActiveServiceAndPackage();
    Reservasi::create([
        'tipe' => 'kursus',
        'nomor_reservasi' => Reservasi::generateNomorReservasi('kursus'),
        'user_id' => $pelanggan->id,
        'layanan_id' => $serviceRef['layanan']->id,
        'paket_id' => $serviceRef['paket']->id,
        'kursus_id' => $kursus->id,
        'tanggal' => now()->format('Y-m-d'),
        'waktu' => '09:00:00',
        'lokasi' => 'Online Classroom LMS',
        'status' => 'Dibayar',
    ]);

    // TC-F-07.2: Pelanggan mengakses kelas & menandai materi selesai
    $this->actingAs($pelanggan)->get("/student/courses/{$kursus->id}/learn")
        ->assertStatus(200)
        ->assertSee('Video Grid Komposisi');

    $this->actingAs($pelanggan)->post("/student/materi/{$materi->id}/complete")
        ->assertSessionHas('success');
    expect($kursus->progressOfUser($pelanggan->id))->toBe(100);

    // TC-F-07.3: Kerjakan kuis & terbitkan sertifikat + verifikasi publik + isolasi sertifikat
    $soals = $quiz->soals()->get();
    $this->actingAs($pelanggan)->post("/student/quiz/{$quiz->id}/submit", [
        'answers' => [
            $soals[0]->id => 'A',
            $soals[1]->id => 'B',
        ],
    ])->assertSessionHas('quiz_result');

    $attempt = QuizAttempt::where('quiz_id', $quiz->id)->where('user_id', $pelanggan->id)->first();
    expect($attempt->nilai)->toBe(100);
    expect((bool) $attempt->is_passed)->toBeTrue();

    $this->actingAs($pelanggan)->post("/student/courses/{$kursus->id}/issue-certificate");
    $sertifikat = Sertifikat::where('user_id', $pelanggan->id)->where('kursus_id', $kursus->id)->first();
    expect($sertifikat)->not->toBeNull();

    $this->actingAs($pelanggan)->get("/student/certificates/{$sertifikat->id}")->assertStatus(200);
    $this->actingAs($pelangganLain)->get("/student/certificates/{$sertifikat->id}")->assertStatus(403);
    $this->get("/verify-certificate/{$sertifikat->kode_verifikasi}")
        ->assertStatus(200)
        ->assertSee($sertifikat->nomor_sertifikat);

    // TC-F-07.5: Owner update modul, materi, dan kuis beserta butir soal
    $this->actingAs($owner)->put("/admin/modules/{$modul->id}", [
        'judul' => 'Modul 1: Komposisi Lanjut',
        'urutan' => 1,
        'deskripsi' => 'Updated.',
    ])->assertSessionHas('success');

    $this->actingAs($owner)->put("/admin/materis/{$materi->id}", [
        'judul' => 'Video Grid Komposisi (Updated)',
        'tipe' => 'video',
        'video_url' => 'https://example.com/video-v2.mp4',
        'urutan' => 1,
    ])->assertSessionHas('success');

    $this->actingAs($owner)->put("/admin/quizzes/{$quiz->id}", [
        'judul' => 'Kuis Komposisi Revisi',
        'durasi_menit' => 25,
        'passing_grade' => 80,
        'soal' => [[
            'pertanyaan' => 'Soal revisi tunggal?',
            'pilihan_a' => 'Ya',
            'pilihan_b' => 'Tidak',
            'pilihan_c' => 'Mungkin',
            'pilihan_d' => 'Semua salah',
            'jawaban_benar' => 'A',
            'bobot_nilai' => 100,
        ]],
    ])->assertSessionHas('success');

    expect($quiz->fresh()->judul)->toBe('Kuis Komposisi Revisi');
    expect($quiz->soals()->count())->toBe(1);

    // TC-F-07.6: Hapus materi, kuis, dan modul
    $this->actingAs($owner)->delete("/admin/materis/{$materi->id}")->assertSessionHas('success');
    $this->actingAs($owner)->delete("/admin/quizzes/{$quiz->id}")->assertSessionHas('success');
    $this->actingAs($owner)->delete("/admin/modules/{$modul->id}")->assertSessionHas('success');

    $this->assertDatabaseMissing('materi', ['id' => $materi->id]);
    $this->assertDatabaseMissing('quiz', ['id' => $quiz->id]);
    $this->assertDatabaseMissing('modul', ['id' => $modul->id]);
});

// =========================================================================
// MODUL 4: RESERVASI FOTOGRAFI & KURSUS (SRS-F-08 s.d. SRS-F-09)
// =========================================================================

test('SRS-F-08 TC-F-08.1..6: buat reservasi foto, validasi tanggal/bentrok jam, proofing, & pembatalan', function () {
    $owner = createSrsUser('owner');
    $pelangganA = createSrsUser('pelanggan');
    $pelangganB = createSrsUser('pelanggan');
    $catalog = createActiveServiceAndPackage(2000000, '2 Jam');
    $futureDate = now()->addDays(5)->format('Y-m-d');

    // Mock snap token ONLY for checkout redirect landing check
    $midtransMock = Mockery::mock(MidtransService::class)->makePartial();
    $midtransMock->shouldReceive('getSnapTokenForBooking')->andReturn('snap-token-booking-mock');
    app()->instance(MidtransService::class, $midtransMock);

    // TC-F-08.2: Tanggal kemarin dan jam hari ini yang sudah lewat ditolak
    $this->actingAs($pelangganA)->post('/reservasi', [
        'layanan_id' => $catalog['layanan']->id,
        'paket_id' => $catalog['paket']->id,
        'tanggal' => now()->subDay()->format('Y-m-d'),
        'waktu' => '10:00',
        'lokasi' => 'Studio Jakarta',
    ])->assertSessionHasErrors('tanggal');

    Carbon::setTestNow(Carbon::today()->setTime(14, 0));
    try {
        $this->actingAs($pelangganA)->post('/reservasi', [
            'layanan_id' => $catalog['layanan']->id,
            'paket_id' => $catalog['paket']->id,
            'tanggal' => Carbon::today()->format('Y-m-d'),
            'waktu' => '10:00',
            'lokasi' => 'Studio Jakarta',
        ])->assertSessionHasErrors('waktu');
    } finally {
        Carbon::setTestNow();
    }

    // TC-F-08.1: Buat reservasi foto valid
    $resStore = $this->actingAs($pelangganA)->post('/reservasi', [
        'layanan_id' => $catalog['layanan']->id,
        'paket_id' => $catalog['paket']->id,
        'tanggal' => $futureDate,
        'waktu' => '10:00',
        'lokasi' => 'Studio Jakarta',
        'kebutuhan_khusus' => 'Latar putih',
    ]);
    $booking = Reservasi::where('user_id', $pelangganA->id)->first();
    expect($booking)->not->toBeNull();
    expect($booking->status)->toBe('Menunggu Konfirmasi');
    $resStore->assertRedirect(route('payments.create', ['booking_id' => $booking->id]));

    // AJAX check-jadwal mengembalikan slot 10:00 dan 11:00 (durasi 2 Jam: 10:00-12:00)
    $this->actingAs($pelangganB)->getJson("/reservasi/check-jadwal?tanggal={$futureDate}")
        ->assertStatus(200)
        ->assertJsonPath('tanggal', $futureDate)
        ->assertJsonFragment(['10:00']);

    // TC-F-08.3: Bentrok jadwal pada 11:00 (overlap interval 10:00-12:00) ditolak
    $this->actingAs($pelangganB)->post('/reservasi', [
        'layanan_id' => $catalog['layanan']->id,
        'paket_id' => $catalog['paket']->id,
        'tanggal' => $futureDate,
        'waktu' => '11:00',
        'lokasi' => 'Taman Menteng',
    ])->assertSessionHasErrors('waktu');

    // TC-F-08.4: Pelanggan melihat miliknya, akses ke milik orang lain 404, Owner akses /admin/bookings
    $this->actingAs($pelangganA)->get("/reservasi/{$booking->id}")->assertStatus(200);
    $this->actingAs($pelangganB)->get("/reservasi/{$booking->id}")->assertStatus(404);
    $this->actingAs($owner)->get('/admin/bookings')->assertStatus(200)->assertSee($booking->nomor_reservasi);

    // TC-F-08.5: Owner update status & upload foto proofing; Pelanggan memilih & submit proofing
    $this->actingAs($owner)->post("/admin/bookings/{$booking->id}/status", [
        'status' => 'Terjadwal',
    ])->assertSessionHas('success');
    expect($booking->fresh()->status)->toBe('Terjadwal');

    $fakeProof = UploadedFile::fake()->image('proof1.jpg', 400, 300);
    $this->actingAs($owner)->post("/admin/proofing/{$booking->id}/upload", [
        'fotos' => [$fakeProof],
    ])->assertSessionHas('success');

    $galeri = GaleriFoto::where('reservasi_id', $booking->id)->first();
    expect($galeri)->not->toBeNull();

    $this->actingAs($pelangganA)->get("/customer/proofing/{$booking->id}")->assertStatus(200);
    $this->actingAs($pelangganA)->postJson("/customer/proofing/{$galeri->id}/toggle", [
        'catatan' => 'Tolong haluskan tone kulit',
    ])->assertJson(['success' => true, 'is_selected' => true]);

    $this->actingAs($pelangganA)->postJson("/customer/proofing/{$booking->id}/submit")
        ->assertJson(['success' => true, 'selected_count' => 1]);
    expect($booking->fresh()->status)->toBe('Pemotretan Selesai');

    if ($galeri->foto_path && File::exists(public_path($galeri->foto_path))) {
        File::delete(public_path($galeri->foto_path));
    }

    // TC-F-08.6: Owner membatalkan reservasi -> slot 10:00 terbuka kembali untuk dipesan ulang
    $this->actingAs($owner)->post("/admin/bookings/{$booking->id}/status", [
        'status' => 'Dibatalkan',
    ])->assertSessionHas('success');
    expect($booking->fresh()->status)->toBe('Dibatalkan');

    $this->actingAs($pelangganB)->post('/reservasi', [
        'layanan_id' => $catalog['layanan']->id,
        'paket_id' => $catalog['paket']->id,
        'tanggal' => $futureDate,
        'waktu' => '10:00',
        'lokasi' => 'Taman Menteng',
    ])->assertSessionHasNoErrors()->assertSessionHas('success');
});

test('SRS-F-09 TC-F-09.1..4: daftar kursus, daftar peserta owner & pelanggan, konfirmasi peserta kursus & akses student dashboard', function () {
    $owner = createSrsUser('owner');
    $pelanggan = createSrsUser('pelanggan');
    $syllabus = createCourseWithSyllabus($owner);
    $kursus = $syllabus['kursus'];

    // TC-F-09.1: Checkout kursus (/payments/create?kursus_id=...)
    $midtransMock = Mockery::mock(MidtransService::class)->makePartial();
    $midtransMock->shouldReceive('getSnapTokenForKursus')->andReturn('snap-token-kursus-mock');
    app()->instance(MidtransService::class, $midtransMock);

    $this->actingAs($pelanggan)->get("/payments/create?kursus_id={$kursus->id}")
        ->assertStatus(200)
        ->assertSee($kursus->nama);

    // Buat transaksi pendaftaran kursus pending
    $pendingKursusPay = Pembayaran::create([
        'nomor_transaksi' => 'TRX-KRS-' . $kursus->id . '-' . $pelanggan->id . '-' . time(),
        'user_id' => $pelanggan->id,
        'kursus_id' => $kursus->id,
        'jumlah' => $kursus->harga,
        'metode_pembayaran' => 'Midtrans (QRIS)',
        'payment_type' => 'qris',
        'status' => 'Menunggu Verifikasi',
    ]);

    // TC-F-09.2: Owner melihat daftar peserta di /admin/courses/reservations
    $this->actingAs($owner)->get('/admin/courses/reservations')
        ->assertStatus(200)
        ->assertSee($pelanggan->name)
        ->assertSee($kursus->nama);

    // TC-F-09.3: Konfirmasi status Dibayar oleh Owner
    // Catatan: Uji dilakukan pada skema DB uji (SQLite/MySQL); jika kolom reservasi foto non-nullable di SQLite terdeteksi, catat hasilnya secara jujur
    $responseConfirm = $this->actingAs($owner)->post("/admin/courses/reservations/{$pendingKursusPay->id}/status", [
        'status' => 'Dibayar',
    ]);
    $responseConfirm->assertSessionHas('success');

    $pendingKursusPay->refresh();
    expect($pendingKursusPay->status)->toBe('Dibayar');
    expect($pendingKursusPay->reservasi_id)->not->toBeNull();
    $this->assertDatabaseHas('invoice', [
        'pembayaran_id' => $pendingKursusPay->id,
        'kursus_id' => $kursus->id,
        'status_pembayaran' => 'LUNAS',
    ]);

    // TC-F-09.4: Pelanggan mengakses /student/dashboard & /customer/reservasi-kursus
    $this->actingAs($pelanggan)->get('/student/dashboard')
        ->assertStatus(200)
        ->assertSee($kursus->nama);
    $this->actingAs($pelanggan)->get('/customer/reservasi-kursus')
        ->assertStatus(200)
        ->assertSee($kursus->nama);
});

// =========================================================================
// MODUL 5: JADWAL & PEMBAYARAN MIDTRANS (SRS-F-010 s.d. SRS-F-011)
// =========================================================================

test('SRS-F-010 TC-F-010.1..4: kalender jadwal owner vs pelanggan, detail modal, reschedule & deteksi bentrok', function () {
    $owner = createSrsUser('owner');
    $pelangganA = createSrsUser('pelanggan');
    $pelangganB = createSrsUser('pelanggan');
    $catalog = createActiveServiceAndPackage();

    $dateA = now()->addDays(7)->format('Y-m-d');
    $dateB = now()->addDays(8)->format('Y-m-d');

    $bookingA = Reservasi::create([
        'tipe' => 'fotografi',
        'nomor_reservasi' => 'RSV-FTG-TEST-001',
        'user_id' => $pelangganA->id,
        'layanan_id' => $catalog['layanan']->id,
        'paket_id' => $catalog['paket']->id,
        'tanggal' => $dateA,
        'waktu' => '09:00:00',
        'lokasi' => 'Studio A',
        'status' => 'Terjadwal',
    ]);

    $bookingB = Reservasi::create([
        'tipe' => 'fotografi',
        'nomor_reservasi' => 'RSV-FTG-TEST-002',
        'user_id' => $pelangganB->id,
        'layanan_id' => $catalog['layanan']->id,
        'paket_id' => $catalog['paket']->id,
        'tanggal' => $dateB,
        'waktu' => '14:00:00',
        'lokasi' => 'Outdoor B',
        'status' => 'Disetujui',
    ]);

    // TC-F-010.1 & TC-F-010.2: Owner melihat semua jadwal, Pelanggan A hanya miliknya
    $this->actingAs($owner)->get('/jadwal')
        ->assertStatus(200)
        ->assertSee('RSV-FTG-TEST-001')
        ->assertSee('RSV-FTG-TEST-002');

    $this->actingAs($pelangganA)->get('/jadwal')
        ->assertStatus(200)
        ->assertSee('RSV-FTG-TEST-001')
        ->assertDontSee('RSV-FTG-TEST-002');

    // TC-F-010.4: Reschedule ke slot yang bentrok dengan booking B ditolak
    $this->actingAs($owner)->put("/jadwal/{$bookingA->id}", [
        'tanggal' => $dateB,
        'waktu' => '14:00',
    ])->assertSessionHas('error');
    expect($bookingA->fresh()->tanggal)->toBe($dateA);

    // TC-F-010.3: Reschedule ke slot kosong berhasil & kirim notifikasi
    $dateFree = now()->addDays(10)->format('Y-m-d');
    $this->actingAs($owner)->put("/jadwal/{$bookingA->id}", [
        'tanggal' => $dateFree,
        'waktu' => '16:00',
        'status' => 'Terjadwal',
    ])->assertSessionHas('success');

    expect($bookingA->fresh()->tanggal)->toBe($dateFree);
    expect($bookingA->fresh()->waktu)->toBe('16:00:00');
    $this->assertDatabaseHas('notifikasi', [
        'user_id' => $pelangganA->id,
        'judul' => 'Jadwal Sesi Foto Diperbarui',
    ]);
});

test('SRS-F-011 TC-F-011.1..6: Midtrans callback SHA512 valid & palsu, payment finish, auto-heal invoice, RBAC invoice & riwayat', function () {
    $owner = createSrsUser('owner');
    $pelangganA = createSrsUser('pelanggan');
    $pelangganB = createSrsUser('pelanggan');
    $catalog = createActiveServiceAndPackage(1750000);

    $booking = Reservasi::create([
        'tipe' => 'fotografi',
        'nomor_reservasi' => 'RSV-FTG-PAY-001',
        'user_id' => $pelangganA->id,
        'layanan_id' => $catalog['layanan']->id,
        'paket_id' => $catalog['paket']->id,
        'tanggal' => now()->addDays(6)->format('Y-m-d'),
        'waktu' => '10:00:00',
        'lokasi' => 'Studio Utama',
        'status' => 'Menunggu Pembayaran',
    ]);

    $serverKey = config('midtrans.server_key');
    $orderId = $booking->nomor_reservasi;
    $statusCode = '200';
    $grossAmount = '1750000.00';

    // TC-F-011.5: Signature palsu harus ditolak tanpa mengubah database
    $this->postJson('/payments/midtrans-callback', [
        'order_id' => $orderId,
        'status_code' => $statusCode,
        'gross_amount' => $grossAmount,
        'transaction_status' => 'settlement',
        'payment_type' => 'qris',
        'signature_key' => 'palsu-signature-hash',
    ])->assertStatus(200)->assertJson([
        'status' => 'error',
        'message' => 'Signature verification failed',
    ]);
    expect($booking->fresh()->status)->toBe('Menunggu Pembayaran');

    // TC-F-011.4: Callback SHA512 valid (MidtransService asli) melunasi booking & menerbitkan invoice
    $validSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
    $this->postJson('/payments/midtrans-callback', [
        'order_id' => $orderId,
        'status_code' => $statusCode,
        'gross_amount' => $grossAmount,
        'transaction_status' => 'settlement',
        'payment_type' => 'qris',
        'acquirer' => 'gopay',
        'signature_key' => $validSignature,
    ])->assertStatus(200)->assertJson([
        'status' => 'success',
        'message' => 'Payment settled and booking confirmed',
    ]);

    expect($booking->fresh()->status)->toBe('Dibayar');
    $pembayaran = Pembayaran::where('reservasi_id', $booking->id)->first();
    expect($pembayaran)->not->toBeNull();
    expect($pembayaran->status)->toBe('Dibayar');
    expect($pembayaran->channel_pembayaran['label'])->toBe('QRIS (Gopay)');

    $invoice = Invoice::where('pembayaran_id', $pembayaran->id)->first();
    expect($invoice)->not->toBeNull();

    // TC-F-011.1 & TC-F-011.2: Pemilik invoice & Owner bisa membuka /invoices/{id}; pelanggan lain ditolak 403
    $this->actingAs($pelangganA)->get("/invoices/{$invoice->id}")
        ->assertStatus(200)
        ->assertSee($invoice->nomor_invoice);
    $this->actingAs($owner)->get("/invoices/{$invoice->id}")->assertStatus(200);
    $this->actingAs($pelangganB)->get("/invoices/{$invoice->id}")->assertStatus(403);

    // Uji auto-heal invoice: hapus record invoice, lalu akses via pembayaran_id harus membuat ulang otomatis
    $invoice->delete();
    $this->actingAs($pelangganA)->get("/invoices/{$pembayaran->id}")->assertStatus(200);
    expect(Invoice::where('pembayaran_id', $pembayaran->id)->exists())->toBeTrue();

    // TC-F-011.4 (cabang gagal/expire): Booking baru dengan callback expire menjadi Dibatalkan
    $bookingExpired = Reservasi::create([
        'tipe' => 'fotografi',
        'nomor_reservasi' => 'RSV-FTG-EXP-002',
        'user_id' => $pelangganB->id,
        'layanan_id' => $catalog['layanan']->id,
        'paket_id' => $catalog['paket']->id,
        'tanggal' => now()->addDays(9)->format('Y-m-d'),
        'waktu' => '13:00:00',
        'lokasi' => 'Outdoor',
        'status' => 'Menunggu Pembayaran',
    ]);
    $expSig = hash('sha512', $bookingExpired->nomor_reservasi . '202' . $grossAmount . $serverKey);
    $this->postJson('/payments/midtrans-callback', [
        'order_id' => $bookingExpired->nomor_reservasi,
        'status_code' => '202',
        'gross_amount' => $grossAmount,
        'transaction_status' => 'expire',
        'payment_type' => 'bank_transfer',
        'signature_key' => $expSig,
    ])->assertJson(['status' => 'failed']);

    expect($bookingExpired->fresh()->status)->toBe('Dibatalkan');
    expect(Pembayaran::where('reservasi_id', $bookingExpired->id)->first()->status)->toBe('Ditolak');

    // TC-F-011.4 (cabang kursus KRS-...) & TC-F-011.3 paymentFinish
    $syllabus = createCourseWithSyllabus($owner);
    $krsOrderId = 'KRS-' . $syllabus['kursus']->id . '-' . $pelangganB->id . '-' . time();
    $krsGross = '250000.00';
    $krsSig = hash('sha512', $krsOrderId . '200' . $krsGross . $serverKey);
    $this->postJson('/payments/midtrans-callback', [
        'order_id' => $krsOrderId,
        'status_code' => '200',
        'gross_amount' => $krsGross,
        'transaction_status' => 'settlement',
        'payment_type' => 'bank_transfer',
        'va_numbers' => [['bank' => 'bca', 'va_number' => '1234567890']],
        'signature_key' => $krsSig,
    ])->assertStatus(200)->assertJson([
        'status' => 'success',
        'message' => 'Course payment settled and student enrolled',
    ]);

    expect($syllabus['kursus']->isEnrolledBy($pelangganB->id))->toBeTrue();
    $this->assertDatabaseHas('pembayaran', [
        'nomor_transaksi' => 'TRX-' . $krsOrderId,
        'kursus_id' => $syllabus['kursus']->id,
        'status' => 'Dibayar',
    ]);

    // TC-F-011.3: Endpoint paymentFinish memproses redirect status settlement
    $this->actingAs($pelangganA)->get("/payments/finish?order_id={$orderId}&transaction_status=settlement")
        ->assertRedirect(route('customer.bookings.show', $booking->id));

    // TC-F-011.6: Riwayat pembayaran pelanggan, panel admin & verifikasi manual
    $this->actingAs($pelangganA)->get('/pembayaran')
        ->assertStatus(200)
        ->assertSee($pembayaran->nomor_transaksi);
    $this->actingAs($owner)->get('/admin/payments')
        ->assertStatus(200)
        ->assertSee($pembayaran->nomor_transaksi);

    $manualPay = Pembayaran::create([
        'nomor_transaksi' => 'TRX-MANUAL-99',
        'user_id' => $pelangganB->id,
        'reservasi_id' => $bookingExpired->id,
        'jumlah' => 1750000,
        'metode_pembayaran' => 'Transfer Bank Manual',
        'status' => 'Menunggu Verifikasi',
    ]);
    $this->actingAs($owner)->post("/admin/payments/{$manualPay->id}/verify", [
        'status' => 'Dibayar',
        'catatan' => 'Mutasi rekening terverifikasi',
    ])->assertSessionHas('success');
    expect($manualPay->fresh()->status)->toBe('Dibayar');
    expect($bookingExpired->fresh()->status)->toBe('Dibayar');
});

// =========================================================================
// MODUL 6: KONSULTASI, PORTOFOLIO & LAPORAN (SRS-F-012 s.d. SRS-F-014)
// =========================================================================

test('SRS-F-012 TC-F-012.2: pengajuan konsultasi tanpa nomor WhatsApp/telepon ditolak', function () {
    $this->from('/konsultasi')->post('/konsultasi', [
        'nama' => 'Tamu Tanpa HP',
        'email' => 'nohp@example.com',
        'jenis_layanan' => 'Wedding',
        'kebutuhan' => 'Butuh info paket',
    ])->assertRedirect('/konsultasi')->assertSessionHasErrors('phone');
});

test('SRS-F-013 TC-F-013.1..5: portofolio publik & CRUD album serta upload/hapus galeri foto', function () {
    Storage::fake('public');
    $owner = createSrsUser('owner');

    // TC-F-013.2 negatif: Tanpa foto sampul maupun galeri ditolak
    $this->actingAs($owner)->from('/admin/portofolio')->post('/admin/portofolio', [
        'judul' => 'Album Kosong',
        'kategori' => 'Wedding',
        'tanggal' => now()->format('Y-m-d'),
        'status_publikasi' => 'published',
    ])->assertSessionHas('error');

    // TC-F-013.2 positif: Buat album baru beserta sampul & multiple gallery
    $cover = UploadedFile::fake()->image('cover.jpg', 800, 600);
    $gal1 = UploadedFile::fake()->image('gal1.jpg', 800, 600);
    $gal2 = UploadedFile::fake()->image('gal2.jpg', 800, 600);

    $this->actingAs($owner)->post('/admin/portofolio', [
        'judul' => 'Golden Hour Intimate Wedding',
        'kategori' => 'Wedding',
        'deskripsi' => 'Sesi sore di Lembang.',
        'tanggal' => now()->format('Y-m-d'),
        'status_publikasi' => 'published',
        'foto_file' => $cover,
        'gallery_files' => [$gal1, $gal2],
    ])->assertSessionHas('success');

    $album = Portofolio::where('judul', 'Golden Hour Intimate Wedding')->first();
    expect($album)->not->toBeNull();
    expect($album->fotos()->count())->toBe(2);
    Storage::disk('public')->assertExists($album->foto);

    // TC-F-013.1: Publik melihat album published & detailnya
    $this->get('/portofolio')->assertStatus(200)->assertSee('Golden Hour Intimate Wedding');
    $this->get("/portofolio/{$album->id}")->assertStatus(200)->assertSee('Golden Hour Intimate Wedding');

    // TC-F-013.3: Update album, toggle status jadi draft menyembunyikan dari publik
    $this->actingAs($owner)->put("/admin/portofolio/{$album->id}", [
        'judul' => 'Golden Hour Wedding Edited',
        'kategori' => 'Wedding',
        'tanggal' => now()->format('Y-m-d'),
        'status_publikasi' => 'published',
    ])->assertSessionHas('success');

    $this->actingAs($owner)->post("/admin/portofolio/{$album->id}/toggle-status")
        ->assertSessionHas('success');
    expect($album->fresh()->status_publikasi)->toBe('draft');
    $this->get('/portofolio')->assertDontSee('Golden Hour Wedding Edited');
    $this->get("/portofolio/{$album->id}")->assertStatus(404);

    // TC-F-013.4: Upload foto tambahan & hapus satu foto galeri
    $gal3 = UploadedFile::fake()->image('gal3.jpg', 800, 600);
    $this->actingAs($owner)->post("/admin/portofolio/{$album->id}/photos", [
        'photos' => [$gal3],
        'caption' => 'Momen candid',
    ])->assertSessionHas('success');
    expect($album->fotos()->count())->toBe(3);

    $fotoToDelete = $album->fotos()->first();
    $deletedPhotoPath = $fotoToDelete->foto;
    $this->actingAs($owner)->delete("/admin/portofolio/photos/{$fotoToDelete->id}")
        ->assertSessionHas('success');
    Storage::disk('public')->assertMissing($deletedPhotoPath);
    expect($album->fotos()->count())->toBe(2);

    // TC-F-013.5: Hapus album beserta file di storage
    $coverPath = $album->foto;
    $this->actingAs($owner)->delete("/admin/portofolio/{$album->id}")
        ->assertRedirect('/admin/portofolio')
        ->assertSessionHas('success');
    $this->assertDatabaseMissing('portofolio', ['id' => $album->id]);
    Storage::disk('public')->assertMissing($coverPath);
});

test('SRS-F-014 TC-F-014.1,2,5: akurasi agregasi metrik laporan pendapatan/reservasi & blokir pelanggan (HTTP 403)', function () {
    $owner = createSrsUser('owner');
    $pelanggan = createSrsUser('pelanggan');
    $catalog = createActiveServiceAndPackage(1250000);
    $syllabus = createCourseWithSyllabus($owner);

    $today = now()->format('Y-m-d');
    $booking = Reservasi::create([
        'tipe' => 'fotografi',
        'nomor_reservasi' => 'RSV-FTG-RPT-001',
        'user_id' => $pelanggan->id,
        'layanan_id' => $catalog['layanan']->id,
        'paket_id' => $catalog['paket']->id,
        'tanggal' => $today,
        'waktu' => '10:00:00',
        'lokasi' => 'Studio',
        'status' => 'Dibayar',
    ]);

    Pembayaran::create([
        'nomor_transaksi' => 'TRX-RPT-FTO-1',
        'user_id' => $pelanggan->id,
        'reservasi_id' => $booking->id,
        'jumlah' => 1250000,
        'metode_pembayaran' => 'Midtrans (QRIS)',
        'tanggal_pembayaran' => now(),
        'status' => 'Dibayar',
    ]);

    Pembayaran::create([
        'nomor_transaksi' => 'TRX-RPT-KRS-1',
        'user_id' => $pelanggan->id,
        'kursus_id' => $syllabus['kursus']->id,
        'jumlah' => 350000,
        'metode_pembayaran' => 'Midtrans (BANK_TRANSFER)',
        'tanggal_pembayaran' => now(),
        'status' => 'Dibayar',
    ]);

    // Transaksi gagal tidak boleh dihitung ke total pendapatan
    Pembayaran::create([
        'nomor_transaksi' => 'TRX-RPT-FAIL-1',
        'user_id' => $pelanggan->id,
        'kursus_id' => $syllabus['kursus']->id,
        'jumlah' => 999000,
        'metode_pembayaran' => 'Midtrans',
        'tanggal_pembayaran' => now(),
        'status' => 'Ditolak',
    ]);

    // TC-F-014.1: Agregasi total (1.250.000 + 350.000 = 1.600.000) & tanpa angka 999.000
    $this->actingAs($owner)->get("/admin/reports/pendapatan?start_date={$today}&end_date={$today}")
        ->assertStatus(200)
        ->assertSee('1.600.000')
        ->assertDontSee('999.000');

    // TC-F-014.2: Laporan reservasi menampilkan data foto sesuai filter tipe
    $this->actingAs($owner)->get("/admin/reports/reservasi?start_date={$today}&end_date={$today}&tipe=foto")
        ->assertStatus(200)
        ->assertSee('RSV-FTG-RPT-001');

    // TC-F-014.5: Pelanggan dilarang mengakses laporan maupun ekspor PDF/Excel (HTTP 403)
    $this->actingAs($pelanggan)->get('/admin/reports/pendapatan')->assertStatus(403);
    $this->actingAs($pelanggan)->get('/admin/reports/reservasi')->assertStatus(403);
    $this->actingAs($pelanggan)->get('/admin/reports/pendapatan/pdf')->assertStatus(403);
    $this->actingAs($pelanggan)->get('/admin/reports/pendapatan/excel')->assertStatus(403);
});
