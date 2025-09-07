<div id="admin-carousel-settings">
  @if (session('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
  @endif

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0">Home carousel</h1>
    <div class="d-flex gap-2">
      <button class="btn btn-outline-light btn-sm" wire:click="add">
        <i class="fa fa-plus me-1"></i> Add slide
      </button>
      <button class="btn btn-outline-light btn-sm" wire:click="save" wire:loading.attr="disabled">
        <span wire:loading.remove>Save</span>
        <span wire:loading class="spinner-grow spinner-grow-sm"></span>
      </button>
    </div>
  </div>

  <div class="row g-3">
    @forelse($slides as $i => $s)
      <div class="col-12">
        <div class="card bg-dark border-secondary-subtle">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <div class="h6 mb-0">Slide #{{ $i+1 }}</div>
              <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-light" wire:click="moveUp({{ $i }})" @disabled($i===0) title="Up"><i class="fa fa-arrow-up"></i></button>
                <button class="btn btn-outline-light" wire:click="moveDown({{ $i }})" @disabled(!isset($slides[$i+1])) title="Down"><i class="fa fa-arrow-down"></i></button>
                <button class="btn btn-outline-danger" wire:click="remove({{ $i }})" onclick="return confirm('Remove this slide?')"><i class="fa fa-trash"></i></button>
              </div>
            </div>

            <div class="row g-3 mt-1">
              <div class="col-md-6">
                <label class="form-label">Title</label>
                <input type="text" class="form-control" wire:model.defer="slides.{{ $i }}.title">
              </div>
              <div class="col-md-6">
                <label class="form-label">Subtitle</label>
                <input type="text" class="form-control" wire:model.defer="slides.{{ $i }}.subtitle">
              </div>
              <div class="col-md-3">
                <label class="form-label">CTA text</label>
                <input type="text" class="form-control" wire:model.defer="slides.{{ $i }}.cta_text" placeholder="e.g. Register">
              </div>
              <div class="col-md-7">
                <label class="form-label">CTA URL</label>
                <input type="text" class="form-control" wire:model.defer="slides.{{ $i }}.cta_url" placeholder="{{ route('register') }}">
              </div>
              <div class="col-md-2 d-flex align-items-end">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="en-{{ $i }}" wire:model="slides.{{ $i }}.enabled">
                  <label class="form-check-label" for="en-{{ $i }}">Enabled</label>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="text-muted">No slides yet. Click <em>Add slide</em>.</div>
      </div>
    @endforelse
  </div>
</div>
