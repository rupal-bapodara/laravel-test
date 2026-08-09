<?php

namespace Tests\Feature;

use Tests\TestCase;

uses(TestCase::class);

it('returns a successful response', function (): void {
    $response = $this->get('/');

    $response->assertStatus(200);
});
