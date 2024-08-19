<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginValidation;
use App\Http\Requests\RegisterValidation;
use App\Models\User;
use Dotenv\Exception\ValidationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){
        try {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);
    } catch (\Exception $e) {
        dd($e->getMessage());
    }
        $user = User::create([
            "name" => $request->name,
            "email"=> $request->email,
            "password"=> bcrypt($request->password),
        ]);
        event (new Registered($user));
        return response()->json([
            'token' => $user->createToken('Personal Access Token')->plainTextToken,
        ]);
    }


    public function login(LoginValidation $request){
        if(!Auth::attempt($request->only('email','password'))){
            throw ValidationException::withMessages([
                'email'=> ["The provided credentials are incorrect"],
                ]);
    }
    $user = Auth::user();

    return response()->json([
        'token' => $user->createToken('Personal Access Token')->plainTextToken,
    ]);
}
}



