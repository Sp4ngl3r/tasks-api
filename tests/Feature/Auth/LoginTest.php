<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('as a user, I can login and logout', function () {
    // Arrange
    User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => Hash::make('secret-password'),
    ]);

    // Act
    $response = $this->postJson(route('api.v1.auth.login'), [
        'email' => 'john@example.com',
        'password' => 'secret-password',
    ]);

    // Assert
    $response->assertSuccessful();
    $this->assertAuthenticated();
    $response->assertJsonStructure([
        'data' => [
            'message',
            'auth_token',
            'token_type',
            'expires_in',
        ]
    ]);
    expect($response->json('data.message'))->toBe('User successfully logged in.')
        ->and($response->json('data.auth_token'))->not->toBeNull()
        ->and($response->json('data.token_type'))->toBe('bearer')
        ->and($response->json('data.expires_in'))->toBe(3600);

    // Logout and assert
    $response = $this->postJson(route('api.v1.auth.logout'));
    $response->assertSuccessful();
    $this->assertGuest();
    expect($response->json('message'))->toBe('Successfully logged out');
});
