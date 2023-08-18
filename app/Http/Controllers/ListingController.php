<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ListingController extends Controller
{
    //Show all listings
    public function index() {
        return view('listings.index', [
            'listings' => Listing::latest()->filter(request(['search']))->paginate(10)
        ]);
    }

    //Show single listing
    public function show(Listing $listing) {
        return view('listings.show', [
            'listing' => $listing
            ]);
    }

        //Show create listing
        public function create() {
            return view('listings.create');
        }

        //Store listing
        public function store(Request $request) {
            $formFields = $request->validate([
                'title' => 'required',
                'description' => 'required',
                'department' => 'required'

            ]);


        $formFields['author'] = auth()->user()->name;
        $formFields['user_id'] = auth()->id();

            Listing::create($formFields);

            return redirect('/')->with('message', 'Listing created successfully');
        }

         // Show Edit Form
    public function edit(Listing $listing) {
        return view('listings.edit', ['listing' => $listing]);
    }

        // Update Listing Data
        public function update(Request $request, Listing $listing) {
            // Make sure logged in user is owner
            if($listing->user_id != auth()->id()) {
                abort(403, 'Unauthorized Action');
            }

            $formFields = $request->validate([
                'title' => 'required',
                'description' => 'required',
                'department' => 'required'
            ]);


            $listing->update($formFields);

            return back()->with('message', 'Listing updated successfully');
        }

            // Delete Listing
    public function destroy(Listing $listing) {
        // Make sure logged in user is owner
        if($listing->user_id != auth()->id()) {
            abort(403, 'Unauthorized Action');
        }

        $listing->delete();
        return redirect('/')->with('message', 'Listing deleted successfully');
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
}
