<?php

use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can add a comment to their own task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();

    $payload = [
        'content' => 'This is a test comment',
    ];

    $response = $this->actingAs($user)->postJson("/api/tasks/{$task->id}/comments", $payload);

    $response->assertStatus(201)
        ->assertJsonPath('comment.content', 'This is a test comment');

    $this->assertDatabaseHas('comments', [
        'task_id' => $task->id,
        'user_id' => $user->id,
        'content' => 'This is a test comment',
    ]);
});

test('user cannot add comment to another users task', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    
    $task = Task::factory()->for($otherUser)->create();

    $payload = [
        'content' => 'Trying to comment on another task',
    ];

    $response = $this->actingAs($user)->postJson("/api/tasks/{$task->id}/comments", $payload);

    $response->assertStatus(403);
});

test('submitting a comment requires content', function () {
    $user = User::factory()->create();
    $task = Task::factory()->for($user)->create();

    $response = $this->actingAs($user)->postJson("/api/tasks/{$task->id}/comments", []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['content']);
});
