<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can register', function () {
    $user = User::factory()->make();

    $this->postJson('/api/auth/register', [
        'name' => $user->name,
        'email' => $user->email,
        'password' => 'hahahaha',
        'password_confirmation' => 'hahahaha',
    ])
    ->assertStatus(201)
    ->assertJsonStructure([
        'message',
        'data' => ['token']
    ]);
});

test('user cannot register with invalid data', function () {
    $this->postJson('/api/auth/register', [
        'name' => '',
        'email' => 'not-an-email',
        'password' => 'short',
        'password_confirmation' => 'mismatch',
    ])
    ->assertStatus(422);
});

test('user cannot register with duplicate email', function () {
    User::factory()->create(['email' => 'kyle@email.com']);

    $this->postJson('/api/auth/register', [
        'name' => 'Kyle',
        'email' => 'kyle@email.com',
        'password' => 'hahahaha',
        'password_confirmation' => 'hahahaha',
    ])
    ->assertStatus(422);
});

test('user can login', function () {
    $user = User::factory()->create(['password' => 'hahahaha']);

    $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'hahahaha',
    ])
    ->assertStatus(200)
    ->assertJsonStructure([
        'message',
        'data' => ['token']
    ]);
});

test('user cannot login with wrong password', function () {
    $user = User::factory()->create();

    $this->postJson('/api/auth/login', [
        'email' => $user->email,
        'password' => 'wrongpassword',
    ])
    ->assertStatus(401);
});

test('user cannot login with nonexistent email', function () {
    $this->postJson('/api/auth/login', [
        'email' => 'ghost@email.com',
        'password' => 'hahahaha',
    ])
    ->assertStatus(401);
});

test('user can logout', function () {
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/auth/logout')
        ->assertStatus(200)
        ->assertJson(['message' => 'Logged out successfully!']);
});

test('unauthenticated user cannot logout', function () {
    $this->postJson('/api/auth/logout')
        ->assertStatus(401);
});

test('user can get own info', function () {
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/auth/me')
        ->assertStatus(200)
        ->assertJsonStructure([
            'data' => ['user']
        ]);
});

test('unauthenticated user cannot get own info', function () {
    $this->getJson('/api/auth/me')
        ->assertStatus(401);
});