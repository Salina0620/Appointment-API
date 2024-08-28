<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;




class ApiController extends Controller
{
    public function register(Request $request)
    {
        $validateUser = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'role' => 'required|in:patient,doctor',
                'password' => 'required|confirmed',
            ]
        );

        if ($validateUser->fails()) {
            Log::info('Validation errors: ', $validateUser->errors()->toArray());
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validateUser->errors()
            ], 422);
        }

        $email = strtolower($request->email);

        $user = User::create([
            'name' => $request->name,
            'email' => $email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);



        return response()->json([
            'status' => true,
            'message' => 'User created successfully',
            'token' => $user->createToken("API TOKEN")->plainTextToken
        ], 200);
    }



    public function login(Request $request)
    {
        $validateUser = Validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required',
            ]
        );
        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'validation error',
                'errors' => $validateUser->errors()
            ], 422);
    }
    if(!Auth::attempt($request->only(['email', 'password']))){
        return response()->json([
            'status' => false,
            'message' => 'Email and password doesnot match with our record',
        ], 422);
    }
$user = User::where('email', $request->email)->first();
return response()->json([
    'status' => true,
    'message' => 'User logged in successfully',
    'token' => $user->createToken("API TOKEN")->plainTextToken
], 200);

}

public function logout(Request $request)
{
    // Ensure user is authenticated
    if (!Auth::check()) {
        return response()->json([
            'status' => false,
            'message' => 'No user is currently logged in',
        ], 401); // 401 Unauthorized
    }

    // Revoke all of the user's tokens
    Auth::user()->tokens()->delete();

    return response()->json([
        'status' => true,
        'message' => 'Logged out successfully',
    ], 200);
}

}