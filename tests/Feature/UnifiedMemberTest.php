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

    $response->assertRedirect('/customer/dashboard');

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

    // Can access customer dashboard & booking
    $this->actingAs($user)->get('/customer/dashboard')->assertStatus(200);
    $this->actingAs($user)->get('/customer/bookings')->assertStatus(200);

    // Can also access student lms dashboard
    $this->actingAs($user)->get('/student/dashboard')->assertStatus(200);
});
