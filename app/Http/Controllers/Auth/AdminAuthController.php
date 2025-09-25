<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $admin = Admin::where('username', $request->username)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()->json([
            'token' => $admin->createToken('admin-token', ['admin:access'])->plainTextToken,
            'admin' => $admin,
        ]);
    }

    public function logout(Request $request)
    {
        // Revoke all tokens (or just the current one)
        $request->user()->tokens()->delete();
        
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function checkAuth(Request $request)
    {
        return response()->json([
            'authenticated' => true,
            'admin' => $request->user()
        ]);
    }
}