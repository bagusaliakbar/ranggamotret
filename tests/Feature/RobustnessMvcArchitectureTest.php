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

