
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

  @if ($listing->applications()->where('accepted', true)->exists())
    <x-card-form>
      @livewire('create-task-form', ['listing' => $listing], key('create-task-form-' . $listing->id))
    </x-card-form>

    <x-secondary-heading class="mt-4">All Tasks</x-secondary-heading>
  @else
    <x-text class="text-white mb-4">You must have accepted students before you can assign tasks.</x-text>
  @endif

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
