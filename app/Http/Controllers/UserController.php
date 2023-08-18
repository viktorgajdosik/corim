<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rules\OsuEmailDomain;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\User;

class UserController extends Controller
{
    public function create() {
        return view('users.register');
    }

        // Create New User
        public function store(Request $request)
        {
            $formFields = $request->validate([
                'name' => ['required', 'min:3'],
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users', 'email'),
                    new OsuEmailDomain(), // Use the custom validation rule here
                ],
                'department' => ['required'],
                'password' => 'required|confirmed|min:6',
            ]);

        // Hash Password
        $formFields['password'] = bcrypt($formFields['password']);

        // Create User
        $user = User::create($formFields);

        // Login
        auth()->login($user);

        return redirect('/')->with('message', 'User created and logged in');

    }

            // Logout User
    public function logout(Request $request) {
        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('message', 'You have been logged out');

    }

 // Show Login Form
 public function login() {
    return view('users.login');
}

// Authenticate User
public function authenticate(Request $request) {
    $formFields = $request->validate([
        'email' => ['required', 'email'],
        'password' => 'required'
    ]);

    if(auth()->attempt($formFields)) {
        $request->session()->regenerate();

        return redirect('/')->with('message', 'You are now logged in');
    }

    return back()->withErrors(['email' => 'Invalid Credentials'])->onlyInput('email');
}

 // Show User Profile
 public function show() {
    $user = auth()->user(); // Get the authenticated user

    return view('users.profile', compact('user'));
}

// Show Edit Profile Page

public function editProfile() {
    $user = auth()->user(); // Get the authenticated user

    return view('users.edit-profile', compact('user'));
}

// Edit Profile
public function updateProfile(Request $request) {
    $user = auth()->user(); // Get the authenticated user

    $formFields = $request->validate([
        'name' => ['required', 'min:3'],
        'email' => ['required', 'email'],
        'department' => ['required'],
    ]);

    $user->update($formFields); // Update user profile

    return redirect('/users/profile')->with('message', 'Profile updated successfully');
}

// Delete Profile

public function deleteProfile(Request $request) {
    $user = auth()->user(); // Get the authenticated user

    // Log out the user
    auth()->logout();

    // Delete the user
    $user->delete();

    // Invalidate the session and regenerate token
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/')->with('message', 'Your profile has been deleted');
}

}
