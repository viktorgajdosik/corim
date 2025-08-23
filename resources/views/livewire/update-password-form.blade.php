<form class="custom-floating-label"
      x-on:submit.prevent="
        $store.ui.startPasswordUpdate({{ $user->id }});
        $wire.save().then(() => {
          // stop spinner if validation errors
          requestAnimationFrame(() => {
            requestAnimationFrame(() => {
              if ($el.querySelector('.is-invalid')) {
                $store.ui.stopPasswordUpdate({{ $user->id }});
              }
            });
          });
        });
      "
>
  {{-- Old Password --}}
  <div class="form-floating mb-3">
    <input type="password"
           id="old_password"
           wire:model.defer="old_password"
           class="form-control form-control-md bg-dark border-secondary text-white @error('old_password') is-invalid @enderror"
           placeholder="Old Password">
    <label for="old_password">Old Password</label>
    @error('old_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  {{-- New Password --}}
  <div class="form-floating mb-3">
    <input type="password"
           id="password"
           wire:model.defer="password"
           class="form-control form-control-md bg-dark border-secondary text-white @error('password') is-invalid @enderror"
           placeholder="New Password">
    <label for="password">New Password</label>
    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  {{-- Confirm --}}
  <div class="form-floating mb-3">
    <input type="password"
           id="password_confirmation"
           wire:model.defer="password_confirmation"
           class="form-control form-control-md bg-dark border-secondary text-white"
           placeholder="Confirm New Password">
    <label for="password_confirmation">Confirm New Password</label>
  </div>

  <button type="submit"
          class="btn btn-primary btn-sm d-inline-flex align-items-center gap-2"
          :disabled="$store.ui.passwordUpdateLoading?.[{{ $user->id }}] === true">
    <template x-if="$store.ui.passwordUpdateLoading?.[{{ $user->id }}] !== true">
      <span>Update Password</span>
    </template>
    <template x-if="$store.ui.passwordUpdateLoading?.[{{ $user->id }}] === true">
      <span class="d-inline-flex align-items-center gap-1">
        Saving
        <span class="spinner-grow spinner-grow-sm align-middle"></span>
      </span>
    </template>
  </button>
</form>
