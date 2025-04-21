<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $req)
    {
        $req->validate([
            'name' => ['required', 'string', 'max:250'],
            'email' => ['required', 'email', 'max:250', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed'],
        ]);

        $user = new User();
        $user->name = $req->input('name');
        $user->email = $req->input('email');
        $user->password = Hash::make($req->input('password'));
        $user->role = $req->input('role', 'user'); 
        $user->save();

        // response back
        return response()->json([
            'result' => true,
            'message' => 'Registration successful',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]
        ]);
    }


    public function login(Request $req)
    {
        $req->validate([
            'email' => 'required|email|max:250',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $req->input('email'))->first(['id', 'name', 'email', 'password', 'role']);

        if (!$user) {
            return response()->json(['result' => false, "message" => 'Invalid email', "data" => []]);
        }

        if (!Hash::check($req->input('password'), $user->password)) {
            return response()->json(['result' => false, "message" => 'Incorrect password', "data" => []]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        // response back
        return response()->json([
            'result' => true,
            'message' => 'Login successful',
            'token' => $token,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]
        ]);
    }

    public function logout(Request $req)
    {
        $req->user('sanctum')->currentAccessToken()->delete();
        return response()->json([
            'result' => true,
            'message' => 'Logout successful',
            'data' => []
        ]);
    }

    public function getMe(Request $req)
    {
        $user = $req->user('sanctum');
        return response()->json([
            'result' => true,
            'message' => 'User retrieved successfully',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role
            ]
        ]);
    }
}
