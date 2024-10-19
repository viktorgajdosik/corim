<div x-data="{ loading: true }" x-init="setTimeout(() => loading = false, 1500)">

    <!-- Placeholder Loading Card -->
    <div class="listing-card p-3" x-show="loading">
        <div class="placeholder-glow">
            <h5 class="listing-title mb-2 placeholder-glow">
                <span class="placeholder text-secondary col-10"></span>
                <span class="placeholder text-secondary text-end col-1"></span>
            </h5>
            <span class="placeholder text-secondary col-1"> </span>
            <span class="placeholder text-secondary col-1"></span>
            <p class="text-secondary description mt-2 text-justify placeholder-glow">
                <span class="placeholder text-secondary mb-2 col-8"></span>
                <span class="placeholder text-secondary col-8"></span>
            </p>
        </div>
    </div>

    <div class="listing-card mb-3 p-3" x-show="!loading">
        <div class="date-container">
            <p class="date-created d-none d-md-block">
                <small>
                    <i class="fa fa-calendar" data-toggle="tooltip" title="Date Created"></i> {{ $listing->created_at->format('d/m/Y') }}
                </small>
            </p>
        </div>
        <h5 class="listing-title mb-2">{{ $listing->title }}</h5>
        <span><small>
            <i class="fa fa-user" data-toggle="tooltip" title="Author"></i> {{ $listing->author }}</small>
        </span>
        <span> | </span>
        <span><small>
            <i class="fa solid fa-building" data-toggle="tooltip" title="Department"></i> {{ $listing->department }}</small>
        </span>
        <span class="d-inline d-md-none"> | </span>
        <span class="d-inline d-md-none"><small>
            <i class="fa fa-calendar" data-toggle="tooltip" title="Date Created"></i> {{ $listing->created_at->format('d/m/Y') }}</small>
        </span>
        <p class="description mt-2 mb-0 text-justify">
            {{ Str::limit($listing->description, 230) }}
        </p>

    </div>
    <hr class="text-white">
</div>

