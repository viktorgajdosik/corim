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
      {{-- Title line --}}
      <div class="skeleton-line w-100 mb-3"></div>

      {{-- Metadata pills (author, date, dept) --}}
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

      {{-- Header / title + meta --}}
      <x-card-heading class="listing-title mb-3">{{ $listing->title }}</x-card-heading>

      <div class="d-flex align-items-center gap-3 mb-1 flex-wrap">
        <small title="Author">
          <i class="fa fa-user me-1"></i> {{ $listing->author }}
        </small>
        <small title="Date Created">
          <i class="fa fa-calendar me-1"></i> {{ $listing->created_at->format('d/m/Y') }}
        </small>
        <small title="Department" class="d-inline-flex align-items-center">
          <x-department-dot :department="$listing->department" />
        </small>
      </div>

      <x-text class="description mt-3 mb-3">
        {!! nl2br(e($listing->description)) !!}
      </x-text>

      {{-- Actions --}}
      @if ($this->isAuthor)
        <div class="d-flex gap-2">
          <button type="button"
                  class="btn btn-primary btn-sm"
                  onclick="window.location.href='{{ route('listings.edit', $listing->id) }}'">
            <i class="fa fa-pencil"></i> Edit
          </button>

          <button type="button"
                  class="btn btn-danger btn-sm"
                  @click="
                    modalOpen = true;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                  ">
            <i class="fa fa-trash"></i> Delete
          </button>
        </div>
      @endif

      {{-- Delete modal --}}
      <template x-if="modalOpen">
        <div class="p-3 border border-danger bg-transparent rounded mt-3">
          {{-- First step --}}
          <div x-show="!confirming">
            <p class="text-danger mb-2">
              Are you sure you want to delete this listing? This action is irreversible.
            </p>
            <div class="d-flex gap-2 justify-content-end">
              <button class="btn btn-outline-light rounded-pill btn-sm"
                      @click="modalOpen = false">
                Cancel
              </button>
              <button class="btn btn-outline-danger rounded-pill btn-sm"
                      @click="
                        confirming = true;
                        countdown = 5;
                        timer = setInterval(() => {
                          if (countdown > 1) {
                            countdown--;
                          } else {
                            clearInterval(timer);
                            $refs.form.submit();
                          }
                        }, 1000);
                      ">
                Yes, Delete
              </button>
            </div>
          </div>

          {{-- Countdown --}}
          <div x-show="confirming">
            <p class="text-danger mb-2">
              Deleting in <strong x-text="countdown"></strong> seconds...
            </p>
            <div class="d-flex justify-content-end">
              <button class="btn btn-outline-light rounded-pill btn-sm"
                      @click="
                        clearInterval(timer);
                        confirming = false;
                        countdown = 5;
                        modalOpen = false;
                      ">
                Cancel Deletion
              </button>
            </div>
          </div>

          {{-- Hidden form --}}
          <form method="POST"
                action="{{ route('listings.destroy', $listing->id) }}"
                x-ref="form"
                class="d-none">
            @csrf
            @method('DELETE')
          </form>
        </div>
      </template>
    </div>
  </div>
</x-card-form>
