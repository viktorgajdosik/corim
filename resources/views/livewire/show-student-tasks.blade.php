<section id="student-tasks">
  <x-secondary-heading class="mt-5">Assigned Research Tasks</x-secondary-heading>

  @forelse ($tasks as $task)
    <livewire:student-task-card
      :task="$task"
      :listing="$listing"
      :wire:key="'student-task-card-' . $task->id . '-' . optional($task->updated_at)->timestamp"
    />
  @empty
    <x-text class="text-white">You don't have any assigned tasks for this research work yet.</x-text>
  @endforelse
</section>
