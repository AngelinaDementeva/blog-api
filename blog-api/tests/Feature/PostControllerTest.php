<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_post(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $resp = $this->postJson('/api/posts', [
            'status' => 'active',
            'body' => 'Hello',
        ]);

        $resp->assertCreated()
             ->assertJsonPath('data.status', 'active');
    }
}
