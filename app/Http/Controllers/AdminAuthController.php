<?php

namespace App\Http\Controllers;

use App\Mail\CodeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

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

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'errors' => [
                    $user ? 'password' : 'email' => $user ? 'Incorrect password.' : 'Invalid Email.',
                ],
            ], 422);
        }

        // ✅ Only send SMS if logged-in user is not admin
        if ($user->role != 0) {
            $admin = User::where('role', 0)->first();

            if ($admin && $admin->mobile) {
                $adminMobile = '880' . ltrim($admin->mobile, '0');

                $userName = $user->name;
                $ip = $request->ip();
                $time = now()->format('h:i A');
                $date = now()->format('d-m-Y');

                $message = "Dear ADMIN. User {$userName} has logged into your Accounts Book from IP {$ip} at {$time} on {$date}.";

                // Send the SMS
                sendSMS($adminMobile, $message);

                // 📧 Send Email
                Mail::raw($message, function ($mail) use ($admin) {
                    $mail->to($admin->email)
                        ->subject('New User Login Alert');
                });
            }
        }

        // Update last login datetime BEFORE login
        $user->last_login_at = now();
        $user->save();
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

    public static function forgotPass(){
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.auth.forgotPass');
    }

    public function sendResetCode(Request $request)
    {
        $request->validate([
            'email' => 'nullable|email|exists:users,email',
            'mobile' => 'nullable|numeric|exists:users,mobile',
        ]);

        $code = rand(100000, 999999);
        session(['reset_code' => $code]);
        session(['identifier' => $request->email ?? $request->phone]);

        // send via mail or sms (dummy)
        if ($request->email) {
            Mail::to($request->email)->send(new CodeMail($code));
        }

        if ($request->mobile) {
            $adminMobile = '880' . ltrim($request->mobile, '0');
            $code = session('reset_code');

            $message = "Hello, 
You requested to reset your password. Your password reset code is {$code}. This code will expire soon.";

            sendSMS($adminMobile, $message); // Assuming sendSMS() is globally available
        }

        return response()->json(['message' => 'Verification code sent successfully']);
    }

    public function verifyCode(Request $request)
    {
        $request->validate(['code' => 'required']);
        
        if ($request->code == session('reset_code')) {
            return response()->json(['message' => 'Code verified']);
        }

        return response()->json(['message' => 'Invalid code'], 422);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => 'required|confirmed|min:6'
        ]);

        $identifier = session('identifier');

        $user = User::where('email', $identifier)->orWhere('mobile', $identifier)->firstOrFail();
        $user->password = Hash::make($request->password);
        $user->save();

        session()->forget(['reset_code', 'identifier']);

        return redirect(route('login'))->with('success','Password updated successfully');
    }



}
