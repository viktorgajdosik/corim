<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Listing;

class CreateListing extends Component
{
    public string $title = '';
    public string $description = '';
    public string $department = '';

    public function submit()
    {
        $this->validate([
            'title' => 'required|min:10|max:500',
            'description' => 'required|min:50|max:5000',
            'department' => 'required',
        ]);

        Listing::create([
            'title' => $this->title,
            'description' => $this->description,
            'department' => $this->department,
            'author' => auth()->user()->name,
            'user_id' => auth()->id(),
        ]);

        session()->flash('message', 'Listing created successfully');
        return redirect('/');
    }

    public function render()
    {
        return view('livewire.create-listing');
    }
}
