@php
  $ts = optional($listing->updated_at)->timestamp;
@endphp

<x-card-form>
  <div wire:init="ready"
       class="position-relative"
       style="min-height: 130px; border-radius: .5rem; overflow: hidden;">

    {{-- =======================
         SKELETON OVERLAY
         ======================= --}}
    <div class="task-skeleton-overlay"
         wire:loading
         wire:target="ready"
         aria-hidden="true">
      <div class="skeleton-line w-100 mb-3"></div>
      <div class="d-flex flex-wrap gap-2 mb-3">
        <div class="skeleton-pill w-25"></div>
        <div class="skeleton-pill w-25"></div>
        <div class="skeleton-pill w-25"></div>
      </div>
      <div class="skeleton-line-sm w-100 mb-2"></div>
      <div class="skeleton-line-sm w-100 mb-2"></div>
      <div class="skeleton-line-sm w-100 mb-2"></div>
    </div>

    {{-- =======================
         REAL CONTENT
         ======================= --}}
    <div @unless($isReady) class="d-none" @endunless
         x-data="{ modalOpen: false, countdown: 5, timer: null, confirming: false }"
         x-cloak>

      <x-card-heading class="listing-title mb-3">{{ $listing->title }}</x-card-heading>

      <div class="d-flex align-items-center gap-3 mb-1 flex-wrap">
        <small title="Author"><i class="fa fa-user me-1"></i> {{ $listing->author }}</small>
        <small title="Date Created"><i class="fa fa-calendar me-1"></i> {{ $listing->created_at->format('d/m/Y') }}</small>
        <small title="Department" class="d-inline-flex align-items-center">
          <x-department-dot :department="$listing->department" />
        </small>
      </div>

      <x-text class="description mt-3 mb-3">
        {!! nl2br(e($listing->description)) !!}
      </x-text>

      @if ($this->isAuthor)
        <div class="d-flex align-items-center gap-2">

          <button type="button"
                  class="btn btn-primary btn-sm"
                  onclick="window.location.href='{{ route('listings.edit', $listing->id) }}'">
            <i class="fa fa-pencil"></i> Edit
          </button>

          <button type="button"
                  class="btn btn-danger btn-sm"
                  @click="modalOpen = true; window.scrollTo({ top: 0, behavior: 'smooth' });">
            <i class="fa fa-trash"></i> Delete
          </button>

          {{-- Sexy Open/Closed switch (aligned right) --}}
          <div class="ms-auto d-flex align-items-center gap-2"
               {{-- wire:key forces Alpine re-init if state changes (belt & suspenders) --}}
               wire:key="switch-{{ $listing->id }}-{{ (int)$isOpen }}"
               x-data="{ isOpen: @entangle('isOpen').live, toggling: false }"
               x-init="
                 if (window.bootstrap) {
                   new bootstrap.Popover($refs.openHelp, {
                     container: 'body',
                     trigger: 'hover focus',
                     placement: 'top'
                   });
                 }
               "
               x-on:listing-open-state-changed.window="
                 if ($event.detail?.listingId === {{ $listing->id }}) {
                   isOpen = !!$event.detail.is_open
                 }
               ">

            {{-- State text bound to entangled value --}}
            <span class="small text-muted-60" x-text="isOpen ? 'Open' : 'Closed'"></span>

            {{-- Custom switch (no manual flip; Livewire returns authoritative state) --}}
            <div class="oa-switch"
                 :class="{ 'is-open': isOpen, 'is-busy': toggling }"
                 role="switch"
                 tabindex="0"
                 :aria-checked="isOpen.toString()"
                 aria-label="Toggle applications open or closed"
                 @click.prevent="
                   if (toggling) return;
                   toggling = true;
                   $wire.toggleOpen()
                     .then(() => { toggling = false; })
                     .catch(() => { toggling = false; });
                 "
                 @keydown.enter.prevent="$el.click()"
                 @keydown.space.prevent="$el.click()">
              <span class="knob"></span>
            </div>

            {{-- Help popover --}}
            <button type="button"
                    class="btn btn-link btn-sm p-0 text-muted-60"
                    data-bs-toggle="popover"
                    data-bs-custom-class="oa-popover"
                    data-bs-title="Applications"
                    data-bs-content="When set to ‘Open’, users can submit applications. Switch to ‘Closed’ to temporarily stop new applications without deleting the listing."
                    aria-label="What does this switch do?"
                    x-ref="openHelp">
              <i class="fa fa-question-circle"></i>
            </button>

            <div class="spinner-grow spinner-grow-sm text-light"
                 x-show="toggling"
                 x-cloak
                 role="status"
                 aria-hidden="true"></div>
          </div>
        </div>
      @endif

      {{-- Delete modal --}}
      <template x-if="modalOpen">
        <div class="p-3 border border-danger bg-transparent rounded mt-3">
          <div x-show="!confirming">
            <p class="text-danger mb-2">Are you sure you want to delete this listing? This action is irreversible.</p>
            <div class="d-flex gap-2 justify-content-end">
              <button class="btn btn-outline-light rounded-pill btn-sm" @click="modalOpen = false">Cancel</button>
              <button class="btn btn-outline-danger rounded-pill btn-sm"
                      @click="
                        confirming = true; countdown = 5;
                        timer = setInterval(() => {
                          if (countdown > 1) { countdown--; }
                          else { clearInterval(timer); $refs.form.submit(); }
                        }, 1000);
                      ">
                Yes, Delete
              </button>
            </div>
          </div>

          <div x-show="confirming">
            <p class="text-danger mb-2">Deleting in <strong x-text="countdown"></strong> seconds...</p>
            <div class="d-flex justify-content-end">
              <button class="btn btn-outline-light rounded-pill btn-sm"
                      @click="clearInterval(timer); confirming = false; countdown = 5; modalOpen = false;">
                Cancel Deletion
              </button>
            </div>
          </div>

          <form method="POST" action="{{ route('listings.destroy', $listing->id) }}" x-ref="form" class="d-none">
            @csrf
            @method('DELETE')
          </form>
        </div>
      </template>
    </div>
  </div>
</x-card-form>

