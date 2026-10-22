<?php

use App\Models\User;

test('new user can register without choosing role and becomes unified member', function () {
    $response = $this->post('/register', [
        'name' => 'Budi Santoso',
        'email' => 'budi@example.com',
        'phone' => '081234567890',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertRedirect('/dashboard');

    $this->assertDatabaseHas('users', [
        'email' => 'budi@example.com',
        'role' => 'customer',
    ]);

    $this->assertDatabaseHas('customers', [
        'phone' => '081234567890',
    ]);
});

test('unified member can access both customer booking dashboard and student lms dashboard', function () {
    $user = User::create([
        'name' => 'Siti Nurhaliza',
        'email' => 'siti@example.com',
        'password' => bcrypt('password123'),
        'role' => 'customer',
        'status' => 'active',
    ]);

    // Can access unified dashboard
    $this->actingAs($user)->get('/dashboard')->assertStatus(200);

    // Can access customer dashboard & booking
    $this->actingAs($user)->get('/customer/dashboard')->assertStatus(200);
    $this->actingAs($user)->get('/customer/bookings')->assertStatus(200);
    $this->actingAs($user)->get('/reservasi')->assertStatus(200);

    // Can also access student lms dashboard
    $this->actingAs($user)->get('/student/dashboard')->assertStatus(200);
});

test('public service detail page renders successfully with luxury UI', function () {
    $layanan = \App\Models\Layanan::create([
        'nama' => 'Wedding Luxury Photography',
        'kategori' => 'Wedding',
        'deskripsi' => 'Layanan dokumentasi pernikahan eksklusif.',
        'harga_mulai' => 5000000,
        'thumbnail' => 'https://images.unsplash.com/photo-1519741497674-611481863552',
        'status' => 'aktif',
    ]);

    \App\Models\Paket::create([
        'layanan_id' => $layanan->id,
        'nama_paket' => 'Royal Signature',
        'harga' => 8500000,
        'durasi' => 'Full Day',
        'jumlah_fotografer' => 2,
        'jumlah_foto' => 100,
        'fasilitas' => 'Album Cetak Eksklusif Box Kayu',
        'status' => 'aktif',
    ]);

    $response = $this->get('/services/' . $layanan->id);
    $response->assertStatus(200);
    $response->assertSee('Wedding Luxury Photography');
    $response->assertSee('Royal Signature');
    $response->assertSee('8.500.000');
});

