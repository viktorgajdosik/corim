
        <!-- Determine if the user is the author -->
        @php
            $isAuthor = auth()->check() && auth()->id() === $listing->user_id;
        @endphp

        <!-- Link to correct page based on author status -->
        <a href="{{ $isAuthor ? route('listings.show-manage', $listing) : route('listings.show', $listing) }}"
           class="text-decoration-none text-white">
            <div class="listing-card mb-3 p-3">
                <p class="fw-bolder listing-title mb-2 fs-6">{{ $listing->title }}</p>
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
                <small> <p class="description mt-2 mb-0 text-justify">
                    {{ Str::limit($listing->description, 230) }}
                </p></small>

            </div>
        </a>


