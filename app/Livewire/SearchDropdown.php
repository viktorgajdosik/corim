<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Listing;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;

class SearchDropdown extends Component
{
    public string $search = '';
    public bool $openOnly = false;

    public function mount(): void
    {
        // Enforce default on fresh loads (prevents a stale true from a prior session)
        $this->openOnly = false;
    }

    public function setOpenOnly(bool $value): void
    {
        $this->openOnly = (bool) $value;

        // Tell sibling component(s) (DefaultListings) to update
        $this->dispatch('openOnlyUpdated', openOnly: $this->openOnly);
    }

    public function render()
    {
        $searchResults = [];

        if (strlen($this->search) > 0) {
            $cacheKey = "search_results_" . md5($this->search . '|' . (int)$this->openOnly);

            $searchResults = Cache::remember($cacheKey, 60, function () {
                return Listing::query()
                    ->when($this->openOnly, fn($q) => $q->where('is_open', 1))
                    ->where(function (Builder $query) {
                        $query->where('title', 'like', '%' . $this->search . '%')
                              ->orWhere('author', 'like', '%' . $this->search . '%')
                              ->orWhere('department', 'like', '%' . $this->search . '%')
                              ->orWhere('description', 'like', '%' . $this->search . '%');
                    })
                    ->orderBy('title')
                    ->limit(5)
                    ->get();
            });
        }

        return view('livewire.search-dropdown', compact('searchResults'));
    }
}
