<?php

use App\Enums\Status;
use App\Models\Task;
use App\Models\User;

test('as an authenticated user, I can create a task', function () {
    // Arrange
    $user = User::factory()->create();
    expect($user->tasks)->toHaveCount(0);

    // Act
    $response = $this
        ->actingAs($user)
        ->postJson(route('api.v1.tasks.store'), [
            'title' => 'Test Task',
            'description' => 'This is a test task.',
            'status' => 'pending',
        ]);

    // Assert
    $response->assertCreated();
    $response->assertJsonStructure([
        'data' => [
            'id',
            'title',
            'description',
            'status',
        ],
    ]);
    expect($user->refresh()->tasks)->toHaveCount(1);

    $task = Task::query()->first();
    expect($response->json('data.id'))->toBe($task->id)
        ->and($response->json('data.title'))->toBe($task->title)
        ->and($response->json('data.description'))->toBe($task->description)
        ->and($response->json('data.status'))->toBe($task->status->value);
});

test('as an authenticated user, I can view a task', function () {
    // Arrange
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'title' => 'Test Task',
        'description' => 'This is a test task.',
        'status' => Status::PENDING,
        'user_id' => $user->id,
    ]);

    // Act
    $response = $this
        ->actingAs($user)
        ->getJson(route('api.v1.tasks.show', $task));

    // Assert
    $response->assertSuccessful();
    $response->assertJsonStructure([
        'data' => [
            'id',
            'title',
            'description',
            'status',
        ],
    ]);
    expect($response->json('data.id'))->toBe($task->id)
        ->and($response->json('data.title'))->toBe($task->title)
        ->and($response->json('data.description'))->toBe($task->description)
        ->and($response->json('data.status'))->toBe($task->status->value);
});

test('as an authenticated user, I can update a task', function () {
    // Arrange
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'title' => 'Test Task',
        'description' => 'This is a test task.',
        'status' => Status::PENDING,
        'user_id' => $user->id,
    ]);

    // Act
    $response = $this
        ->actingAs($user)
        ->putJson(route('api.v1.tasks.update', $task), [
            'title' => 'Updated Task',
            'description' => 'This is an updated task.',
            'status' => 'completed',
        ]);

    // Assert
    $response->assertSuccessful();
    $response->assertJson([
        'message' => 'Task updated successfully.',
    ]);
    $task = $task->refresh();
    expect($task->title)->toBe('Updated Task')
        ->and($task->description)->toBe('This is an updated task.')
        ->and($task->status)->toBe(Status::COMPLETED);
});

test('as an authenticated user, I can delete a task', function () {
    // Arrange
    $user = User::factory()->create();
    $task = Task::factory()->create([
        'title' => 'Test Task',
        'description' => 'This is a test task.',
        'status' => Status::PENDING,
        'user_id' => $user->id,
    ]);

    // Act
    $response = $this
        ->actingAs($user)
        ->deleteJson(route('api.v1.tasks.destroy', $task));

    // Assert
    $response->assertSuccessful();
    $response->assertJson([
        'message' => 'Task deleted successfully.',
    ]);
    expect($user->refresh()->tasks)->toHaveCount(0);
});

test('as an authenticated user, I cannot view a task that does not belong to me', function () {
    // Arrange
    $user1 = User::factory()->create();
    $task = Task::factory()->create([
        'title' => 'Test Task',
        'description' => 'This is a test task.',
        'status' => Status::PENDING,
        'user_id' => $user1->id,
    ]);
    $user2 = User::factory()->create();

    // Act
    $response = $this
        ->actingAs($user2)
        ->getJson(route('api.v1.tasks.show', $task));

    // Assert
    $response->assertForbidden();
});

test('as an authenticated user, I cannot update a task that does not belong to me', function () {
    // Arrange
    $user1 = User::factory()->create();
    $task = Task::factory()->create([
        'title' => 'Test Task',
        'description' => 'This is a test task.',
        'status' => Status::PENDING,
        'user_id' => $user1->id,
    ]);
    $user2 = User::factory()->create();

    // Act
    $response = $this
        ->actingAs($user2)
        ->putJson(route('api.v1.tasks.update', $task), [
            'title' => 'Updated Task',
            'description' => 'This is an updated task.',
            'status' => 'completed',
        ]);

    // Assert
    $response->assertForbidden();
});

test('as an authenticated user, I cannot delete a task that does not belong to me', function () {
    // Arrange
    $user1 = User::factory()->create();
    $task = Task::factory()->create([
        'title' => 'Test Task',
        'description' => 'This is a test task.',
        'status' => Status::PENDING,
        'user_id' => $user1->id,
    ]);
    $user2 = User::factory()->create();

    // Act
    $response = $this
        ->actingAs($user2)
        ->deleteJson(route('api.v1.tasks.destroy', $task));

    // Assert
    $response->assertForbidden();
});

test('as an authenticated user, I can view all tasks', function () {
    // Arrange
    $user = User::factory()->create();
    Task::factory()->count(5)->create([
        'title' => 'Test Task',
        'description' => 'This is a test task.',
        'status' => Status::PENDING,
        'user_id' => $user->id,
    ]);

    // Act
    $response = $this
        ->actingAs($user)
        ->getJson(route('api.v1.tasks.index'));

    // Assert
    $response->assertSuccessful();
    $response->assertJsonStructure([
        'data' => [
            [
                'id',
                'title',
                'description',
                'status',
            ],
        ],
    ]);
    expect($response->json('data'))->toHaveCount(5);
});

test('as a random user, I cannot view any tasks', function () {
    // Arrange
    User::factory()->create();

    // Act
    $response = $this->getJson(route('api.v1.tasks.index'));

    // Assert
    $response->assertUnauthorized();
});
