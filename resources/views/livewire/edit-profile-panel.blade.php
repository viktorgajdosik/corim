<x-card-form>
  <div
    wire:init="readyPanel"
    class="position-relative"
    style="min-height: 130px; overflow: hidden;"
    data-user-id="{{ $user->id }}"
    data-updated-at="{{ optional($user->updated_at)->timestamp }}"
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
      {{-- FORM 1: Profile info --}}
      @livewire('edit-profile-form', ['user' => $user], key('edit-profile-form-'.$user->id.'-'.optional($user->updated_at)->timestamp))

      <hr class="my-4">

      {{-- FORM 2: Password --}}
      @livewire('update-password-form', ['user' => $user], key('update-password-form-'.$user->id))
    </div>
  </div>
</x-card-form>
