<div>
    @if ($isPlaceholder ?? false)
        <!-- Placeholder (Matches Real Listing Size) -->
        <div class="listing-card p-3 placeholder-glow" style="min-height: 140px; width: 100%;">
            <h5 class="listing-title mb-2">
                <span class="placeholder text-secondary col-12"></span>
            </h5>
            <p class="text-secondary description mt-2 text-justify">
                <span class="placeholder text-secondary col-12"></span>
                <span class="placeholder text-secondary col-10"></span>
            </p>
        </div>
    @else
        <!-- Determine if the user is the author -->
        @php
            $isAuthor = auth()->check() && auth()->id() === $listing->user_id;
        @endphp

        <!-- Link to correct page based on author status -->
        <a href="{{ $isAuthor ? route('listings.show-manage', $listing) : route('listings.show', $listing) }}"
           class="text-decoration-none text-white">
            <div class="listing-card mb-3 p-3">
                <p class="fw-bolder listing-title mb-2 fs-5">{{ $listing->title }}</p>
                <span><small>
                    <i class="fa fa-user"></i> {{ $listing->author }}</small>
                </span>
                <span> | </span>
                <span><small>
                    <i class="fa solid fa-building"></i> {{ $listing->department }}</small>
                </span>
                <span> | </span>
                <span><small>
                    <i class="fa fa-calendar"></i> {{ $listing->created_at->format('d/m/Y') }}</small>
                </span>
                <p class="description mt-2 mb-0 text-justify">
                    {{ Str::limit($listing->description, 230) }}
                </p>
            </div>
        </a>
    @endif
    <hr class="text-white">
</div>
