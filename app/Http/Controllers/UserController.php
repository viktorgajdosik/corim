<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Show the user profile.
     */
    public function show()
    {
        $user = Auth::user();
        $user->load('acceptedApplications');

        return view('users.profile', compact('user'));
    }

    /**
     * Show the edit profile page.
     */
    public function editProfile()
    {
        return view('users.edit-profile', ['user' => Auth::user()]);
    }

    /**
     * Delete the authenticated user's profile.
     */
    public function deleteProfile(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect('/')->with('error', 'User not found.');
        }

        // Delete related applications safely
        if ($user->applications()->exists()) {
            $user->applications()->delete();
        }

        // Delete related listings and their applications safely
        if ($user->listings()->exists()) {
            foreach ($user->listings as $listing) {
                if ($listing->applications()->exists()) {
                    $listing->applications()->delete();
                }
                $listing->delete();
            }
        }

        // Logout the user before deleting
        Auth::logout();

        // Delete the user and prevent errors
        try {
            $user->delete();
        } catch (\Exception $e) {
            return redirect('/users/profile')->with('error', 'Error deleting profile.');
        }

        // Invalidate session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('message', 'Your profile has been deleted.');
    }
}
