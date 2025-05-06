<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminAuthController extends Controller
{
    public static function login()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.login');
    }

    public static function authenticate(Request $request)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email', // Check if email exists in instructors table
            'password' => 'required',
        ], [
            'email.exists' => 'Invalid Email Address.', // Custom error message for email not found
        ]);

        // If validation fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check email and password
        $user = User::where('email', $request->email)->first();

        if($user && $user->role != 0 ){
            return response()->json([
                'success' => false,
                'errors' => ['authorization' => 'You are not Authorized to access Admin Panel'],
            ], 422);
        }
        

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'errors' => [
                    $user ? 'password' : 'email' => $user ? 'Incorrect password.' : 'Invalid Email.',
                ],
            ], 422);
        }

        // Authenticate the user with the instructor guard
        Auth::guard('admin')->login($user);


        return response()->json([
            'success' => true,
            'message' => 'Admin Login successful!',
            'redirect' => route('admin.dashboard'),
        ]);
    }

    public static function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('login');
    }
}
