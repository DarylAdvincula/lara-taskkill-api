<?php

use App\Models\SoloTask;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can create a solo task', function () {
    $user = User::factory()->create();
    $soloTask = SoloTask::factory()->make();

    $this->actingAs($user, 'sanctum')
         ->postJson('/api/solo-tasks', [
            'title' => $soloTask->title,
            'description' => $soloTask->description,
            'due_date' => $soloTask->due_date,
         ])
         ->assertStatus(201);
});

test('user can view their solo tasks', function () {
    $user = User::factory()->create();

    SoloTask::factory(5)->create(['user_id' => $user->id]);
    
    $this->actingAs($user, 'sanctum')
         ->getJson('/api/solo-tasks')
         ->assertStatus(200)
         ->assertJsonCount(5, 'tasks.data');
});

test('user can view one of their solo tasks', function () {
    $user = User::factory()->create();
    $soloTask = SoloTask::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user, 'sanctum')
         ->getJson('/api/solo-tasks/' . $soloTask->id)
         ->assertStatus(200)
         ->assertJsonStructure(['data' => ['task']]);
});

test("user can't view others' solo tasks", function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherSoloTask = SoloTask::factory()->create(['user_id' => $otherUser->id]);

    $this->actingAs($user, 'sanctum')
         ->getJson('/api/solo-tasks/' . $otherSoloTask->id)
         ->assertStatus(403);
});

test('user can update one of their solo tasks', function () {
    $user = User::factory()->create();
    $soloTask = SoloTask::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user, 'sanctum')
         ->patchJson('/api/solo-tasks/' . $soloTask->id, [
            'title' => 'New Title'
         ])
         ->assertStatus(200);

    $this->assertDatabaseHas('solo_tasks', [
        'user_id' => $user->id,
        'title' => 'New Title'
    ]);
});

test("user can't update others' solo tasks", function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherSoloTask = SoloTask::factory()->create(['user_id' => $otherUser->id]);

    $this->actingAs($user, 'sanctum')
         ->patchJson('/api/solo-tasks/' . $otherSoloTask->id, [
            'title' => 'New Title'
         ])
         ->assertStatus(403);
});

test('user can delete one of their solo tasks', function () {
    $user = User::factory()->create();
    $soloTask = SoloTask::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user, 'sanctum')
         ->deleteJson('/api/solo-tasks/' . $soloTask->id)
         ->assertStatus(200);
        
    $this->assertDatabaseMissing('solo_tasks', ['id' => $soloTask->id]);
});

test("user can't delete others' solo tasks", function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherSoloTask = SoloTask::factory()->create(['user_id' => $otherUser->id]);

    $this->actingAs($user, 'sanctum')
         ->deleteJson('/api/solo-tasks/' . $otherSoloTask->id)
         ->assertStatus(403);
});