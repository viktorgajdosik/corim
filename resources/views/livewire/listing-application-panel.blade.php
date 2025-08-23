<x-card-form>
  <div
    wire:init="readyPanel"
    class="position-relative"
    style="min-height: 105px; overflow: hidden;"
  >
    {{-- SKELETON --}}
    <div class="task-skeleton-overlay"
         wire:loading
         wire:target="readyPanel"
         aria-hidden="true">
      <div class="skeleton-line w-100 mb-2"></div>
      <div class="skeleton-line w-100 mb-2"></div>
      <div class="skeleton-line w-100 mb-2"></div>
      <div class="skeleton-line w-100"></div>
    </div>

    {{-- REAL CONTENT --}}
    <div @unless($panelReady) class="d-none" @endunless>

      @if (!$application)
        <x-card-heading class="mb-2">Message the Author</x-card-heading>
        <x-text class="mb-2">Tell the author something about yourself.</x-text>

        @livewire('apply-to-listing-form', ['listing' => $listing], key('apply-form-'.$listing->id))

      @elseif (!$isAccepted)
        {{-- Awaiting state (used by JS to stop spinner) --}}
        <div data-app-state="awaiting" data-listing-id="{{ $listing->id }}">
          <x-card-heading>Awaiting Application Results</x-card-heading>
          <x-text>You have already applied for this research work. Please wait for the author's response.</x-text>
        </div>

      @else
        {{-- Accepted: panel remains quiet; tasks are shown below on the page --}}
      @endif

    </div>
  </div>
</x-card-form>
