<div x-data="{ showDropdown: false }"
     class="position-relative"
     style="z-index:2147483646;"> {{-- keep this whole search stack above everything --}}

  {{-- Email Verification Alert --}}
  @auth
    @if (!auth()->user()->hasVerifiedEmail())
      <div class="alert alert-warning text-center py-1 small" role="alert" style="width: 100%;">
        Please verify your email to access all features.
        <a href="{{ route('verification.notice') }}" class="alert-link">Verify</a>
      </div>
    @endif
  @endauth

  {{-- Search Bar + Settings --}}
  <div class="position-relative text-white mt-2">
    <input
      type="text"
      wire:model.live="search"
      placeholder="Search..."
      class="form-control bg-dark text-white rounded-pill border-0 search-input search-input-with-ctrl"
      x-on:focus="showDropdown = true"
      x-on:click.away="showDropdown = false"
    />

    {{-- Right controls: spinner(s) + kebab (inside the input) --}}
    <div class="position-absolute top-50 end-0 translate-middle-y d-flex align-items-center me-2 gap-2">

      {{-- Loading Spinner (while typing/searching) --}}
      <div wire:loading.class.remove="d-none" wire:target="search" class="d-none">
        <div class="spinner-grow spinner-grow-sm text-secondary" role="status" aria-hidden="true"></div>
      </div>

      {{-- While switching openOnly, replace kebab with a spinner in the same spot --}}
      <div wire:loading wire:target="setOpenOnly" class="kebab-circle">
        <div class="spinner-grow text-secondary centered-spinner" role="status" aria-hidden="true"></div>
      </div>

      {{-- Kebab settings (in a grey circle) — hidden while setOpenOnly is running --}}
      <div class="dropdown" wire:loading.remove wire:target="setOpenOnly">
        <button
          class="kebab-circle text-muted-60"
          type="button"
          id="searchSettingsBtn"
          data-bs-toggle="dropdown"
          aria-expanded="false"
          aria-label="Search settings"
        >
          <i class="fa fa-ellipsis-v"></i>
        </button>

        {{-- NOTE: removed `dropdown-menu-dark`, we fully control the palette via .search-settings-menu vars --}}
        <ul class="dropdown-menu dropdown-menu-end shadow search-settings-menu"
            aria-labelledby="searchSettingsBtn">
          <li>
            <button class="dropdown-item d-flex justify-content-between align-items-center"
                    wire:click="setOpenOnly(false)">
              All listings
              @if(!$openOnly)
                <i class="fa fa-check ms-3"></i>
              @endif
            </button>
          </li>
          <li>
            <button class="dropdown-item d-flex justify-content-between align-items-center"
                    wire:click="setOpenOnly(true)">
              Open listings only
              @if($openOnly)
                <i class="fa fa-check ms-3"></i>
              @endif
            </button>
          </li>
        </ul>
      </div>
    </div>
  </div>

  {{-- Search Results Dropdown (always on top; anchored under input) --}}
  <div class="bg-dark text-white rounded mt-2 search-results-menu"
       x-show="showDropdown && search.length > 0"
       style="max-height: 300px; overflow-y: auto; width: 100%;">
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
