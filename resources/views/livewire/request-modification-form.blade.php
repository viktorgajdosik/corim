<form class="custom-floating-label" enctype="multipart/form-data"
  x-on:submit.prevent="
    $store.ui.startMod({{ $task->id }});
    /* call your Livewire action — adjust name if different */
    $wire.requestModification().then(() => {
      requestAnimationFrame(() => {
        requestAnimationFrame(() => {
          if ($el.querySelector('.is-invalid')) {
            $store.ui.stopMod({{ $task->id }});
          }
        });
      });
    });
  "
>
  <div class="form-floating mb-3">
    <textarea wire:model.defer="modification_message"
              class="form-control bg-dark text-white @error('modification_message') is-invalid @enderror"
              placeholder="Write what needs to be changed"
              style="height: 120px"></textarea>
    <label class="text-white">Request Modification</label>
    @error('modification_message') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

  <button type="submit" class="btn btn-danger btn-sm d-inline-flex align-items-center gap-2"
          :disabled="$store.ui.modLoading[{{ $task->id }}] === true">
    <template x-if="!$store.ui.modLoading[{{ $task->id }}]">
      <span>Send Modification Request</span>
    </template>
    <template x-if="$store.ui.modLoading[{{ $task->id }}]">
        <span class="d-inline-flex align-items-center gap-1">Sending <span class="spinner-grow spinner-grow-sm align-middle"></span></span>
    </template>
  </button>
</form>
