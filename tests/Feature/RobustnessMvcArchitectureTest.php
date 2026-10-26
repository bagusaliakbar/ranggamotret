<?php

use App\Models\User;
use App\Models\Katalog;
use App\Models\Reservasi;
use App\Models\Jadwal;
use App\Models\Kursus;
use App\Models\Portofolio;

test('controllers and routes corresponding to robustness boundaries respond with HTTP 200', function () {
    $user = User::firstOrCreate(
        ['email' => 'routetester@example.com'],
        [
            'name' => 'Route Tester',
            'password' => bcrypt('password123'),
            'role' => 'pelanggan',
            'status' => 'active',
        ]
    );

    // Boundary: katalog
    $this->get('/katalog')->assertStatus(200);

    // Boundary: portofolio
    $this->get('/portofolio')->assertStatus(200);

    // Boundary: kursus
    $this->get('/kursus')->assertStatus(200);

    // Boundary: konsultasi
    $this->get('/konsultasi')->assertStatus(200);

    // Authenticated Boundaries
    $this->actingAs($user)->get('/dashboard')->assertStatus(200);
    $this->actingAs($user)->get('/profile')->assertStatus(200);
    $this->actingAs($user)->get('/reservasi')->assertStatus(200);
    $this->actingAs($user)->get('/customer/fotografi')->assertStatus(200)->assertSee('Layanan Fotografi Saya');
    $this->actingAs($user)->get('/pembayaran')->assertStatus(200);
    $this->actingAs($user)->get('/jadwal')->assertStatus(200);
    $this->actingAs($user)->get('/customer/consultations')->assertStatus(200);
    $this->actingAs($user)->get('/student/dashboard')->assertStatus(200)->assertSee('Kursus Akademi Saya');
    $this->actingAs($user)->get('/customer/reservasi-kursus')->assertStatus(200)->assertSee('Riwayat Reservasi Kursus');
});

test('nine robustness diagram entity models are properly registered and functional', function () {
    $robustnessEntities = [
        \App\Models\User::class,
        \App\Models\Katalog::class,
        \App\Models\Kursus::class,
        \App\Models\Materi::class,
        \App\Models\Jadwal::class,
        \App\Models\Reservasi::class,
        \App\Models\Konsultasi::class,
        \App\Models\Portofolio::class,
        \App\Models\Pembayaran::class,
    ];

    foreach ($robustnessEntities as $entityClass) {
        $this->assertTrue(class_exists($entityClass), "Entity {$entityClass} must exist.");
        $instance = new $entityClass();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Model::class, $instance);
    }
});

test('customer can submit consultation request successfully', function () {
    $response = $this->post('/consultation', [
        'nama' => 'Budi Santoso',
        'email' => 'budi@example.com',
        'phone' => '081234567899',
        'jenis_layanan' => 'Wedding & Prewedding',
        'konsep' => 'Outdoor Sunset',
        'lokasi' => 'Pantai Indah Kapuk',
        'tanggal' => '2026-11-20',
        'budget' => 5000000,
        'kebutuhan' => 'Mohon rekomendasi paket prewedding lengkap busana.',
    ]);

    $response->assertSessionHas('success');
    $this->assertDatabaseHas('konsultasi', [
        'nama' => 'Budi Santoso',
        'email' => 'budi@example.com',
        'phone' => '081234567899',
        'jenis_layanan' => 'Wedding & Prewedding',
    ]);
});

test('authenticated user consultation links user_id and appears in consultation view', function () {
    $user = User::firstOrCreate(
        ['email' => 'consultclient@example.com'],
        [
            'name' => 'Consult Client',
            'password' => bcrypt('password123'),
            'role' => 'pelanggan',
            'status' => 'active',
        ]
    );

    $this->actingAs($user)->post('/konsultasi', [
        'nama' => $user->name,
        'email' => $user->email,
        'phone' => '081987654321',
        'jenis_layanan' => 'Wisuda / Graduation',
        'konsep' => 'Studio Portrait Elegant',
        'budget' => 1500000,
        'kebutuhan' => 'Foto wisuda bersama 4 anggota keluarga.',
    ])->assertSessionHas('success');

    $this->assertDatabaseHas('konsultasi', [
        'user_id' => $user->id,
        'email' => $user->email,
        'jenis_layanan' => 'Wisuda / Graduation',
    ]);

    $this->actingAs($user)->get('/konsultasi')
        ->assertStatus(200)
        ->assertDontSee('Riwayat Konsultasi Anda');

    $this->actingAs($user)->get('/customer/consultations')
        ->assertStatus(200)
        ->assertSee('Riwayat Konsultasi Konsep')
        ->assertSee('Wisuda / Graduation');
});

