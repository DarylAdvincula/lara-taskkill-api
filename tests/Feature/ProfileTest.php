<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can update their profile', function () {
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->patchJson('/api/profile', ['name' => 'Technoblade'])
        ->assertStatus(200);
    
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Technoblade'
    ]);
});

test('user can change their password', function () {
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->patchJson('/api/profile/password', [
            'current_password' => 'password',
            'password' => 'hahahaha',
            'password_confirmation' => 'hahahaha'
        ])->assertStatus(200);
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $this->actingAs($user, 'sanctum')
        ->deleteJson('/api/profile', ['current_password' => 'password'])
        ->assertStatus(200);
    
    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});