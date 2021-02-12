<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteAccountTest extends TestCase
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
     * Test to see if a user can delete their account.
     *
     * @return void
     */
    public function canDeleteAccount() {
        $response = $this->actingAs($this->user)->deleteJson($this->apiBasePrefix . '/user',);

        // Check that the correct HTTP response is returned.
        $response->assertStatus(200);

        // Check that the user has been deleted from the DB.
        $this->assertDeleted($this->user);
    }

    /**
     * @test
     *
     * Test to see if a 401 HTTP response is returned and
     * the user in question remains in the DB if the delete
     * user route is accessed when no user is currently
     * authenticated.
     *
     * @return void
     */
    public function cannotDeleteAccountIfNotLoggedIn() {
        $response = $this->deleteJson($this->apiBasePrefix . '/user',);

        // Check that the correct HTTP response is returned.
        $response->assertStatus(401);

        // Check that the user is still in the DB.
        $this->assertDatabaseHas('users', [
           'id' => $this->user->id
        ]);
    }
}
