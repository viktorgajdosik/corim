@php
  $pendingCount      = $this->pending->count();
  $participantsCount = $this->participants->count();
@endphp

<div wire:init="ready" data-listing-id="{{ $listing->id }}">

  {{-- ================== Applications ================== --}}
  <x-secondary-heading>Applications</x-secondary-heading>

  {{-- Initial skeletons ONLY if there are any pending items --}}
  @unless($isReady)
    @if($pendingCount > 0)
      @for($i = 0; $i < $pendingCount; $i++)
        <x-card-form>
          <div class="position-relative" style="min-height:70px;border-radius:.5rem;overflow:hidden;">
            <div class="task-skeleton-overlay" aria-hidden="true">
              <div class="skeleton-line w-50 mb-3"></div>
              <div class="d-flex flex-wrap gap-2">
                <div class="skeleton-pill w-25"></div>
                <div class="skeleton-pill w-25"></div>
              </div>
                <div class="skeleton-pill w-50 mt-2"></div>
            </div>
          </div>
        </x-card-form>
      @endfor
    @else
      <x-text class="text-white mb-5">Currently no applications.</x-text>
    @endif
  @else
    @forelse($this->pending as $application)
      @php $ts = optional($application->updated_at)->timestamp; @endphp

      <x-card-form wire:key="pending-{{ $application->id }}-{{ $ts }}">
        <div
          class="position-relative"
          style="min-height:70px;border-radius:.5rem;overflow:hidden;"
          data-app-id="{{ $application->id }}"
          data-updated-at="{{ $ts }}"
          x-data
        >
          {{-- overlay THIS card while accept/deny for this id is in flight --}}
          <div class="task-skeleton-overlay"
               x-show="$store.ui.appAcceptLoading[{{ $application->id }}] || $store.ui.appDenyLoading[{{ $application->id }}]"
               x-cloak
               aria-hidden="true">
            <div class="skeleton-line w-50 mb-3"></div>
            <div class="d-flex flex-wrap gap-2">
              <div class="skeleton-pill w-25"></div>
              <div class="skeleton-pill w-25"></div>
            </div>
              <div class="skeleton-pill w-50 mt-2"></div>
          </div>

          <div :class="($store.ui.appAcceptLoading[{{ $application->id }}] || $store.ui.appDenyLoading[{{ $application->id }}]) ? 'invisible' : ''">
            {{-- FLEX ROW to center the right column vertically --}}
            <div class="d-flex justify-content-between align-items-center w-100">
              {{-- LEFT: name + meta + message --}}
              <div class="me-3 flex-grow-1">
                <x-card-heading>{{ $application->user->name }}</x-card-heading>

                <div class="d-flex gap-3 mb-1">
                  <small title="Email address">
                    <i class="fa fa-envelope me-1"></i> {{ $application->user->email }}
                  </small>
                  <small title="Department">
                    <x-department-dot :department="$application->user->department" />
                  </small>
                </div>

                <p class="mb-0"><i class="fa fa-edit"></i> {{ $application->message }}</p>
              </div>

              {{-- RIGHT: stacked buttons, vertically centered by the parent align-items-center --}}
              <div class="d-flex flex-column gap-2 align-items-end ms-3">
                {{-- Accept --}}
                <button type="button"
                        class="btn btn-primary btn-sm d-inline-flex align-items-center gap-2"
                        :disabled="($store.ui.appAcceptLoading[{{ $application->id }}]===true) || ($store.ui.appDenyLoading[{{ $application->id }}]===true)"
                        @click="
                          $store.ui.addParticipantGhost({{ $listing->id }});
                          $store.ui.startAppAccept({{ $application->id }});
                          $wire.accept({{ $application->id }});
                        ">
                  <i class="fa fa-check" x-show="!$store.ui.appAcceptLoading[{{ $application->id }}]"></i>
                  <span class="spinner-grow spinner-grow-sm align-middle" x-show="$store.ui.appAcceptLoading[{{ $application->id }}]" x-cloak></span>
                  <span x-text="$store.ui.appAcceptLoading[{{ $application->id }}] ? 'Accepting' : 'Accept'"></span>
                </button>

                {{-- Deny --}}
                <button type="button"
                        class="btn btn-danger btn-sm d-inline-flex align-items-center gap-2"
                        :disabled="($store.ui.appAcceptLoading[{{ $application->id }}]===true) || ($store.ui.appDenyLoading[{{ $application->id }}]===true)"
                        @click="
                          if (confirm('Deny this application?')) {
                            $store.ui.startAppDeny({{ $application->id }});
                            $wire.deny({{ $application->id }});
                          }
                        ">
                  <i class="fa fa-times" x-show="!$store.ui.appDenyLoading[{{ $application->id }}]"></i>
                  <span class="spinner-grow spinner-grow-sm align-middle" x-show="$store.ui.appDenyLoading[{{ $application->id }}]" x-cloak></span>
                  <span x-text="$store.ui.appDenyLoading[{{ $application->id }}] ? 'Denying' : 'Deny'"></span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </x-card-form>
    @empty
      <x-text class="text-white mb-5">Currently no applications.</x-text>
    @endforelse
  @endunless


  {{-- ================== Participants ================== --}}
