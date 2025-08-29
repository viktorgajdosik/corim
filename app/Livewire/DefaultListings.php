<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Listing;

class DefaultListings extends Component
{
    use WithPagination;

    public int $perPage = 4;
    public bool $openOnly = false;

    protected $listeners = [
        'openOnlyUpdated' => 'openOnlyUpdated',
    ];

    public function loadMore(): void
    {
        $this->perPage += 4;
    }

    public function openOnlyUpdated($openOnly): void
    {
        $this->openOnly = (bool) $openOnly;
        $this->resetPage();
    }

    public function updatingOpenOnly(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Listing::query()->orderBy('created_at', 'desc');

        if ($this->openOnly) {
            $query->where('is_open', 1);
        }

        $listings = $query->paginate($this->perPage);

        return view('livewire.default-listings', compact('listings'));
    }
}
