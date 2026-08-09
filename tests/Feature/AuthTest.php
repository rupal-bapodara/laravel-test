<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('registers a user and returns a token', function (): void {
    $response = $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'user' => ['id', 'name', 'email'],
            'token',
        ]);

    $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
});

it('logs in a user and returns a token', function (): void {
    $user = User::factory()->create(['password' => 'password']);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'user' => ['id', 'email'],
            'token',
        ]);
});

it('returns the authenticated user from me', function (): void {
    $user = User::factory()->create(['password' => 'password']);
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeaders([
        'Authorization' => "Bearer {$token}",
    ])->getJson('/api/me');

    $response->assertStatus(200)
        ->assertJson([
            'id' => $user->id,
            'email' => $user->email,
        ]);
});

it('logs out and revokes the token', function (): void {
    $user = User::factory()->create(['password' => 'password']);
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeaders([
        'Authorization' => "Bearer {$token}",
    ])->postJson('/api/logout');

    $response->assertStatus(200)
        ->assertJson(['message' => 'Logged out.']);

    $this->assertDatabaseCount('personal_access_tokens', 0);
});
