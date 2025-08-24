<div>
<button
  class="btn btn-primary btn-sm d-inline-flex align-items-center gap-2"
  :disabled="$store.ui.updateLoading[{{ $task->id }}] === true"
  x-on:click="$store.ui.startUpdate({{ $task->id }}); $wire.markAsFinished();"
>
  <template x-if="!$store.ui.updateLoading[{{ $task->id }}]">
    <span>Mark task as finished</span>
  </template>
  <template x-if="$store.ui.updateLoading[{{ $task->id }}]">
      <span class="d-inline-flex align-items-center gap-1">Finishing <span class="spinner-grow spinner-grow-sm align-middle"></span></span>
  </template>
</button>
</div>
