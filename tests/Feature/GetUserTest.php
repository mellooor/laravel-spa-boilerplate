<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetUserTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User
     *
     * User who is to be used in the tests.
     */
    protected $user;

    /** @var \App\Models\User
     *
     * Another user who is also to be used in
     * the tests.
     */
    protected $otherUser;

    public function setUp(): void
    {
        parent::setUp(); // Required to include the TestCase set up code as well.

        $this->user = User::factory()->create(); // New user is created who will be used in the tests.
        $this->otherUser = User::factory()->create(); // Another new user is created who will also be used in the tests.
    }

    /**
     * @test
     *
     * Test to see if a user's details can be retrieved
     * in JSON format when a user ID is passed to the
     * relevant route.
     *
     * @return void
     */
    public function canGetSingleUser()
    {
        $response = $this->actingAs($this->user)->getJson($this->apiBasePrefix . '/user/' . $this->user->id);

        // Check that the correct HTTP response is returned.
        $response->assertStatus(200);

        // Check that the correct JSON content is returned.
        $response->assertJson([
            'data' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'email_verified_at' => $this->user->email_verified_at->toISOString(),
                'created_at' => $this->user->created_at->toISOString(),
                'updated_at' => $this->user->updated_at->toISOString()
            ]
        ]);
    }

    /**
     * @test
     *
     * Test to see if a list of all the users that
     * are registered on the system can be retrieved
     * in JSON format when accessing the relevant route.
     *
     * @return void
     */
    public function canGetAllUsers()
    {
        $response = $this->actingAs($this->user)->getJson($this->apiBasePrefix . '/user');

        // Check that the correct HTTP response is returned.
        $response->assertStatus(200);

        // Check that the correct JSON content is returned.
        $response->assertJson([
            'data' => [
                [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                    'email_verified_at' => $this->user->email_verified_at->toISOString(),
                    'created_at' => $this->user->created_at->toISOString(),
                    'updated_at' => $this->user->updated_at->toISOString()
                ], [
                    'id' => $this->otherUser->id,
                    'name' => $this->otherUser->name,
                    'email' => $this->otherUser->email,
                    'email_verified_at' => $this->otherUser->email_verified_at->toISOString(),
                    'created_at' => $this->otherUser->created_at->toISOString(),
                    'updated_at' => $this->otherUser->updated_at->toISOString()
                ]
            ]
        ]);
    }

    /**
     * @test
     *
     * Test to see if a single user's details cannot be
     * retrieved if unauthenticated.
     *
     * @return void
     */
    public function cannotGetSingleUserIfNotLoggedIn()
    {
        $response = $this->getJson( $this->apiBasePrefix . '/user/' . $this->user->id);

        // Check that the correct HTTP response is returned.
        $response->assertStatus(401);

        // Check that the correct JSON content is returned.
        $response->assertJson([
            'message' => 'Unauthenticated.'
        ]);
    }

    /**
     * @test
     *
     * Test to see if the details of all of the users
     * cannot be retrieved if unauthenticated.
     *
     * @return void
     */
    public function cannotGetAllUsersIfNotLoggedIn()
    {
        $response = $this->getJson($this->apiBasePrefix . '/user');

        // Check that the correct HTTP response is returned.
        $response->assertStatus(401);

        // Check that the correct JSON content is returned.
        $response->assertJson([
            'message' => 'Unauthenticated.'
        ]);
    }

    /**
     * @test
     *
     * Test to see if a 422 HTTP response and an
     * error message in a JSON object are returned
     * when an incorrect user ID is passed to the
     * route that retrieves an individual user.
     *
     * @return void
     */
    public function invalidUserIDReturnsErrorForSingleUserGet()
    {
        $response = $this->actingAs($this->user)->getJson($this->apiBasePrefix . '/user/someInvalidParameter');

        // Check that the correct HTTP response is returned.
        $response->assertStatus(422);

        // Check that the correct JSON content is returned.
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'user' =>  ['User could not be found.']
            ]
        ]);
    }
}
