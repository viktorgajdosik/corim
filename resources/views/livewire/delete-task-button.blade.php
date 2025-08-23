<div>
    {{-- Delete button with optimistic spinner (uses Alpine store $store.ui.deleteLoading[taskId]) --}}
    <button
        type="button"
        class="btn btn-sm"
        :disabled="$store.ui.deleteLoading?.[{{ $task->id }}] === true"
        x-on:click.prevent="
            (async () => {
                if (!confirm('Are you sure you want to delete this task?')) return;
                $store.ui.startDelete({{ $task->id }});
                // Fire Livewire delete()
                await $wire.delete();
                // DO NOT stop spinner here; app.js will stop it after the DOM no longer contains this task card.
            })()
        "
        title="Delete Task"
    >
        <template x-if="$store.ui.deleteLoading?.[{{ $task->id }}] !== true">
            <i class="fa fa-trash text-white"></i>
        </template>

        <template x-if="$store.ui.deleteLoading?.[{{ $task->id }}] === true">
            <span class="spinner-grow spinner-grow-sm align-middle"></span>
        </template>
    </button>
</div>
