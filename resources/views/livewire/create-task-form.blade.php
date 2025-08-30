<div wire:poll.10s="refreshParticipants">
<form class="custom-floating-label" enctype="multipart/form-data"
  x-on:submit.prevent="
    $store.ui.startCreate();
    $wire.create().then(() => {
      requestAnimationFrame(() => {
        requestAnimationFrame(() => {
          if ($el.querySelector('.is-invalid')) {
            $store.ui.stopCreate();
          }
        });
      });
    });
  "
>
    {{-- Task Name --}}
    <div class="form-floating mb-3">
      <input type="text" class="form-control bg-dark text-white @error('task_name') is-invalid @enderror"
             wire:model.defer="task_name" id="task-name" placeholder="Task Name">
      <label for="task-name" class="text-white">Task Name</label>
      @error('task_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Task Details --}}
    <div class="form-floating mb-3">
      <textarea class="form-control bg-dark text-white @error('task_details') is-invalid @enderror"
                wire:model.defer="task_details" id="task-details" placeholder="Task Details"
                style="height: 200px"></textarea>
      <label for="task-details" class="text-white">Task Details</label>
      @error('task_details') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Deadline --}}
    <div class="form-floating mb-3">
      <input type="date" wire:model.defer="deadline"
             id="deadline" class="form-control bg-dark text-white @error('deadline') is-invalid @enderror"
             min="{{ now()->format('Y-m-d') }}">
      <label for="deadline" class="text-white">Deadline <span class="text-secondary">(optional)</span></label>
      @error('deadline') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- Assign To (same look as before) --}}
    <div class="form-floating mb-3">
      <select wire:model.defer="assigned_user_id"
              class="form-select bg-dark text-white @error('assigned_user_id') is-invalid @enderror"
              id="assigned_user_id">
        <option value="" @if(!$assigned_user_id) selected @endif>Choose participant</option>
        @foreach ($participants as $applicant)
          <option value="{{ $applicant->user->id }}" wire:key="opt-create-{{ $applicant->user->id }}">
            {{ $applicant->user->name }} ({{ $applicant->user->email }})
          </option>
        @endforeach
      </select>
      <label for="assigned_user_id">Assign task</label>
      @error('assigned_user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    {{-- File Upload --}}
    <div class="mb-3">
      <div class="input-group">
        <label class="btn btn-outline-light rounded">
          <i class="fa fa-upload me-1"></i> Upload file
          <input type="file" wire:model="task_file" class="d-none">
        </label>
        <span class="btn border-0 disabled rounded text-white">
          <span wire:loading wire:target="task_file" class="spinner-border spinner-border-sm align-middle"></span>
          <span wire:loading.remove wire:target="task_file">
            {{ $task_file ? $task_file->getClientOriginalName() : 'No file selected' }}
            <span class="text-secondary">(optional)</span>
          </span>
        </span>
      </div>
      @error('task_file') <div class="text-danger mt-1">{{ $message }}</div> @enderror
    </div>

    <button type="submit"
            class="btn btn-primary btn-sm d-inline-flex align-items-center gap-2"
            :disabled="$store.ui.createLoading">
      <template x-if="!$store.ui.createLoading">
        <span>Create Task</span>
      </template>
      <template x-if="$store.ui.createLoading">
          <span class="d-inline-flex align-items-center gap-1">Creating <span class="spinner-grow spinner-grow-sm align-middle"></span></span>
      </template>
    </button>
  </form>
</div>
