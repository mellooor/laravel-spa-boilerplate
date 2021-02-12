<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User
     *
     * User who is to be used in the tests.
     */
    protected $user, $otherUser;

    /** @var string
     *
     * The user's new name that is to be used in the tests.
     */
    protected $newName = 'New Newingson';

    public function setUp(): void
    {
        parent::setUp(); // Required to include the TestCase set up code as well.

        $this->user = User::factory()->create(); // New user is created who will be used in the tests.
        $this->otherUser = User::factory()->create(); // Another user is created who will be used in the tests.
    }

    /**
     * @test
     *
     * Test to see if a user can update their profile.
     *
     * @return void
     */
    public function canUpdateProfile()
    {
        $response = $this->actingAs($this->user)->putJson($this->apiBasePrefix . '/user/profile-information', [
            'name' => $this->newName,
            'email' => $this->user->email,
        ]);

        // Check that the correct HTTP response is returned.
        $response->assertStatus(200);

        // Check that the user's details have updated.
        $this->user->refresh();
        $this->assertTrue($this->user->name === $this->newName);
    }

    /**
     * @test
     *
     * Test to see if a user cannot update their profile
     * if there is no value for the name attribute in the
     * request form data.
     *
     * @return void
     */
    public function cannotUpdateProfileWithNameMissing()
    {
        $response = $this->actingAs($this->user)->putJson($this->apiBasePrefix . '/user/profile-information', [
            'name' => '',
            'email' => $this->user->email,
        ]);

        // Check that the correct HTTP response is returned.
        $response->assertStatus(422);

        // Check that the correct JSON content is returned.
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'name' => [ 'The name field is required.' ]
            ]
        ]);

        // Check that the user's details haven't changed.
        $this->user->refresh();
        $this->assertFalse($this->user->name === $this->newName);
    }

    /**
     * @test
     *
     * Test to see if a user cannot update their profile
     * if there is no value for the email attribute in the
     * request form data.
     *
     * @return void
     */
    public function cannotUpdateProfileWithEmailMissing()
    {
        $response = $this->actingAs($this->user)->putJson($this->apiBasePrefix . '/user/profile-information', [
            'name' => $this->newName,
            'email' => '',
        ]);

        // Check that the correct HTTP response is returned.
        $response->assertStatus(422);

        // Check that the correct JSON content is returned.
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'email' => [ 'The email field is required.' ]
            ]
        ]);

        // Check that the user's details haven't changed.
        $this->user->refresh();
        $this->assertFalse($this->user->name === $this->newName);
    }

    /**
     * @test
     *
     * Test to see if a user cannot update their profile
     * if they try to change their email address to one that
     * is already in use.
     *
     * @return void
     */
    public function cannotUpdateProfileWithInvalidEmail()
    {
        $response = $this->actingAs($this->user)->putJson($this->apiBasePrefix . '/user/profile-information', [
            'name' => $this->newName,
            'email' => $this->otherUser->email,
        ]);

        // Check that the correct HTTP response is returned.
        $response->assertStatus(422);

        // Check that the correct JSON content is returned.
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'email' => [ 'The email has already been taken.' ]
            ]
        ]);

        // Check that the user's details haven't updated.
        $this->user->refresh();
        $this->assertFalse($this->user->name === $this->newName);
    }

    /**
     * @test
     *
     * Test to see if a 401 error is returned if the
     * update profile route is accessed and a user
     * isn't logged in.
     *
     * @return void
     */
    public function cannotUpdateProfileIfNotLoggedIn()
    {
        $response = $this->putJson($this->apiBasePrefix . '/user/profile-information', [
            'name' => $this->newName,
            'email' => $this->user->email,
        ]);

        // Check that the correct HTTP response is returned.
        $response->assertStatus(401);

        // Check that the correct JSON content is returned.
        $response->assertJson([
            'message' => 'Unauthenticated.',
        ]);
    }
}
