<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ListingController extends Controller
{

    public function index() {
        return view('listings.index');
    }

    //Show single listing
    public function show(Listing $listing)
    {
        $user = auth()->user();
        $userHasUnprocessedApplication = $this->hasUnprocessedApplication($listing, $user);

        return view('listings.show', compact('listing', 'userHasUnprocessedApplication'));
    }
        //Show create listing
        public function create() {
            return view('listings.create');
        }

         // Show Edit Form
    public function edit(Listing $listing) {
        return view('listings.edit', ['listing' => $listing]);
    }

// Delete Listing
public function destroy(Listing $listing) {
    // Make sure logged-in user is the owner
    if ($listing->user_id != auth()->id()) {
        abort(403, 'Unauthorized Action');
    }

    // Delete the listing and its associated applications
    $listing->applications()->delete();
    $listing->delete();

    return redirect('/listings/manage')->with('message', 'Listing deleted successfully');
}
            // Manage Listings
    public function manage() {
        return view('listings.manage', ['listings' => auth()->user()->listings()->get()]);
    }
         // Manage Listings
         public function showManage(Listing $listing) {
            // Check if the listing belongs to the authenticated user
            if ($listing->user_id != auth()->id()) {
                return redirect('/listings/manage')->with('error', 'You do not have permission to view this listing.');
            }

            return view('listings.show-manage', ['listing' => $listing]);
        }


public function hasUnprocessedApplication(Listing $listing, $user)
{
    // Check if the user has an unprocessed application for this listing
    return Application::where('listing_id', $listing->id)
        ->where('user_id', $user->id)
        ->where('accepted', 0) // Check for unprocessed applications
        ->exists();
}

}
