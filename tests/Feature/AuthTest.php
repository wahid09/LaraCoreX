<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Ensure default hashing is applied
    \Illuminate\Support\Facades\Hash::setRounds(4);
});

it('can register a new user', function () {
    $response = $this->post('/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect('/'); // or route after registration
    $this->assertDatabaseHas('users', [
        'email' => 'john@example.com',
    ]);
});

it('fails to register with invalid data', function () {
    $response = $this->post('/register', [
        'name' => '',
        'email' => 'not-an-email',
        'password' => 'short',
    ]);

    $response->assertSessionHasErrors(['name', 'email', 'password']);
});

it('can login with correct credentials', function () {
    $user = User::factory()->create([
        'email' => 'jane@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->post('/login', [
        'email' => 'jane@example.com',
        'password' => 'password123',
    ]);

    $response->assertRedirect('/'); // or intended route
    $this->assertAuthenticatedAs($user);
});

it('fails to login with wrong credentials', function () {
    $user = User::factory()->create([
        'email' => 'mike@example.com',
        'password' => bcrypt('correct-password'),
    ]);

    $response = $this->from('/login')->post('/login', [
        'email' => 'mike@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});
