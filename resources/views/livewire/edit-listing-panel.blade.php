<x-card-form>
  <div
    wire:init="readyPanel"
    class="position-relative"
    style="min-height: 130px; overflow: hidden;"
    data-listing-id="{{ $listing->id }}"
    data-updated-at="{{ optional($listing->updated_at)->timestamp }}"
  >
    {{-- Initial skeleton overlay (first paint only) --}}
    <div class="task-skeleton-overlay"
         wire:loading
         wire:target="readyPanel"
         aria-hidden="true">
      <div class="skeleton-line w-100 mb-3"></div>
      <div class="skeleton-line w-100 mb-3"></div>
      <div class="skeleton-line w-100 mb-3"></div>
      <div class="skeleton-line w-100"></div>
    </div>

    {{-- Real content (revealed after readyPanel) --}}
    <div @unless($panelReady) class="d-none" @endunless>
      @livewire('edit-listing-form', ['listing' => $listing], key('edit-listing-form-'.$listing->id.'-'.optional($listing->updated_at)->timestamp))
    </div>
  </div>
</x-card-form>
