<?php

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can create a project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->make();

    $this->actingAs($user, 'sanctum')
        ->postJson('/api/projects', [
            'user_id' => $user->id,
            'name' => $project->name,
            'description' => $project->description
        ])
        ->assertStatus(201);
});

test('user can view their project', function () {
    $user = User::factory()->create();

    Project::factory(5)->create(['user_id' => $user->id]);
    
    $this->actingAs($user, 'sanctum')
        ->getJson('/api/projects')
        ->assertStatus(200)
        ->assertJsonCount(5, 'projects.data');
});

test('user can view one of their projects', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/projects/' . $project->id)
        ->assertStatus(200)
        ->assertJsonStructure(['data' => ['project']]);
});

test("user can't view others' projects", function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherProject = Project::factory()->create(['user_id' => $otherUser->id]);

    $this->actingAs($user, 'sanctum')
         ->getJson('/api/projects/' . $otherProject->id)
         ->assertStatus(403);
});

test('user can update one of their projects', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user, 'sanctum')
         ->patchJson('/api/projects/' . $project->id, [
            'name' => 'New Project'
         ])
         ->assertStatus(200);

    $this->assertDatabaseHas('projects', [
        'user_id' => $user->id,
        'name' => 'New Project'
    ]);
});


test("user can't update others' solo tasks", function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherProject = Project::factory()->create(['user_id' => $otherUser->id]);

    $this->actingAs($user, 'sanctum')
         ->patchJson('/api/projects/' . $otherProject->id, [
            'name' => 'New Project'
         ])
         ->assertStatus(403);
});


test('user can delete one of their projects', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user, 'sanctum')
         ->deleteJson('/api/projects/' . $project->id)
         ->assertStatus(200);
        
    $this->assertDatabaseMissing('projects', ['id' => $project->id]);
});

test("user can't delete others' projects", function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $otherUser->id]);

    $this->actingAs($user, 'sanctum')
         ->deleteJson('/api/projects/' . $project->id)
         ->assertStatus(403);
});