<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Listing;

class DefaultListings extends Component
{
    use WithPagination;

    public $perPage = 4; // Number of listings to load initially

    protected $listeners = ['loadMore'];

    public function loadMore()
    {
        $this->perPage += 4; // Increase the number of listings displayed
    }

    public function render()
    {
        $listings = Listing::orderBy('created_at', 'desc')->paginate($this->perPage);

        return view('livewire.default-listings', compact('listings'))
            ->extends('components.layout')
            ->section('content');
    }
}
