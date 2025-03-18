<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Listing;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;

class SearchDropdown extends Component
{
    public string $search = "";

    public function render()
    {
        $searchResults = [];

        if (strlen($this->search) > 0) {
            $cacheKey = "search_results_" . md5($this->search); // Unique cache key

            $searchResults = Cache::remember($cacheKey, 60, function () {
                return Listing::query()
                    ->where(function (Builder $query) {
                        $query->where('title', 'like', $this->search . '%') //  Uses index
                              ->orWhere('author', 'like', $this->search . '%')
                              ->orWhere('department', 'like', $this->search . '%');
                    })
                    ->orderBy('title') //  Optimized sorting
                    ->limit(5) //  Fetch only needed results
                    ->get();
            });
        }

        return view('livewire.search-dropdown', compact('searchResults'));
    }
}
