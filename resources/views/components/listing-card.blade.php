@php
    $isAuthor = auth()->check() && auth()->id() === $listing->user_id;
@endphp

<a href="{{ $isAuthor ? route('listings.show-manage', $listing) : route('listings.show', $listing) }}"
   class="text-decoration-none text-white">
    <div class="listing-card mb-3 p-3 position-relative">

        <!-- Title and Date -->
        <div class="d-flex justify-content-between align-items-start mb-2">
            <p class="fw-bolder listing-title fs-6 mb-0 text-truncate"
               style="max-width: 75%; word-break: break-word;">
                {{ $listing->title }}
            </p>
            <small class="date text-nowrap ms-2">
                {{ $listing->created_at->format('d/m/Y') }}
            </small>
        </div>

        <!-- Author + Department -->
        <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-2 gap-sm-3">
            <small class="text-white">
                <i class="fa fa-user me-1"></i> {{ $listing->author }}
            </small>

            <div class="d-flex align-items-center">
                <x-department-dot :department="$listing->department" />
            </div>
        </div>

        <!-- Description -->
        <small>
            <p class="description mt-2 mb-1 text-justify">
                {{ Str::limit($listing->description, 230) }}
            </p>
        </small>
    </div>
</a>
