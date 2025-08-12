@php
  $ts = optional($task->updated_at)->timestamp;
@endphp

<x-card-form>
  <div
    x-data="{ detailsOpen: false, editOpen: false, modOpen: false }"
    x-init="console.log('✅ Alpine initialized for task {{ $task->id }}')"
  >
    {{-- Header with task title and controls --}}
    <div class="d-flex justify-content-between align-items-start">
      <x-card-heading>{{ $task->name }}</x-card-heading>

      <div class="d-flex gap-1 ms-auto">
        {{-- Edit & Delete (only visible when detailsOpen is true) --}}
        <template x-if="detailsOpen">
          <div class="d-flex gap-1">
            {{-- Edit --}}
            <button class="btn btn-sm"
                    @click="editOpen = !editOpen"
                    :title="editOpen ? 'Close Edit' : 'Edit Task'">
              <i :class="editOpen ? 'fa fa-times text-white' : 'fa fa-pencil text-white'"></i>
            </button>

            {{-- Delete --}}
            <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
              @csrf
              @method('DELETE')
              <button class="btn btn-sm" title="Delete Task">
                <i class="fa fa-trash text-white"></i>
              </button>
            </form>
          </div>
        </template>

        {{-- Arrow toggle (always visible) --}}
        <button class="btn btn-sm"
                @click="detailsOpen = !detailsOpen"
                :title="detailsOpen ? 'Hide Details' : 'See More'">
          <i :class="detailsOpen ? 'fa fa-chevron-up text-white' : 'fa fa-chevron-down text-white'"></i>
        </button>
      </div>
    </div>

    {{-- Status and metadata --}}
    <x-text class="d-flex align-items-center gap-2 mb-0">
      <span class="badge bg-{{ $task->status === 'finished' ? 'success' : ($task->status === 'modification_requested' ? 'warning text-dark' : 'secondary') }}">
        {{ ucfirst(str_replace('_', ' ', $task->status)) }}
      </span>

      @php
        $assignedStillAccepted = $listing->applications()->where('accepted', true)->pluck('user_id')->contains($task->assigned_user_id);
      @endphp

      @if ($task->assignedUser && $assignedStillAccepted)
        <small><i class="fa fa-user me-1"></i> {{ $task->assignedUser->name }}</small>
      @else
        <small><i class="fa fa-user me-1"></i> User removed</small>
      @endif

      <small class="text-white"><i class="fa fa-calendar me-1"></i> Created {{ $task->created_at->format('d/m/Y') }}</small>

      @if ($task->deadline)
        <small class="text-white"><i class="fa fa-hourglass-end me-1"></i> Due {{ $task->deadline->format('d/m/Y') }}</small>
      @endif

      @if ($task->deadline && $task->status !== 'finished' && now()->gt($task->deadline))
        <span class="badge bg-danger">Overdue</span>
      @endif
    </x-text>

    {{-- Expanded view --}}
    <div x-show="detailsOpen" x-cloak x-transition.duration.150ms class="mt-2">
      {{-- Edit Form --}}
      <div x-show="editOpen" x-cloak x-transition.duration.150ms>
        @livewire('update-task-form', ['task' => $task], key('update-task-' . $task->id . '-' . $ts))
      </div>

      {{-- Description (only when not editing) --}}
      <div x-show="!editOpen" x-cloak x-transition.duration.150ms>
        <x-text class="mt-2">{{ $task->description }}</x-text>

        @if ($task->file)
          <a href="{{ \Storage::url($task->file) }}" class="btn btn-outline-light btn-sm mt-2" target="_blank">
            <i class="fa fa-download me-1"></i>Download assignment file
          </a>
        @endif

        {{-- Student Submission --}}
        <x-card-heading class="mt-4 pt-3 border-top border-secondary">Student Submission</x-card-heading>
        @if ($task->result_text || $task->result_file)
          @if ($task->result_text)
            <x-text>{{ $task->result_text }}</x-text>
          @endif
          <div>
          @if ($task->result_file)
            <a href="{{ \Storage::url($task->result_file) }}" class="btn btn-outline-light btn-sm mb-3" target="_blank">
              <i class="fa fa-download me-1"></i>Download submission file
            </a>
          @endif
           </div>
        @else
          <p class="text-warning mt-3">The participant has not submitted any work for this task yet.</p>
        @endif

        @if ($task->status === 'modification_requested' && $task->modification_note)
          <div class="alert alert-warning p-2 mb-3">
            <strong>Modification request:</strong> {{ $task->modification_note }}
          </div>
        @endif

        {{-- Livewire Action Buttons --}}
        @if (auth()->id() === $listing->user_id)
          @if (in_array($task->status, ['assigned', 'submitted', 'modification_requested']))
            @livewire('mark-task-as-finished', ['task' => $task], key('finish-task-' . $task->id . '-' . $ts))
          @endif

          @if (in_array($task->status, ['submitted', 'modification_requested']))
            <div class="mt-3">
              <button type="button" class="btn btn-danger btn-sm mb-2"
                      @click="modOpen = !modOpen">
                <template x-if="modOpen">
                  <span><i class="fa fa-times"></i> Close Request</span>
                </template>
                <template x-if="!modOpen">
                  <span>{{ $task->modification_note ? 'Edit' : '' }} Modification Request</span>
                </template>
              </button>

              <div x-show="modOpen" x-cloak x-transition.duration.150ms>
                @livewire('request-modification-form', ['task' => $task], key('modification-' . $task->id . '-' . $ts))
              </div>
            </div>
          @endif

          @if ($task->status === 'finished')
            @livewire('reopen-task', ['task' => $task], key('reopen-task-' . $task->id . '-' . $ts))
          @endif
        @endif
      </div>
    </div>
  </div>
</x-card-form>
