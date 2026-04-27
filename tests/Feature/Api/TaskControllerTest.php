<?php

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can list their own tasks', function () {
    $user = User::factory()->create();
    Task::factory()->count(3)->for($user)->create();
    
    // Create another user's task
    Task::factory()->create();

    $response = $this->actingAs($user)->getJson('/api/tasks');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

test('user can filter tasks by status', function () {
    $user = User::factory()->create();
    Task::factory()->for($user)->create(['status' => TaskStatus::PENDING]);
    Task::factory()->for($user)->create(['status' => TaskStatus::COMPLETED]);

    $response = $this->actingAs($user)->getJson('/api/tasks?status=' . TaskStatus::COMPLETED->value);

    $response->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.status', TaskStatus::COMPLETED->value);
});

test('user can create a task', function () {
    $user = User::factory()->create();

    $payload = [
        'title' => 'New Task',
        'description' => 'Task description',
        'status' => TaskStatus::PENDING->value,
    ];

    $response = $this->actingAs($user)->postJson('/api/tasks', $payload);

    $response->assertStatus(201)
        ->assertJsonPath('data.title', 'New Task');

    $this->assertDatabaseHas('tasks', [
        'title' => 'New Task',
        'user_id' => $user->id,
    ]);
});

test('user cannot view a task they do not own', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    
    $task = Task::factory()->for($otherUser)->create();

    $response = $this->actingAs($user)->getJson("/api/tasks/{$task->id}");

    $response->assertStatus(403);
});

test('user can update their own task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create(['title' => 'Old Title']);

    $response = $this->actingAs($user)->putJson("/api/tasks/{$task->id}", [
        'title' => 'New Title',
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.title', 'New Title');

    $this->assertDatabaseHas('tasks', [
        'id' => $task->id,
        'title' => 'New Title',
    ]);
});

test('user can delete their own task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();

    $response = $this->actingAs($user)->deleteJson("/api/tasks/{$task->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});
