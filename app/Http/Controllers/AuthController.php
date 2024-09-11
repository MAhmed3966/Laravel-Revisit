<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginValidation;
use App\Http\Requests\RegisterValidation;
use App\Mail\WelcomeEmail;
use App\Models\User;
use Dotenv\Exception\ValidationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="This is an Auth Controller",
 *         version="1.0.0",
 *         description="To Register and Log User in",
 *         @OA\Contact(
 *             email="m.ahmed3966@gmail.com"
 *         ),
 *         @OA\License(
 *             name="Apache 2.0",
 *             url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *         )
 *     )
 * )
 */
class AuthController extends BaseController
{

    /**
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     description="Create a new user account and return an access token.",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password1234"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User Created Successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User Created Successfully"),
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="User Registration Unsuccessful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User Registration Unsuccessful"),
     *             @OA\Property(property="error", type="string", example="Error message here")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="name",
     *                     type="array",
     *                     @OA\Items(type="string", example="The name field is required.")
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="array",
     *                     @OA\Items(type="string", example="The email has already been taken.")
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="array",
     *                     @OA\Items(type="string", example="The password must be at least 8 characters.")
     *                 )
     *             )
     *         )
     *      )
     * )
     */
    public function register(Request $request)
    {

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
            ]);
            $user = User::create([
                "name" => $request->name,
                "email" => $request->email,
                "password" => Hash::make($request->password),
            ]);
            Mail::to("m.ahmed3966@gmail.com")->send(new WelcomeEmail($user->name));
            return response()->json([
                "user" => Auth::user(),
                "message" => "User Created Successfully",
                'token' => $user->createToken('Personal Access Token')->plainTextToken,
            ]);
        } catch (\Exception $e) {
            return response()->json(["message" => __("User Registeration unsuccessful"), "error" => $e->getMessage()]);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/login",
     *     summary="User Login",
     *     description="Login a User",
     *     tags={"Login User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", example="m.ahmed3966@gmail.com"),
     *             @OA\Property(property="password", type="string", format="password", example="12345678")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User Logged In successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User logged In successfully"),
     *             @OA\Property(property="token", type="string", example="dasgre1fe13qfr23fevqr32e....")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="The given data was invalid"
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="email",
     *                     type="array",
     *                     @OA\Items(
     *                         type="string",
     *                         example="The provided credentials are incorrect"
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */

    public function login(LoginValidation $request)
    {
        try {

            $user = User::where('email',$request->email)->first();
            if(!$user || !Hash::check($request->password, $user->password)){
                throw ValidationException::withMessages([
                    'email' => ['Invalid Credentials'],
                ]);
            }

            $token = $user->createToken('Person Access Token')->plainTextToken;

            return $this->successResponse(["token" => $token], "User logged In successfully");

        } catch (\Exception $e) {
                return $this->errorResponse('',$e->getMessage(), 500);
        }
    }
}
