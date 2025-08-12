<form class="custom-floating-label" enctype="multipart/form-data"
  x-on:submit.prevent="
    $store.ui.startUpdate({{ $task->id }});
    $wire.update().then(() => {
      // If there were validation errors, Livewire has re-rendered,
      // so check for any invalid fields and stop the spinner locally.
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
    {{-- Name --}}
    <div class="form-floating mb-3">
      <input type="text" wire:model.defer="task_name" class="form-control bg-dark text-white @error('task_name') is-invalid @enderror" id="task-name" placeholder="Task Name">
      <label for="task-name" class="text-white">Task Name</label>
      @error('task_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Description --}}
    <div class="form-floating mb-3">
      <textarea wire:model.defer="task_details" class="form-control bg-dark text-white @error('task_details') is-invalid @enderror" id="task-details" style="height: 200px" placeholder="Task Details"></textarea>
      <label for="task-details" class="text-white">Task Details</label>
      @error('task_details') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Deadline --}}
    <div class="form-floating mb-3">
      <input type="date" wire:model.defer="deadline" min="{{ now()->format('Y-m-d') }}" id="deadline" class="form-control bg-dark text-white @error('deadline') is-invalid @enderror">
      <label for="deadline" class="text-white">Deadline <span class="text-secondary">(optional)</span></label>
      @error('deadline') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Assign User --}}
    <div class="form-floating mb-3">
      <select wire:model.defer="assigned_user_id" class="form-select bg-dark text-white @error('assigned_user_id') is-invalid @enderror" id="assigned_user_id">
        <option value="" disabled>Choose participant</option>
        @foreach ($participants as $applicant)
          <option value="{{ $applicant->user->id }}">
            {{ $applicant->user->name }} ({{ $applicant->user->email }})
          </option>
        @endforeach
      </select>
      <label for="assigned_user_id">Reassign Task</label>
      @error('assigned_user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

   {{-- File Upload --}}
<div class="mb-3">
  <div class="input-group">
    <label class="btn btn-outline-light rounded">
      <i class="fa fa-upload me-1"></i> Change file
      <input type="file" wire:model="task_file" class="d-none">
    </label>
    <span class="btn border-0 disabled rounded text-white">
      <span wire:loading wire:target="task_file" class="spinner-border spinner-border-sm align-middle"></span>
      <span wire:loading.remove wire:target="task_file">
        {{ $task_file ? $task_file->getClientOriginalName() : ($task->file ? basename($task->file) : 'No file selected') }}
        <span class="text-secondary">(optional)</span>
      </span>
    </span>
  </div>
  @error('task_file') <div class="text-danger mt-1">{{ $message }}</div> @enderror
</div>

    <div class="d-flex gap-2 mt-3" x-data="{ resuming: false }">
  {{-- Submit --}}
<button type="submit"
        class="btn btn-primary btn-sm d-inline-flex align-items-center gap-2"
        :disabled="$store.ui.updateLoading[{{ $task->id }}] === true">

  <template x-if="!$store.ui.updateLoading[{{ $task->id }}]">
    <span>Update Task</span>
  </template>

  <template x-if="$store.ui.updateLoading[{{ $task->id }}]">
    <span class="d-inline-flex align-items-center gap-1">
      Updating
      <span class="spinner-grow spinner-grow-sm align-middle"></span>
    </span>
  </template>

</button>

  {{-- Resume --}}
  <button type="button"
          class="btn btn-danger btn-sm d-inline-flex align-items-center gap-2"
          :disabled="resuming"
          @click="
            resuming = true;
            $wire.resetForm().then(() => resuming = false);
          ">
    <template x-if="!resuming">
      <span>Reset</span>
    </template>
    <template x-if="resuming">
      <span class="d-inline-flex align-items-center gap-1">Resetting <span class="spinner-grow spinner-grow-sm align-middle"></span></span>
    </template>
  </button>
</div>

  </form>
