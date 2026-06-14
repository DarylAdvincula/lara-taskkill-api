<?php

use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can create a project task', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $projectTask = ProjectTask::factory()->make();

    $this->actingAs($user, 'sanctum')
         ->postJson('/api/projects/' . $project->id . '/project-tasks', [
            'title' => $projectTask->title,
            'description' => $projectTask->description,
            'due_date' => $projectTask->due_date,
         ])
         ->assertStatus(201);
});

test('user can view their project tasks', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    
    ProjectTask::factory(5)->create(['project_id' => $project->id]);
    
    $this->actingAs($user, 'sanctum')
         ->getJson('/api/projects/' . $project->id . '/project-tasks')
         ->assertStatus(200)
         ->assertJsonCount(5, 'data.tasks.data');
});

test('user can view one of their project tasks', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $projectTask = ProjectTask::factory()->create(['project_id' => $project->id]);

    $this->actingAs($user, 'sanctum')
         ->getJson('/api/project-tasks/' . $projectTask->id)
         ->assertStatus(200)
         ->assertJsonStructure(['data' => ['task']]);
});

test("user can't view others' project tasks", function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherProject = Project::factory()->create(['user_id' => $otherUser->id]);
    $otherProjectTask = ProjectTask::factory()->create(['project_id' => $otherProject->id]);

    $this->actingAs($user, 'sanctum')
         ->getJson('/api/project-tasks/' . $otherProjectTask->id)
         ->assertStatus(403);
});

test('user can update one of their project tasks', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $projectTask = ProjectTask::factory()->create(['project_id' => $project->id]);

    $this->actingAs($user, 'sanctum')
         ->patchJson('/api/project-tasks/' . $projectTask->id, [
            'title' => 'New Title'
         ])
         ->assertStatus(200);

    $this->assertDatabaseHas('project_tasks', [
        'project_id' => $project->id,
        'title' => 'New Title'
    ]);
});

test("user can't update others' project tasks", function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherProject = Project::factory()->create(['user_id' => $otherUser->id]);
    $otherProjectTask = ProjectTask::factory()->create(['project_id' => $otherProject->id]);

    $this->actingAs($user, 'sanctum')
         ->patchJson('/api/project-tasks/' . $otherProjectTask->id, [
            'title' => 'New Title'
         ])
         ->assertStatus(403);
});

test('user can delete one of their project tasks', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['user_id' => $user->id]);
    $projectTask = ProjectTask::factory()->create(['project_id' => $project->id]);

    $this->actingAs($user, 'sanctum')
         ->deleteJson('/api/project-tasks/' . $projectTask->id)
         ->assertStatus(200);
        
    $this->assertDatabaseMissing('project_tasks', ['id' => $projectTask->id]);
});

test("user can't delete others' project tasks", function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $otherProject = Project::factory()->create(['user_id' => $otherUser->id]);
    $otherProjectTask = ProjectTask::factory()->create(['project_id' => $otherProject->id]);

    $this->actingAs($user, 'sanctum')
         ->deleteJson('/api/project-tasks/' . $otherProjectTask->id)
         ->assertStatus(403);
});