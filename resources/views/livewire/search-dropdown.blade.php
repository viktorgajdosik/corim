<div x-data="{ showDropdown: false }" class="position-relative">

    <!-- Email Verification Alert (Appears right above search bar, not fixed) -->
    @auth
        @if (!auth()->user()->hasVerifiedEmail())
            <div class="alert alert-warning text-center py-1 small"
                 role="alert"
                 style="width: 100%;">
                Please verify your email to access all features.
                <a href="{{ route('verification.notice') }}" class="alert-link">Verify</a>
            </div>
        @endif
    @endauth

    <!-- Search Bar -->
    <div class="position-relative text-white mt-2">
        <input
            type="text"
            wire:model.live="search"
            placeholder="Search..."
            class="form-control bg-dark text-white rounded-pill border-0 search-input"
            x-on:focus="showDropdown = true"
            x-on:click.away="showDropdown = false"
        />

        <!-- Loading Spinner (Appears while searching) -->
        <div wire:loading class="position-absolute top-50 end-0 translate-middle-y me-2">
            <div class="spinner-grow spinner-grow-sm text-secondary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </div>

    <!-- Search Results Dropdown -->
    <div class="bg-dark text-white rounded mt-2"
         x-show="showDropdown && search.length > 0"
         style="position: absolute; z-index: 1000; max-height: 300px; overflow-y: auto; width: 100%;">

        @if(count($searchResults))
            <ul class="list-group">
                @foreach($searchResults as $listing)
                    <li class="list-group-item bg-dark border-0 text-white">
                        <a href="{{ url('/listings', ['listing' => $listing->id]) }}"
                           class="d-block text-white text-decoration-none"
                           x-on:click="showDropdown = false">
                            {{ $listing->title }}
                        </a>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-center text-secondary mt-3 w-100">No results found</p>
        @endif
    </div>

</div>