test('course detail page with eager loaded relationships returns HTTP 200', function () {
    $owner = User::firstOrCreate(
        ['email' => 'owner@test.com'],
        ['name' => 'Owner Studio Test', 'password' => bcrypt('password123'), 'role' => 'owner', 'status' => 'active']
    );

    $kursus = Kursus::create([
        'nama' => 'Mastering Portrait Photography',
        'deskripsi' => 'Belajar portrait dari nol hingga pro.',
        'harga' => 350000,
        'durasi' => '4 Minggu',
        'level' => 'Pemula',
        'instruktur_id' => $owner->id,
        'status' => 'published',
    ]);

    $this->get('/kursus/' . $kursus->id)
        ->assertStatus(200)
        ->assertSee('Mastering Portrait Photography');
});

test('admin can access reports and export pdf and excel with active filters', function () {
    $admin = User::firstOrCreate(
        ['email' => 'admin_report_test@example.com'],
        [
            'name' => 'Owner Admin Tester',
            'password' => bcrypt('password123'),
            'role' => 'owner',
            'status' => 'active',
        ]
    );

    // 1. Laporan Pendapatan Web View
    $this->actingAs($admin)->get('/admin/reports/pendapatan')
        ->assertStatus(200)
        ->assertSee('Laporan Pendapatan');

    // 2. Laporan Pendapatan PDF Export
    $resPendapatanPdf = $this->actingAs($admin)->get('/admin/reports/pendapatan/pdf?start_date=2026-01-01&end_date=2026-12-31');
    $resPendapatanPdf->assertStatus(200);
    $this->assertStringContainsString('application/pdf', $resPendapatanPdf->headers->get('content-type'));

    // 3. Laporan Pendapatan Excel Export
    $resPendapatanExcel = $this->actingAs($admin)->get('/admin/reports/pendapatan/excel?start_date=2026-01-01&end_date=2026-12-31');
    $resPendapatanExcel->assertStatus(200);
    $this->assertStringContainsString('spreadsheetml.sheet', $resPendapatanExcel->headers->get('content-type'));

    // 4. Laporan Reservasi Web View
    $this->actingAs($admin)->get('/admin/reports/reservasi')
        ->assertStatus(200)
        ->assertSee('Laporan Reservasi');

    // 5. Laporan Reservasi PDF Export
    $resReservasiPdf = $this->actingAs($admin)->get('/admin/reports/reservasi/pdf?start_date=2026-01-01&end_date=2026-12-31&tipe=semua');
    $resReservasiPdf->assertStatus(200);
    $this->assertStringContainsString('application/pdf', $resReservasiPdf->headers->get('content-type'));

    // 6. Laporan Reservasi Excel Export
    $resReservasiExcel = $this->actingAs($admin)->get('/admin/reports/reservasi/excel?start_date=2026-01-01&end_date=2026-12-31&tipe=semua');
    $resReservasiExcel->assertStatus(200);
    $this->assertStringContainsString('spreadsheetml.sheet', $resReservasiExcel->headers->get('content-type'));

    // 7. Laporan Pendapatan PDF with Category & Method Filters
    $resFilteredPdf = $this->actingAs($admin)->get('/admin/reports/pendapatan/pdf?start_date=2026-01-01&end_date=2026-12-31&kategori=foto&metode=manual');
    $resFilteredPdf->assertStatus(200);

    // 8. Laporan Reservasi Excel with Tipe & Status Filters
    $resFilteredExcel = $this->actingAs($admin)->get('/admin/reports/reservasi/excel?start_date=2026-01-01&end_date=2026-12-31&tipe=kursus&status=Dibayar');
    $resFilteredExcel->assertStatus(200);
});

