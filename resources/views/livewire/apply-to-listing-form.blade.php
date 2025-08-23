<form class="custom-floating-label"
      x-on:submit.prevent="
        $store.ui.startAppApply({{ $listing->id }});
        $wire.submit().then(() => {
          // Stop spinner on validation errors
          requestAnimationFrame(() => {
            requestAnimationFrame(() => {
              if ($el.querySelector('.is-invalid')) {
                $store.ui.stopAppApply({{ $listing->id }});
              }
            });
          });
        });
      "
>
  <div class="form-floating mb-3">
    <textarea
      wire:model.defer="message"
      id="message"
      name="message"
      placeholder="Enter message"
      style="height: 150px"
      class="form-control text-white bg-dark border-1 @error('message') is-invalid @enderror"
      required
    ></textarea>
    <label for="message">Enter message</label>
    @error('message') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <button type="submit"
          class="btn btn-primary btn-sm d-inline-flex align-items-center gap-2"
          :disabled="$store.ui.appApplyLoading?.[{{ $listing->id }}] === true">
    <template x-if="$store.ui.appApplyLoading?.[{{ $listing->id }}] !== true">
      <span>Apply</span>
    </template>
    <template x-if="$store.ui.appApplyLoading?.[{{ $listing->id }}] === true">
      <span class="d-inline-flex align-items-center gap-1">
        Applying
        <span class="spinner-grow spinner-grow-sm align-middle"></span>
      </span>
    </template>
  </button>
</form>
