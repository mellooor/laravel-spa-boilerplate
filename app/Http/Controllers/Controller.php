<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 *  @OA\Info(
 *      title="Time and Motion SPA",
 *      version="1.0.0",
 *  )
 *
 *  @OA\Server(
 *      url="http://api.time-and-motion-spa.test/v1",
 *      description="API Domain"
 *  )
 *
 *  Laravel Fortify End Points are documented here as the majority
 *  are located in the vendor sub directory.
 *
 *  @OA\Post(
 *     path="/login",
 *     summary="Login",
 *     description="Authenticates a user from supplied credentials.",
 *     operationId="login",
 *     tags={"Users"},
 *     @OA\RequestBody(
 *          required=true,
 *          description="Pass user login credentials",
 *          @OA\JsonContent(
 *              required={"email","password"},
 *              @OA\Property(property="email", type="string", format="email", example="tparker@example.com"),
 *              @OA\Property(property="password", type="string", format="password", example="password"),
 *          )
 *     ),
 *     @OA\Response(
 *          response=200,
 *          description="Successful login.",
 *          @OA\JsonContent(
 *              @OA\Property(property="two_factor", type="boolean", example="false")
 *          )
 *     ),
 *     @OA\Response(
 *          response=419,
 *          description="CSRF token mismatch.",
 *          @OA\JsonContent(
 *              @OA\Property(property="message", type="string", example="CSRF token mismatch."),
 *          )
 *     ),
 *     @OA\Response(
 *          response=422,
 *          description="Invalid login credentials.",
 *          @OA\JsonContent(
 *              @OA\Property(property="message", type="string", example="The given data was invalid."),
 *              @OA\Property(property="errors", type="object",
 *                  @OA\Property(property="email", type="array",
 *                      @OA\Items(
 *                          type="string",
 *                          example="These credentials do not match our records."
 *                      )
 *                  )
 *              )
 *          )
 *     )
 *  )
 *
 *  @OA\Post(
 *      path="/register",
 *      summary="Register",
 *      description="Register a user.",
 *      operationId="register",
 *      tags={"Users"},
 *      @OA\RequestBody(
 *          required=true,
 *          description="Pass user registration credentials",
 *          @OA\JsonContent(
 *              required={"name", "email","password","password_confirmation"},
 *              @OA\Property(property="name", type="string", example="Tony Parker"),
 *              @OA\Property(property="email", type="string", format="email", example="tparker@example.com"),
 *              @OA\Property(property="password", type="string", format="password", example="password"),
 *              @OA\Property(property="password_confirmation", type="string", format="password", example="password"),
 *          ),
 *      ),
 *      @OA\Response(
 *          response=201,
 *          description="Successful registration request.",
 *          @OA\JsonContent(
 *              type="string",
 *              example="",
 *          )
 *      ),
 *      @OA\Response(
 *          response=419,
 *          description="CSRF token mismatch.",
 *          @OA\JsonContent(
 *              @OA\Property(property="message", type="string", example="CSRF token mismatch.")
 *          )
 *      ),
 *      @OA\Response(
 *          response=422,
 *          description="Invalid registration credentials.",
 *          @OA\JsonContent(
 *              @OA\Property(property="message", type="string", example="The given data was invalid."),
 *              @OA\Property(property="errors", type="object",
 *                  @OA\Property(property="email", type="array",
 *                      @OA\Items(
 *                          type="string",
 *                          example="The email must be a valid email address."
 *                      )
 *                  )
 *              )
 *          )
 *      )
 *  )
 *
 *  @OA\Post(
 *      path="/logout",
 *      summary="Log out",
 *      description="Log out the authenticated user.",
 *      operationId="logout",
 *      tags={"Users"},
 *      @OA\Response(
 *          response=204,
 *          description="Successfully logged out."
 *      ),
 *      @OA\Response(
 *          response=419,
 *          description="CSRF token mismatch.",
 *          @OA\JsonContent(
 *              @OA\Property(property="message", type="string", example="CSRF token mismatch.")
 *          )
 *      ),
 *  )
 *
 *  @OA\Post(
 *      path="/forgot-password",
 *      summary="Forgot password.",
 *      description="Sends out an email for a user to reset their password upon a valid email being passed.",
 *      operationId="forgotPassword",
 *      tags={"Users"},
 *      @OA\RequestBody(
 *          required=true,
 *          description="The email address of the user who has forgotten their password.",
 *          @OA\JsonContent(
 *              required={"email"},
 *              @OA\Property(property="email", type="string", format="email", example="tparker@example.com")
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Succesful forgot password request.",
 *          @OA\JsonContent(
 *              @OA\Property(property="message", type="string", example="We have emailed your password reset link!")
 *          )
 *      ),
 *      @OA\Response(
 *          response=419,
 *          description="CSRF token mismatch.",
 *          @OA\JsonContent(
 *              @OA\Property(property="message", type="string", example="CSRF token mismatch.")
 *          )
 *      ),
 *      @OA\Response(
 *          response=422,
 *          description="Invalid email supplied or password request link already sent.",
 *          @OA\JsonContent(
 *              @OA\Property(property="message", type="string", example="The given data was invalid."),
 *              @OA\Property(property="errors", type="object",
 *                  @OA\Property(property="email", type="array",
 *                      @OA\Items(
 *                          type="string",
 *                          example="We can't find a user with that email address."
 *                      )
 *                  )
 *              )
 *          )
 *      ),
 *      @OA\Response(
 *          response=500,
 *          description="Internal error (most likely caused by email server issue).",
 *          @OA\JsonContent(
 *              @OA\Property(property="message", type="string", example="Server Error")
 *          )
 *      )
 *  )
 *
 *  @OA\Post(
 *      path="/reset-password",
 *      summary="Reset password",
 *      description="Reset a user's password.",
 *      operationId="resetPassword",
 *      tags={"Users"},
 *      @OA\RequestBody(
 *          required=true,
 *          description="User email, new password with confirmation and email token.",
 *          @OA\JsonContent(
 *              required={"email", "password", "password_confirmation", "token"},
 *              @OA\Property(property="email", type="string", format="email", example="tparker@example.com"),
 *              @OA\Property(property="password", type="string", format="password", example="password"),
 *              @OA\Property(property="password_confirmation", type="string", format="password", example="password"),
 *              @OA\Property(property="token", type="string", example="96c482d50cf8e1071ad73286c822e70b8a71dfe3c5241887d599a95c08f25680")
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Succesfully reset password.",
 *          @OA\JsonContent(
 *              @OA\Property(property="message", type="string", example="Your password has been reset!")
 *          )
 *      ),
 *      @OA\Response(
 *          response=419,
 *          description="CSRF token mismatch.",
 *          @OA\JsonContent(
 *              @OA\Property(property="message", type="string", example="CSRF token mismatch.")
 *          )
 *      ),
 *      @OA\Response(
 *          response=422,
 *          description="Invalid form data passed (i.e. passwords don't match, email token is incorrect etc.).",
 *          @OA\JsonContent(
 *              @OA\Property(property="message", type="string", example="The given data was invalid."),
 *              @OA\Property(property="errors", type="object",
 *                  @OA\Property(property="email", type="array",
 *                      @OA\Items(
 *                          type="string",
 *                          example="This password reset token is invalid."
 *                      )
 *                  )
 *              )
 *          )
 *      )
 *  )
 *
 *  @OA\Put(
 *      path="/user/password",
 *      summary="Update password",
 *      description="Update the user's password.",
 *      operationId="updatePassword",
 *      tags={"Users"},
 *      @OA\RequestBody(
 *          required=true,
 *          description="The user's current password as well as a replacement password that's been confirmed.",
 *          @OA\JsonContent(
 *              required={"current_password", "password", "password_confirmation"},
 *              @OA\Property(property="current_password", type="string", format="password", example="password"),
 *              @OA\Property(property="password", type="string", format="password", example="password1"),
 *              @OA\Property(property="password_confirmation", type="string", format="password", example="password1"),
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Password successfully updated.",
 *          @OA\JsonContent(
 *              type="string",
 *              example=""
 *          )
 *      ),
 *      @OA\Response(
 *          response=401,
 *          description="Not logged in (or Referer request header is missing).",
 *          @OA\JsonContent(
 *              @OA\Property(property="message", type="string", example="Unauthenticated.")
 *          )
 *      ),
 *      @OA\Response(
 *          response=419,
 *          description="CSRF token mismatch.",
 *          @OA\JsonContent(
 *              @OA\Property(property="message", type="string", example="CSRF token mismatch.")
 *          )
 *      ),
 *      @OA\Response(
 *          response=422,
 *          description="Invalid form data passed (i.e. incorrect current password).",
 *          @OA\JsonContent(
 *              @OA\Property(property="message", type="string", example="The given data was invalid."),
 *              @OA\Property(property="errors", type="object",
 *                  @OA\Property(property="current_password", type="array",
 *                      @OA\Items(
 *                          type="string",
 *                          example="The provided password does not match your current password."
 *                      )
 *                  )
 *              )
 *          )
 *      )
 *  )
 *
 *  @OA\Put(
 *      path="/user/profile-information",
 *      summary="Update user profile",
 *      description="Update user's profile information.",
 *      operationId="updateProfileInfo",
 *      tags={"Users"},
 *      @OA\RequestBody(
 *          required=true,
 *          description="The user's profile information with the updates included where required.",
 *          @OA\JsonContent(
 *              required={"name", "email"},
 *              @OA\Property(property="name", type="string", example="Tony Parker"),
 *              @OA\Property(property="email", type="string", format="email", example="tparker@example.com")
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Successfully update user profile information.",
 *          @OA\JsonContent(
 *              type="string",
 *              example=""
 *          )
 *      ),
 *     @OA\Response(
 *          response=401,
 *          description="Not logged in.",
 *          @OA\JsonContent(
 *              @OA\Property(property="message", type="string", example="Unauthenticated.")
 *          )
 *      ),
 *     @OA\Response(
 *          response=419,
 *          description="CSRF token mismatch.",
 *          @OA\JsonContent(
 *              @OA\Property(property="message", type="string", example="CSRF token mismatch.")
 *          )
 *      ),
 *     @OA\Response(
 *          response=422,
 *          description="Invalid form data passed (i.e. some profile information is missing or empty).",
 *          @OA\JsonContent(
 *              @OA\Property(property="message", type="string", example="The given data was invalid."),
 *              @OA\Property(property="errors", type="object",
 *                  @OA\Property(property="email", type="array",
 *                      @OA\Items(
 *                          type="string",
 *                          example="The email must be a valid email address."
 *                      )
 *                  )
 *              )
 *          )
 *      ),
 *  )
 */

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
