<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\User as UserResource;

class UsersController extends Controller
{
    /**
     *  @OA\Get(
     *      path="/user",
     *      summary="Get All Users.",
     *      description="Retrieves all users from the DB.",
     *      operationId="getAllUsers",
     *      tags={"Users"},
     *      @OA\Response(
     *          response=200,
     *          description="Successful request.",
     *          @OA\JsonContent(
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/User")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="User not logged in.",
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
     *      )
     *  )
     *
     * Return a list of all users as a JSON object.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return UserResource::collection(User::all());
    }

    /**
     *  @OA\Get(
     *      path="/user/{userID}",
     *      summary="Get User",
     *      description="Retrieves a single user, based on the ID parameter that's passed.",
     *      operationId="getSingleUser",
     *      tags={"Users"},
     *      @OA\Response(
     *          response=200,
     *          description="Successful response.",
     *          @OA\JsonContent(
     *              @OA\Property(property="data", type="object", ref="#/components/schemas/User")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="User not logged in.",
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
     *     @OA\Response(
     *          response=422,
     *          description="Invalid user ID supplied.",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The given data was invalid."),
     *              @OA\Property(property="errors", type="object",
     *                  @OA\Property(property="user", type="array",
     *                      @OA\Items(
     *                          type="string",
     *                          example="User could not be found."
     *                      )
     *                  )
     *              )
     *          )
     *     )
     *  )
     *
     * Return the specified user as a JSON object.
     *
     * @param  int  $id
     * @return \App\Http\Resources\User|\Illuminate\Http\JsonResponse
     */
    public function fetch($id)
    {
        if ($user = User::find($id))
            return new UserResource($user);
        else
            return response()->json(['message' => 'The given data was invalid.', 'errors' => ['user' =>  ['User could not be found.']]], 422);
    }

    /**
     *  @OA\Delete(
     *      path="/user",
     *      summary="Delete User",
     *      description="Deletes the currently authenticated user.",
     *      operationId="deleteUser",
     *      tags={"Users"},
     *      @OA\Response(
     *          response=200,
     *          description="User succesfully deleted.",
     *          @OA\JsonContent(
     *              type="string",
     *              example=""
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="User not logged in.",
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
     *      )
     *  )
     *
     * Remove the specified user from the DB.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy()
    {
        if (Auth::user()) { // If the user is authenticated, delete them; otherwise return a 401 response.
            Auth::user()->delete();
            return response()->json([''], 200);
        } else
        {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
    }
}
