<?php
// app/Http/Controllers/UserAuthenticationController.php

namespace App\Http\Controllers;

use App\Models\UserAuthentication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserAuthenticationController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'age' => 'required|integer|min:18|max:100',
            'birthday' => 'required|date',
            'username' => 'required|string|max:255|unique:user_authentications',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = UserAuthentication::create([
            'full_name' => $request->full_name,
            'age' => $request->age,
            'birthday' => $request->birthday,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'status' => 'Active',
        ]);

        $token = $user->createToken('user-token')->plainTextToken;

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = UserAuthentication::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        if ($user->status !== 'Active') {
            return response()->json([
                'message' => 'Your account is ' . $user->status
            ], 403);
        }

        $token = $user->createToken('user-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    }

    public function checkAuth(Request $request)
    {
        return response()->json([
            'user' => $request->user()
        ], 200);
    }

    // Add this method to your UserAuthenticationController
    public function index(Request $request)
    {
        // Only allow admins to access this
        if (!$request->user() || !$request->user()->tokenCan('admin:access')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $users = UserAuthentication::all();
        return response()->json(['users' => $users]);
    }

    public function destroy(Request $request, $id)
    {
        // Only allow admins to access this
        if (!$request->user() || !$request->user()->tokenCan('admin:access')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = UserAuthentication::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->delete();
        return response()->json(['message' => 'User deleted successfully']);
    }

    public function updateStatus(Request $request, $id)
    {
        // Only allow admins to access this
        if (!$request->user() || !$request->user()->tokenCan('admin:access')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:Active,Inactive,Suspended'
        ]);

        $user = UserAuthentication::find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $user->status = $request->status;
        $user->save();

        return response()->json(['message' => 'Status updated successfully', 'user' => $user]);
    }
}