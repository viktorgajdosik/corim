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


}
