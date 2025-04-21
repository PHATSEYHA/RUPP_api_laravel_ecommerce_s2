<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User; // Import the User model

class UserController extends Controller
{
    public function update(Request $req)
    {
        $user = auth()->user();

        $req->validate([
            'name' => 'nullable|string|max:250',
            'local_name' => 'nullable|string|max:250',
            'gender' => 'nullable|in:1,2', // Only 1 (male) or 2 (female)
        ]);

        if ($req->has('name')) {
            $user->name = $req->input('name');
        }
        if ($req->has('local_name')) {
            $user->local_name = $req->input('local_name');
        }
        if ($req->has('gender')) {
            $user->gender = $req->input('gender');
        }

        $user->save();

        return response()->json([
            'result' => true,
            'message' => 'User updated successfully',
            'data' => [
                'name' => $user->name,
                'local_name' => $user->local_name,
                'gender' => $user->gender,
            ]
        ]);
    }

    public function updateSetting(Request $req)
    {
        $user = auth()->user();

        $req->validate([
            'email' => 'nullable|email|max:250|unique:users,email,' . $user->id,
            'old_password' => 'required_with:password|string',
            'password' => 'nullable|string|confirmed|min:6',
        ]);

        if ($req->has('email')) {
            $user->email = $req->input('email');
        }

        if ($req->has('password')) {
            // Verify old password before changing
            if (!Hash::check($req->input('old_password'), $user->password)) {
                return response()->json([
                    'result' => false,
                    'message' => 'Old password is incorrect',
                ], 400);
            }

            // If old password is correct, update the new password
            $user->password = Hash::make($req->input('password'));
        }

        $user->save();

        return response()->json([
            'result' => true,
            'message' => 'User updated successfully',
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
            ]
        ]);
    }

    public function index()
    {
        $user = auth()->user();
    
        // If the request passes through the IsAdmin middleware, the user is an admin
        if ($user->role === 'admin') {
            // Admins can view all users
            $users = User::select('id', 'name', 'local_name', 'email', 'gender', 'created_at')
                ->get();
    
            return response()->json([
                'result' => true,
                'message' => 'All user profiles retrieved successfully',
                'data' => $users
            ]);
        }
    
        // Regular users can only view their own profile
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'local_name' => $user->local_name,
            'email' => $user->email,
            'gender' => $user->gender,
            'created_at' => $user->created_at,
        ];
    
        return response()->json([
            'result' => true,
            'message' => 'Your profile retrieved successfully',
            'data' => $userData
        ]);
    }
}