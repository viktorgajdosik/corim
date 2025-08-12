<form class="custom-floating-label" enctype="multipart/form-data"
  x-on:submit.prevent="
    $store.ui.startUpdate({{ $task->id }});
    $wire.submit().then(() => {
      requestAnimationFrame(() => {
        requestAnimationFrame(() => {
          if ($el.querySelector('.is-invalid')) {
            $store.ui.stopUpdate({{ $task->id }});
          }
        });
      });
    });
  "
>

  {{-- Text --}}
  <div class="form-floating mb-3">
    <textarea wire:model.defer="result_text"
              class="form-control bg-dark text-white @error('result_text') is-invalid @enderror"
              id="result_text_{{ $task->id }}"
              style="height: 120px"
              placeholder="Write your results here"></textarea>
    <label for="result_text_{{ $task->id }}">Task progress</label>
    @error('result_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
  </div>

 {{-- File --}}
<div class="mb-3">
  <div class="input-group">
    <label class="btn btn-outline-light rounded">
      <i class="fa fa-upload me-1"></i> Upload Result File
      <input type="file" wire:model="result_file" class="d-none">
    </label>
    <span class="btn border-0 disabled rounded small text-white">
      <span wire:loading wire:target="result_file" class="spinner-border spinner-border-sm align-middle"></span>
      <span wire:loading.remove wire:target="result_file">
        {{ $result_file ? $result_file->getClientOriginalName() : ($task->result_file ? basename($task->result_file) : 'No file selected') }}
      </span>
    </span>
  </div>
  @error('result_file') <div class="text-danger mt-1">{{ $message }}</div> @enderror
</div>

 {{-- Actions --}}
<div class="d-flex gap-2" x-data="{ resuming: false }">
  {{-- Submit --}}
  <button type="submit"
          class="btn btn-primary btn-sm d-inline-flex align-items-center gap-2 align-items-center"
          :disabled="$store.ui.updateLoading[{{ $task->id }}] === true">
    <template x-if="!$store.ui.updateLoading[{{ $task->id }}]">
      <span>Submit</span>
    </template>
    <template x-if="$store.ui.updateLoading[{{ $task->id }}]">
      <span class="d-inline-flex align-items-center gap-1">
        Submitting
        <span class="spinner-grow spinner-grow-sm align-middle"></span>
      </span>
    </template>
  </button>

  {{-- Reset (local-only spinner) --}}
  <button type="button"
          class="btn btn-danger btn-sm d-inline-flex align-items-center gap-2"
          :disabled="resuming || $store.ui.updateLoading[{{ $task->id }}] === true"
          @click="
            resuming = true;
            $wire.clearForm().then(() => { resuming = false })
                            .catch(() => { resuming = false });
          ">
    <template x-if="!resuming">
      <span>Reset</span>
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
