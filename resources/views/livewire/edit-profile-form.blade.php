<form class="custom-floating-label"
      x-data="{ resuming: false }"
      x-on:submit.prevent="
        $store.ui.startProfileUpdate({{ $user->id }});
        $wire.save().then(() => {
          // stop spinner immediately if validation errors appeared
          requestAnimationFrame(() => {
            requestAnimationFrame(() => {
              if ($el.querySelector('.is-invalid')) {
                $store.ui.stopProfileUpdate({{ $user->id }});
              }
            });
          });
        });
      "
>
  {{-- Name --}}
  <div class="form-floating mb-3">
    <input type="text"
           id="name"
           wire:model.defer="name"
           class="form-control form-control-md bg-dark text-white border-secondary @error('name') is-invalid @enderror"
           placeholder="Name, Surname, Titles">
    <label for="name">Name, Surname, Titles</label>
    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  {{-- Department --}}
  <div class="form-floating mb-3">
    <select id="department"
            wire:model.defer="department"
            class="form-select form-control-md bg-dark text-white border-secondary @error('department') is-invalid @enderror">
      <option value="" disabled {{ !$department ? 'selected' : '' }}>Select Your Department</option>
      @foreach($departments as $dept)
        <option value="{{ $dept }}">{{ $dept }}</option>
      @endforeach
    </select>
    <label for="department">Department</label>
    @error('department') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <div class="d-flex gap-2 mt-2">
    {{-- Save --}}
    <button type="submit"
            class="btn btn-primary btn-sm d-inline-flex align-items-center gap-2"
            :disabled="$store.ui.profileUpdateLoading?.[{{ $user->id }}] === true">
      <template x-if="$store.ui.profileUpdateLoading?.[{{ $user->id }}] !== true">
        <span>Update Profile</span>
      </template>
      <template x-if="$store.ui.profileUpdateLoading?.[{{ $user->id }}] === true">
        <span class="d-inline-flex align-items-center gap-1">
          Saving
          <span class="spinner-grow spinner-grow-sm align-middle"></span>
        </span>
      </template>
    </button>

    {{-- Reset --}}
    <button type="button"
            class="btn btn-danger btn-sm d-inline-flex align-items-center gap-2"
            :disabled="resuming"
            @click="
              resuming = true;
              $wire.resetForm().then(() => resuming = false);
            ">
      <template x-if="!resuming">
        <span>Reset Changes</span>
      </template>
      <template x-if="resuming">
        <span class="d-inline-flex align-items-center gap-1">
          Resetting
          <span class="spinner-grow spinner-grow-sm align-middle"></span>
        </span>
      </template>
    </button>
  </div>
</form>
