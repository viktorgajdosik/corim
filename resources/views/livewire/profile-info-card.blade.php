<x-card-form>
  <div wire:init="ready"
       class="position-relative"
       style="min-height: 130px; border-radius: .5rem; overflow: hidden;">

    {{-- =============== SKELETON OVERLAY =============== --}}
    <div class="task-skeleton-overlay"
         wire:loading
         wire:target="ready"
         aria-hidden="true">
      <div class="skeleton-line w-75 mb-3"></div>
      <div class="d-flex flex-wrap gap-2">
        <div class="skeleton-pill w-50 mb-2"></div>
        <div class="skeleton-pill w-50"></div>
      </div>
    </div>

    {{-- =============== REAL CONTENT =============== --}}
    <div @unless($isReady) class="d-none" @endunless
         x-data="{ modalOpen: false, countdown: 5, timer: null, confirming: false }"
         x-cloak>

      <div class="d-flex justify-content-between align-items-start">
        <div>
          <x-card-heading>{{ $user->name }}</x-card-heading>

          <x-text>
            <i class="me-1 text-white fa fa-envelope"></i> {{ $user->email }}
          </x-text>

            <div class="d-flex align-items-center">
          <x-department-dot :department="$user->department" />
        </div>
        </div>

        {{-- Right-aligned stacked icons --}}
        <div class="d-flex flex-column align-items-end gap-3 mt-1">
          {{-- Edit profile --}}
          <a href="/users/edit-profile" class="text-white" data-bs-toggle="tooltip" title="Edit Profile">
            <i class="fa fa-pencil"></i>
          </a>

          {{-- Download info (adjust href if you have a real export route) --}}
          <a href="#" class="text-white" data-bs-toggle="tooltip" title="Download Your Info">
            <i class="fa fa-download"></i>
          </a>

          {{-- Trigger Delete Profile --}}
          <button type="button"
                  class="btn p-0 text-white border-0 bg-transparent"
                  data-bs-toggle="tooltip"
                  title="Delete Profile"
                  @click="modalOpen = true">
            <i class="fa fa-trash"></i>
          </button>
        </div>
      </div>

      {{-- Delete modal --}}
      <template x-if="modalOpen">
        <div class="p-3 border border-danger bg-transparent rounded mt-3">
          {{-- First step --}}
          <div x-show="!confirming">
            <p class="text-danger mb-2">
              Are you sure you want to delete your profile? This action is irreversible.
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
                action="{{ route('users.delete-profile') }}"
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
