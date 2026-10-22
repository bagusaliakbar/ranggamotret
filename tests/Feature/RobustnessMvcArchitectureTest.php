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
            'role' => 'customer',
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
    $this->actingAs($user)->get('/pembayaran')->assertStatus(200);
    $this->actingAs($user)->get('/jadwal')->assertStatus(200);
    $this->actingAs($user)->get('/customer/consultations')->assertStatus(200);
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
            'role' => 'customer',
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
        ->assertSee('Riwayat Konsultasi Anda')
        ->assertSee('Wisuda / Graduation');
});

