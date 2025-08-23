<form class="custom-floating-label"
      x-data="{ resuming: false }"
      x-on:submit.prevent="
        $store.ui.startListingUpdate({{ $listing->id }});
        $wire.save().then(() => {
          // Stop spinner immediately if validation errors appeared
          requestAnimationFrame(() => {
            requestAnimationFrame(() => {
              if ($el.querySelector('.is-invalid')) {
                $store.ui.stopListingUpdate({{ $listing->id }});
              }
            });
          });
        });
      "
>
  {{-- Title --}}
  <div class="form-floating mb-3">
    <input type="text"
           id="title"
           wire:model.defer="title"
           class="form-control form-control-md text-white bg-dark @error('title') is-invalid @enderror"
           placeholder="Title">
    <label for="title">Title</label>
    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
    <small class="form-text text-secondary">Minimum 10 characters required.</small>
  </div>

  {{-- Description --}}
  <div class="form-floating mb-3">
    <textarea id="description"
              wire:model.defer="description"
              class="form-control form-control-md text-white bg-dark @error('description') is-invalid @enderror"
              placeholder="Description"
              style="height: 200px"></textarea>
    <label for="description">Description</label>
    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    <small class="form-text text-secondary">Minimum 50 characters required.</small>
  </div>

  {{-- Department --}}
  <div class="form-floating mb-3">
    <select id="department"
            wire:model.defer="department"
            class="form-select form-control-md text-white bg-dark @error('department') is-invalid @enderror">
      <option value="" disabled>Select a department</option>
      @foreach ($departments as $dept)
        <option value="{{ $dept }}">{{ $dept }}</option>
      @endforeach
    </select>
    <label for="department">Department</label>
    @error('department') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  {{-- Actions: Primary Save + Secondary Reset (with its own spinner) --}}
  <div class="d-flex gap-2 mt-3">
    {{-- Save --}}
    <button type="submit"
            class="btn btn-primary btn-sm d-inline-flex align-items-center gap-2"
            :disabled="$store.ui.listingUpdateLoading?.[{{ $listing->id }}] === true">
      <template x-if="$store.ui.listingUpdateLoading?.[{{ $listing->id }}] !== true">
        <span>Update Listing</span>
      </template>
      <template x-if="$store.ui.listingUpdateLoading?.[{{ $listing->id }}] === true">
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
