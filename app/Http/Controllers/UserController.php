<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rules\OsuEmailDomain;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\User;
use Illuminate\Auth\Events\Registered;


class UserController extends Controller
{
    public function create() {
        return view('users.register');
    }

        // Create New User
        public function store(Request $request)
        {
            $formFields = $request->validate([
                'name' => ['required', 'min:6'],
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users', 'email'),
                    new OsuEmailDomain(), // Use the custom validation rule here
                ],
                'department' => ['required'],
                'password' => 'required|confirmed|min:8',
            ]);

        // Hash Password
        $formFields['password'] = bcrypt($formFields['password']);

        // Create User
        $user = User::create($formFields);

        // Dispatch the Registered event
        event(new Registered($user));

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
    $user = auth()->user()->load('acceptedApplications'); // Load the acceptedApplications relationship

    return view('users.profile', compact('user'));
}

// Show Edit Profile Page

public function editProfile() {
    $user = auth()->user(); // Get the authenticated user

    return view('users.edit-profile', compact('user'));
}

// Edit Profile
public function updateProfile(Request $request)
{
    $user = auth()->user(); // Get the authenticated user

    // Validate the input fields
    $formFields = $request->validate([
        'name' => 'nullable|min:6', // Name is optional and should be at least 3 characters
        'department' => 'nullable', // Department is optional
        'old_password' => 'nullable|required_with:password|min:8', // Old password is required only if a new password is provided
        'password' => 'nullable|min:8|confirmed|different:old_password|required_with:old_password', // New password is optional but must be different from the old password
    ]);

    // Check if the old password matches the current password
    if (isset($formFields['old_password']) && !password_verify($formFields['old_password'], $user->password)) {
        return back()->withErrors(['old_password' => 'The old password does not match your current password.'])->onlyInput('old_password');
    }

    // Prepare the data to update
    $updateData = [
        'name' => $formFields['name'],
    ];

    // Check if 'department' exists in $formFields and update it if provided
    if (array_key_exists('department', $formFields)) {
        $updateData['department'] = $formFields['department'];
    }

    // Check if a new password is provided and update it
    if (isset($formFields['password'])) {
        $updateData['password'] = bcrypt($formFields['password']);
    }

    // Update user profile based on provided fields
    $user->update($updateData);

    return redirect('/users/profile')->with('message', 'Profile updated successfully');
}

// Delete Profile
public function deleteProfile(Request $request) {
    $user = auth()->user(); // Get the authenticated user

    // Delete related applications
    $user->applications()->delete();

    // Delete related listings and their associated applications
    $user->listings->each(function ($listing) {
        $listing->applications()->delete();
        $listing->delete();
    });

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
