<?php

use App\Models\User;

test('as a user, I can register', function () {
    // Arrange
    expect(User::query()->count())->toBe(0);

    // Act
    $response = $this->postJson(route('api.v1.auth.register'), [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'secret-password',
    ]);

    // Assert
    $response->assertCreated();
    $response->assertJsonStructure([
        'message',
        'data' => [
            'id',
            'name',
            'email',
        ],
        'token',
        'token_type',
        'expires_in',
    ]);
    expect(User::query()->count())->toBe(1);
    $user = User::query()->first();
    expect($response->json('data.id'))->toBe($user->id)
        ->and($response->json('data.name'))->toBe($user->name)->toBe('John Doe')
        ->and($response->json('data.email'))->toBe($user->email)->toBe('john@example.com')
        ->and($response->json('token'))->not->toBeNull()
        ->and($response->json('token_type'))->toBe('bearer')
        ->and($response->json('expires_in'))->toBe(3600);
});
