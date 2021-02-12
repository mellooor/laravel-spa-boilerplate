<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class UpdatePasswordTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User
     *
     * User who is to be used in the tests.
     */
    protected $user;

    /** @var string
     *
     * The old password that is used in the tests.
     */
    protected $oldPassword = 'password';

    /** @var string
     *
     * The new password that is to be used in the tests.
     */
    protected $newPassword = 'password123';

    public function setUp(): void
    {
        parent::setUp(); // Required to include the TestCase set up code as well.

        $this->user = User::factory()->create(); // New user is created who will be used in the tests.
    }

    /**
     * @test
     *
     * Test to see if a user can update their password.
     *
     * @return void
     */
    public function canUpdatePassword()
    {
        $response = $this->actingAs($this->user)->putJson($this->apiBasePrefix . '/user/password', [
           'current_password' => $this->oldPassword,
           'password' => $this->newPassword,
           'password_confirmation' => $this->newPassword,
        ]);

        // Check that the correct HTTP response is returned.
        $response->assertStatus(200);

        // Check that the user's password has updated.
        $this->user->update();
        $this->assertTrue(Hash::check($this->newPassword, $this->user->password));
    }

    /**
     * @test
     *
     * Test to see if a user cannot update their password
     * if they supply the wrong value for existing
     * password.
     *
     * @return void
     */
    public function cannotUpdatePasswordWithIncorrectExistingPassword()
    {
        $response = $this->actingAs($this->user)->putJson($this->apiBasePrefix . '/user/password', [
            'current_password' => 'wrong_password',
            'password' => $this->newPassword,
            'password_confirmation' => $this->newPassword,
        ]);

        // Check that the correct HTTP response is returned.
        $response->assertStatus(422);

        // Check that the correct JSON content is returned.
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'current_password' => [ 'The provided password does not match your current password.' ]
            ]
        ]);

        // Check that the user's password hasn't changed.
        $this->user->refresh();
        $this->assertTrue(Hash::check($this->oldPassword, $this->user->password));
    }

    /**
     * @test
     *
     * Test to see if a user cannot update their password
     * if the new password and confirm password values
     * don't match.
     *
     * @return void
     */
    public function cannotUpdatePasswordIfNewPasswordsDontMatch()
    {
        $response = $this->actingAs($this->user)->putJson($this->apiBasePrefix . '/user/password', [
            'current_password' => $this->oldPassword,
            'password' => $this->newPassword,
            'password_confirmation' => $this->newPassword . 'isWrong',
        ]);

        // Check that the correct HTTP response is returned.
        $response->assertStatus(422);

        // Check that the correct JSON content is returned.
        $response->assertJson([
            'message' => 'The given data was invalid.',
            'errors' => [
                'password' => [ 'The password confirmation does not match.' ]
            ]
        ]);

        // Check that the user's password hasn't changed.
        $this->user->refresh();
        $this->assertTrue(Hash::check($this->oldPassword, $this->user->password));
    }

    /**
     * @test
     *
     * Test to see if a 401 error is returned if the
     * update password route is accessed and a user
     * isn't logged in.
     *
     * @return void
     */
    public function cannotUpdatePasswordIfNotLoggedIn()
    {
        $response = $this->putJson($this->apiBasePrefix . '/user/password', [
            'current_password' => $this->oldPassword,
            'password' => $this->newPassword,
            'password_confirmation' => $this->newPassword,
        ]);

        // Check that the correct HTTP response is returned.
        $response->assertStatus(401);

        // Check that the correct JSON content is returned.
        $response->assertJson([
            'message' => 'Unauthenticated.',
        ]);

        // Check that the user's password hasn't changed.
        $this->user->refresh();
        $this->assertTrue(Hash::check($this->oldPassword, $this->user->password));
    }
}
