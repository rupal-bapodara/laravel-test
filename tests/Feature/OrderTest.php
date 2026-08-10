<?php

namespace Tests\Feature;

use App\Jobs\SendOrderSuccessEmail;
use App\Mail\OrderPlacedEmail;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('allows an authenticated user to place an order', function (): void {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeaders([
        'Authorization' => "Bearer {$token}",
    ])->postJson('/api/orders', [
        'product_name' => 'Test Product',
        'quantity' => 2,
        'total' => 49.99,
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure(['order' => ['id', 'product_name', 'quantity', 'total', 'status']]);

    $this->assertDatabaseHas('orders', [
        'user_id' => $user->id,
        'product_name' => 'Test Product',
        'quantity' => 2,
        'total' => 49.99,
    ]);
});

it('dispatches a SendOrderSuccessEmail job when placing an order', function (): void {
    Queue::fake();

    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $this->withHeaders([
        'Authorization' => "Bearer {$token}",
    ])->postJson('/api/orders', [
        'product_name' => 'Test Product',
        'quantity' => 2,
        'total' => 49.99,
    ])->assertStatus(201);

    Queue::assertPushed(SendOrderSuccessEmail::class, function (SendOrderSuccessEmail $job): bool {
        return $job->order->product_name === 'Test Product'
            && $job->order->quantity === 2
            && $job->order->total == 49.99;
    });
});

it('sends the order placed email when the SendOrderSuccessEmail job is handled', function (): void {
    Mail::fake();

    $order = Order::factory()->for(User::factory())->create();

    app(SendOrderSuccessEmail::class)->handle();

    Mail::assertSent(OrderPlacedEmail::class, function (OrderPlacedEmail $mail) use ($order): bool {
        return $mail->hasTo($order->user->email);
    });
});
