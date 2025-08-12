<button
  class="btn btn-primary btn-sm d-inline-flex align-items-center gap-2"
  :disabled="$store.ui.updateLoading[{{ $task->id }}] === true"
  x-on:click="$store.ui.startUpdate({{ $task->id }}); $wire.reopen();"
>
  <template x-if="!$store.ui.updateLoading[{{ $task->id }}]">
    <span>Reopen Task</span>
  </template>
  <template x-if="$store.ui.updateLoading[{{ $task->id }}]">
     <span class="d-inline-flex align-items-center gap-1">Reopening <span class="spinner-grow spinner-grow-sm align-middle"></span></span>
  </template>
</button>
