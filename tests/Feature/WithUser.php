<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class WithUser extends TestCase
{
    protected User $user;
    protected function setUp(): void
    {
        parent::setUp();

        User::query()->where('email', 'test@localhost')->forceDelete();
        $this->user = User::factory()->create([
            'email' => 'test@localhost'
        ]);
        $this->actingAs($this->user);
    }

    protected function tearDown(): void
    {
        $this->user->forceDelete();

        parent::tearDown();
    }
}