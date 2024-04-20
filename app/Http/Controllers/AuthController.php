<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employer;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        return response()->json(compact('token'));
    }

    public function register(Request $request)
    {
        $employer = Employer::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'role' => $request->input('role'),
            'phone' => $request->input('phone'),
        ]);

        $token = JWTAuth::fromUser($employer);

        return response()->json(compact('token'));
    }

    public function checkSession(Request $request)
    {
        try {
            // Retrieve the authenticated user using the token from the request headers
            $user = JWTAuth::parseToken()->authenticate();

            // Return the user data
            return response()->json(["user" => $user], 200);
        } catch (\Exception $e) {
            // If an exception occurs (e.g., token invalid or expired), return an error response
            return response()->json(['error' => 'Unauthenticated'], 401);
        }
    }

    // public function logout(Request $request)
    // {
    //     $request->user()->token()->revoke();

    //     return response()->json(['message' => 'Successfully logged out']);
    // }

    public function logout()
    {
        Auth::logout();

        return response()->json(['message' => 'Successfully logged out']);
    }
}
