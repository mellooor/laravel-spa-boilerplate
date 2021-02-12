<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User
     *
     * User who is to be used in the tests.
     */
    protected $user;

    public function setUp(): void
    {
        parent::setUp(); // Required to include the TestCase set up code as well.

        $this->user = User::factory()->create(); // New user is created who will be used in the tests.
    }

    /**
     * @test
     *
     * Test to see if an authenticated use can log out successfully.
     *
     * @return void
     */
    public function loggedInUserCanLogOut()
    {
        $response = $this->actingAs($this->user)->postJson($this->apiBasePrefix . '/logout', []);

        // Check that the correct HTTP response is returned.
        $response->assertStatus(204);
    }
}
