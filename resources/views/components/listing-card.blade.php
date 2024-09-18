<div class="listing-card p-3">
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
    <p class="text-secondary description mt-2 mb-0 text-justify">
        {{ Str::limit($listing->description, 200) }}
        <span class="expand-listing text-info d-none d-md-inline end-0 preview-link" data-toggle="modal" data-target="#descriptionModal" data-title="{{ $listing->title }}" data-description="{{ $listing->description }}"> Preview</span>
    </p>

    <div class="modal fade" id="descriptionModal" tabindex="-1" role="dialog" aria-labelledby="descriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content p-2">
                <div class="modal-header">
                    <h5 class="modal-title" id="descriptionModalLabel"></h5>
                </div>
                <div class="modal-body text-justify"></div>
            </div>
        </div>
    </div>
</div>