<x-secondary-heading>Current Participants</x-secondary-heading>

{{-- Initial skeletons ONLY if there are any participants --}}
@unless($isReady)
  @if($participantsCount > 0)
    @for($i = 0; $i < $participantsCount; $i++)
      <x-card-form>
        <div class="position-relative" style="min-height:50px;border-radius:.5rem;overflow:hidden;">
          <div class="task-skeleton-overlay" aria-hidden="true">
            <div class="skeleton-line w-50 mb-3"></div>
            <div class="d-flex flex-wrap gap-2">
              <div class="skeleton-pill w-25"></div>
              <div class="skeleton-pill w-25"></div>
            </div>
          </div>
        </div>
      </x-card-form>
    @endfor
  @else
    <x-text class="text-white mb-5">Currently no participants.</x-text>
  @endif
@else
  {{-- Ghost placeholders + smart empty state --}}
  <div x-data x-init="$store.ui.ensureListing({{ $listing->id }})">
    <template x-for="i in ($store.ui.participantGhostsByListing[{{ $listing->id }}] || 0)" :key="i">
      <x-card-form>
        <div class="position-relative" style="min-height:55px;border-radius:.5rem;overflow:hidden;">
          <div class="task-skeleton-overlay" aria-hidden="true">
            <div class="skeleton-line w-50 mb-3"></div>
            <div class="d-flex flex-wrap gap-2">
              <div class="skeleton-pill w-25"></div>
              <div class="skeleton-pill w-25"></div>
            </div>
          </div>
        </div>
      </x-card-form>
    </template>

    {{-- Show empty text ONLY when no real participants AND no ghosts --}}
    @if ($participantsCount === 0)
      <x-text class="text-white mb-5"
              x-show="($store.ui.participantGhostsByListing[{{ $listing->id }}] || 0) === 0"
              x-cloak>
        Currently no participants.
      </x-text>
    @endif
  </div>

  {{-- Real participants list (renders when available) --}}
  @foreach ($this->participants as $application)
    @php $ts = optional($application->updated_at)->timestamp; @endphp

    <x-card-form wire:key="participant-{{ $application->id }}-{{ $ts }}">
      <div
        class="position-relative d-flex justify-content-between align-items-center"
        style="min-height:55px;border-radius:.5rem;overflow:hidden;"
        data-app-id="{{ $application->id }}"
        data-updated-at="{{ $ts }}"
        x-data
      >
        {{-- overlay THIS participant while remove is in flight --}}
        <div class="task-skeleton-overlay"
             x-show="$store.ui.appRemoveLoading[{{ $application->id }}]"
             x-cloak
             aria-hidden="true">
          <div class="skeleton-line w-50 mb-3"></div>
          <div class="d-flex flex-wrap gap-2">
            <div class="skeleton-pill w-25"></div>
            <div class="skeleton-pill w-25"></div>
          </div>
        </div>

        <div :class="$store.ui.appRemoveLoading[{{ $application->id }}] ? 'invisible w-100' : 'w-100'">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <x-card-heading>{{ $application->user->name }}</x-card-heading>
              <div class="d-flex gap-3 mb-1">
                <small title="Email address">
                  <i class="fa fa-envelope me-1"></i> {{ $application->user->email }}
                </small>
                <small title="Department">
                  <x-department-dot :department="$application->user->department" />
                </small>
              </div>
            </div>

            {{-- Remove --}}
            <button type="button"
                    class="btn btn-danger btn-sm d-inline-flex align-items-center gap-2"
                    :disabled="$store.ui.appRemoveLoading[{{ $application->id }}]===true"
                    @click="
                      if (confirm('Remove this participant?')) {
                        $store.ui.startAppRemove({{ $application->id }});
                        $wire.remove({{ $application->id }});
                      }
                    ">
              <span class="d-inline-flex align-items-center gap-2">
                <i class="fa fa-trash" x-show="!$store.ui.appRemoveLoading[{{ $application->id }}]"></i>
                <span class="spinner-grow spinner-grow-sm align-middle" x-show="$store.ui.appRemoveLoading[{{ $application->id }}]" x-cloak></span>
                <span x-text="$store.ui.appRemoveLoading[{{ $application->id }}] ? 'Removing' : 'Remove'"></span>
              </span>
            </button>
          </div>
        </div>
      </div>
    </x-card-form>
  @endforeach
@endunless

</div>
