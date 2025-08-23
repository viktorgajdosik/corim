<section id="tasks"
  x-data
  x-init="
    const root = $el.closest('[wire\\:id]');
    const thisId = root ? root.getAttribute('wire:id') : null;
    if (!thisId) return;

    // Prevent double-registration if this component re-renders
    window.__tasksHookRegistry ??= new Set();
    if (window.__tasksHookRegistry.has(thisId)) return;
    window.__tasksHookRegistry.add(thisId);

    Livewire.hook('message.processed', (message, component) => {
      if (component.id === thisId) {
        // Fire after Livewire morph + next paint frame
        requestAnimationFrame(() => {
          requestAnimationFrame(() => {
            window.dispatchEvent(new CustomEvent('tasksPatchComplete'));
          });
        });
      }
    });
  "
>
  <x-secondary-heading>Create Task</x-secondary-heading>

  {{-- Create Task card: same pattern as task-card (wire:init + loading overlay + reveal) --}}
  @if ($listing->applications()->where('accepted', true)->exists())
    <x-card-form>
      <div
        wire:init="readyCreate"
        class="position-relative"
        style="min-height: 105px; overflow: hidden;"
      >
        {{-- =======================
             SKELETON OVERLAY (lines + pills)
             ======================= --}}
        <div class="task-skeleton-overlay"
             wire:loading
             wire:target="readyCreate"
             aria-hidden="true">
          {{-- Title line --}}
          <div class="skeleton-line w-100 mb-2"></div>
           <div class="skeleton-line w-100 mb-2"></div>
            <div class="skeleton-line w-100 mb-2"></div>
             <div class="skeleton-line w-100"></div>

        </div>

        {{-- =======================
             REAL CARD CONTENT
             ======================= --}}
        <div @unless($createReady) class="d-none" @endunless>
          @livewire('create-task-form', ['listing' => $listing], key('create-task-form-' . $listing->id))
        </div>
      </div>
    </x-card-form>
  @else
    <x-text class="text-white mb-5">You must have accepted students before you can assign tasks.</x-text>
  @endif

  <x-secondary-heading class="mt-4">All Tasks</x-secondary-heading>

  @foreach ($tasks as $task)
    <div
      class="task-card-wrap"
      data-task-id="{{ $task->id }}"
      data-updated-at="{{ optional($task->updated_at)->timestamp }}"
      wire:key="wrap-{{ $task->id }}-{{ optional($task->updated_at)->timestamp }}"
    >
      @livewire('task-card',
        ['task' => $task, 'listing' => $listing],
        key('task-card-' . $task->id . '-' . optional($task->updated_at)->timestamp)
      )
    </div>
  @endforeach
</section>
