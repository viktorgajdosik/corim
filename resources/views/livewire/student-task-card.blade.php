@php $ts = optional($task->updated_at)->timestamp; @endphp

{{-- Put data attributes on the root so app.js can find the new version --}}
<div data-task-id="{{ $task->id }}" data-updated-at="{{ $ts }}">
  <x-card-form>
    <div x-data="taskCardState({{ $task->id }})"
         x-init="console.log('✅ Student Alpine init for task {{ $task->id }}')">

      {{-- Header --}}
      <div class="d-flex justify-content-between align-items-start">
        <x-card-heading>{{ $task->name }}</x-card-heading>

        <button class="btn btn-sm"
                @click="detailsOpen = !detailsOpen"
                :title="detailsOpen ? 'Hide Details' : 'See More'">
          <i :class="detailsOpen ? 'fa fa-chevron-up text-white' : 'fa fa-chevron-down text-white'"></i>
        </button>
      </div>

      {{-- Status line --}}
      <x-text class="d-flex align-items-center gap-2 mb-0">
        <span class="badge bg-{{ $task->status === 'finished' ? 'success' : ($task->status === 'modification_requested' ? 'warning text-dark' : 'secondary') }}">
          {{ ucfirst(str_replace('_', ' ', $task->status)) }}
        </span>

        <small class="text-white"><i class="fa fa-calendar me-1"></i> Created {{ $task->created_at->format('d/m/Y') }}</small>

        @if ($task->deadline)
          <small class="text-white"><i class="fa fa-hourglass-end me-1"></i> Due {{ $task->deadline->format('d/m/Y') }}</small>
        @endif

        @if ($task->deadline && $task->status !== 'finished' && now()->gt($task->deadline))
          <span class="badge bg-danger">Overdue</span>
        @endif
      </x-text>

      {{-- Expanded content --}}
      <div x-show="detailsOpen" x-cloak x-transition.duration.150ms class="mt-2">
        {{-- Description --}}
        <x-text class="mt-2">{{ $task->description }}</x-text>

        @if ($task->file)
          <a href="{{ \Storage::url($task->file) }}" class="btn btn-outline-light btn-sm mt-2" target="_blank">
            <i class="fa fa-download me-1"></i>Download assignment file
          </a>
        @endif

        {{-- Previous submission / modification note --}}
        @if ($task->result_text || $task->result_file || ($task->status === 'modification_requested' && $task->modification_note))
          <x-card-heading class="mt-4 pt-3 border-top border-secondary">Your Submission</x-card-heading>

          @if ($task->result_text)
            <x-text>{{ $task->result_text }}</x-text>
          @endif

          @if ($task->result_file)
            <a href="{{ \Storage::url($task->result_file) }}" class="btn btn-outline-light btn-sm mb-2" target="_blank">
              <i class="fa fa-download me-1"></i>Download submission file
            </a>
          @endif

          @if ($task->status === 'modification_requested' && $task->modification_note)
            <div class="alert alert-warning p-2 mt-2 mb-0">
              <strong>Modification requested:</strong> {{ $task->modification_note }}
            </div>
          @endif
        @endif

        {{-- Submit form (assigned or modification_requested) --}}
        @if (in_array($task->status, ['assigned', 'modification_requested']))
          <div class="mt-4">
            <livewire:submit-task-form :task="$task" :wire:key="'submit-task-' . $task->id . '-' . $ts" />
          </div>
        @endif
      </div>
    </div>
  </x-card-form>
</div>
