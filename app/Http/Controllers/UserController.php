<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        // Check if the user has permission to access this page
        if (auth()->user()->access->admin_panel == 3) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to access this page.');
        }
        // Fetch all users
        $users = User::where('id', '!=', '1' )->get();

        // Return the view with the users data
        return view('admin.user.index', [
            'users' => $users,
            'userAccess' => auth()->user()->userAccess,
        ]);
    }


    public function store(Request $request)
    {
        // Check if the user has permission to create users
        if (auth()->user()->access->admin_panel != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to create users.');
        }
        // Validate and store the new user
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'mobile' => 'required|string|max:15|unique:users',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'mobile' => $request->mobile,
            'role' => 1,
        ]);

        // Create user access
        $user = User::where('email', $request->email)->first();
        $user->access()->create([
            'admin_panel' => 3,
            'sms_and_email' => $request->sms_and_email,
            'contact' => $request->contact,
            'income' => $request->income,
            'expense' => $request->expense,
            'investment' => $request->investment,
            'asset' => $request->asset,
            'liability' => $request->liability,
            'bankbook' => $request->bankbook,
            'accounts' => $request->accounts,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully.',
        ]);
    }

    public function update(Request $request, User $user)
    {
        // Check if the user has permission to update users
        if (auth()->user()->access->admin_panel != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to update users.');
        }
        // Validate and update the user
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'mobile' => 'required|string|max:15|unique:users,mobile,' . $user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
            'mobile' => $request->mobile,
            'role' => 1,
        ]);

        // Update user access
        $user->access()->update([
            'admin_panel' => 3,
            'sms_and_email' => $request->sms_and_email,
            'contact' => $request->contact,
            'income' => $request->income,
            'expense' => $request->expense,
            'investment' => $request->investment,
            'asset' => $request->asset,
            'liability' => $request->liability,
            'bankbook' => $request->bankbook,
            'accounts' => $request->accounts,
        ]);
        // Return success response

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully.',
            'id' => $user->id,
        ]);
    }

    public function destroy(User $user)
    {
        // Check if the user has permission to delete users
        if (auth()->user()->access->admin_panel != 2) {
            return redirect()->route('admin.dashboard')->with('error', 'You do not have permission to delete users.');
        }
        // Delete the user
        $user->delete();

        return redirect()->route('admin.users')->with('success', 'User deleted successfully.');
    }
}
