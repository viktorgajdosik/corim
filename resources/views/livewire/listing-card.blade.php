@php
    $href = $this->isAuthor
        ? route('listings.show-manage', $listing)
        : route('listings.show', $listing);
@endphp

<a href="{{ $href }}" class="text-decoration-none text-white">
  <div
    class="listing-card mb-3 p-3 position-relative"
    wire:init="ready"
    style="min-height:140px; overflow:hidden;"
  >
    {{-- =======================
         SKELETON (server-first)
         ======================= --}}
    @unless ($isReady)
      <div class="lc-skeleton-overlay" aria-hidden="true">
        <div class="skeleton-line w-100 mb-3"></div>

        <div class="d-flex flex-wrap gap-2 mb-3">
          <div class="skeleton-pill w-25"></div>
          <div class="skeleton-pill w-25"></div>
        </div>

        <div class="skeleton-line-sm w-100 mb-2"></div>
        <div class="skeleton-line-sm w-25 mb-2"></div>
      </div>
    @endunless

    {{-- =======================
         REAL CONTENT
         ======================= --}}
    @if ($isReady)
      {{-- Title + Date --}}
      <div class="d-flex justify-content-between align-items-start mb-2">
        <p class="fw-bolder listing-title fs-6 mb-0 text-truncate"
           style="max-width: 75%; word-break: break-word;">
          {{ $listing->title }}
        </p>
        <small class="date text-nowrap ms-2">
          {{ $listing->created_at->format('d/m/Y') }}
        </small>
      </div>

      {{-- Author + Department --}}
      <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center gap-2 gap-sm-3">
        <small class="text-white">
          <i class="fa fa-user me-1"></i> {{ $listing->author }}
        </small>
        <div class="d-flex align-items-center">
          <x-department-dot :department="$listing->department" />
        </div>
      </div>

      {{-- Description --}}
      <small>
        <p class="description mt-2 mb-1 text-justify">
          {{ Str::limit($listing->description, 230) }}
        </p>
      </small>
    @endif
  </div>
</a>