test('consultation supports two-way threaded chat between customer and owner in single table', function () {
    $pelanggan = User::create([
        'name' => 'Bagus Klien',
        'email' => 'bagus.klien@test.com',
        'password' => bcrypt('password123'),
        'role' => 'pelanggan',
        'status' => 'active',
    ]);

    $owner = User::firstOrCreate(
        ['email' => 'owner@test.com'],
        ['name' => 'Owner Studio Test', 'password' => bcrypt('password123'), 'role' => 'owner', 'status' => 'active']
    );

    // 1. Pelanggan mengajukan konsultasi awal
    $this->actingAs($pelanggan)->post('/konsultasi', [
        'nama' => $pelanggan->name,
        'email' => $pelanggan->email,
        'phone' => '082112345678',
        'jenis_layanan' => 'Wedding & Prewedding',
        'konsep' => 'Outdoor Cinematic',
        'kebutuhan' => 'Bisa untuk foto prewedding di area Lembang?',
    ])->assertSessionHas('success');

    $konsultasi = \App\Models\Konsultasi::where('email', 'bagus.klien@test.com')->first();
    expect($konsultasi)->not->toBeNull();
    expect($konsultasi->chat_messages)->toHaveCount(1);
    expect($konsultasi->chat_messages[0]['pesan'])->toBe('Bisa untuk foto prewedding di area Lembang?');

    // 2. Owner membalas pesan
    $this->actingAs($owner)->post("/admin/consultations/{$konsultasi->id}/reply", [
        'pesan' => 'Bisa sekali kak! Rencana untuk tanggal berapa?',
    ])->assertSessionHas('success');

    $konsultasi->refresh();
    expect($konsultasi->chat_messages)->toHaveCount(2);
    expect($konsultasi->status)->toBe('replied');
    expect($konsultasi->chat_messages[1]['sender'])->toBe('owner');

    // 3. Pelanggan membalas kembali
    $this->actingAs($pelanggan)->post("/customer/consultations/{$konsultasi->id}/reply", [
        'pesan' => 'Rencana tanggal 15 November kak, konsep casual.',
    ])->assertSessionHas('success');

    $konsultasi->refresh();
    expect($konsultasi->chat_messages)->toHaveCount(3);
    expect($konsultasi->status)->toBe('pending');
    expect($konsultasi->chat_messages[2]['sender'])->toBe('pelanggan');

    // 4. Verifikasi tampilan di portal pelanggan
    $this->actingAs($pelanggan)->get('/customer/consultations')
        ->assertStatus(200)
        ->assertSee('Rencana tanggal 15 November kak, konsep casual.')
        ->assertSee('Ruang Diskusi Konsep');

    // 5. Verifikasi tampilan di portal owner
    $this->actingAs($owner)->get('/admin/consultations')
        ->assertStatus(200)
        ->assertSee('Ruang Diskusi Konsultasi');

    // 6. Pelanggan mengakhiri chat (End Chat / Selesai)
    $this->actingAs($pelanggan)->post("/customer/consultations/{$konsultasi->id}/close")
        ->assertSessionHas('success');

    $konsultasi->refresh();
    expect($konsultasi->status)->toBe('selesai');
    expect($konsultasi->chat_messages)->toHaveCount(4);
    expect($konsultasi->chat_messages[3]['sender'])->toBe('system');
    expect($konsultasi->chat_messages[3]['pesan'])->toContain('diselesaikan oleh klien');

    // Tampilan portal pelanggan setelah chat selesai menampilkan banner & tombol booking
    $this->actingAs($pelanggan)->get('/customer/consultations')
        ->assertStatus(200)
        ->assertSee('Percakapan Konsultasi Telah Selesai')
        ->assertSee('Lanjut Booking Foto');

    // 7. Pelanggan membuka kembali chat (Reopen)
    $this->actingAs($pelanggan)->post("/customer/consultations/{$konsultasi->id}/reopen")
        ->assertSessionHas('success');

    $konsultasi->refresh();
    expect($konsultasi->status)->toBe('pending');

    // 8. Owner mengakhiri konsultasi
    $this->actingAs($owner)->post("/admin/consultations/{$konsultasi->id}/close")
        ->assertSessionHas('success');

    $konsultasi->refresh();
    expect($konsultasi->status)->toBe('selesai');

    // 9. Owner membuka kembali konsultasi
    $this->actingAs($owner)->post("/admin/consultations/{$konsultasi->id}/reopen")
        ->assertSessionHas('success');

    $konsultasi->refresh();
    expect($konsultasi->status)->toBe('replied');
});


