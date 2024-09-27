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

    <!-- Real Content -->
    <div class="listing-card p-3" x-show="!loading">
      <div class="date-container">
          <p class="date-created d-none d-md-block">
              <small>
                  <i class="fa fa-calendar text-secondary" data-toggle="tooltip" title="Date Created"></i> {{ $listing->created_at->format('d/m/Y') }}
              </small>
          </p>
      </div>
      <h5 class="listing-title mb-2">{{ $listing->title }}</h5>
      <span><small>
          <i class="fa fa-user text-secondary" data-toggle="tooltip" title="Author"></i> {{ $listing->author }}</small>
      </span>
      <span> | </span>
      <span><small>
          <i class="fa solid fa-building text-secondary" data-toggle="tooltip" title="Department"></i> {{ $listing->department }}</small>
      </span>
      <span class="d-inline d-md-none"> | </span>
      <span class="d-inline d-md-none"><small>
          <i class="fa fa-calendar text-secondary" data-toggle="tooltip" title="Date Created"></i> {{ $listing->created_at->format('d/m/Y') }}</small>
      </span>
      <p class="text-secondary description mt-2 mb-0 text-justify">
          {{ Str::limit($listing->description, 230) }}
          <span class="expand-listing text-info d-none d-md-inline end-0 preview-link" data-toggle="modal" data-target="#descriptionModal" data-title="{{ $listing->title }}" data-description="{{ $listing->description }}"> Preview</span>
      </p>

      <!-- Modal for Full Description -->
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

  </div>
