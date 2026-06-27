<?php

use App\Models\Project;
use App\Models\SoloTask;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can retrieve dashboard data', function () {
    $user = User::factory()->create();
    
    SoloTask::factory(2)->create([
        'user_id' => $user->id,
        'status' => 'pending'
    ]);
    SoloTask::factory(3)->create([
        'user_id' => $user->id,
        'status' => 'done'
    ]);
    Project::factory(5)->create(['user_id' => $user->id]);

    $this->actingAs($user, 'sanctum')
         ->getJson('/api/dashboard')
         ->assertStatus(200)
         ->assertJsonPath('data.pending_tasks_count', 2)
         ->assertJsonPath('data.completed_tasks_count', 3)
         ->assertJsonPath('data.total_tasks_count', 5)
         ->assertJsonCount(5, 'data.recent_tasks')
         ->assertJsonCount(5, 'data.recent_projects');
});
