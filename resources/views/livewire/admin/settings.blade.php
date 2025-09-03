<div id="admin-settings">
  @if (session('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
  @endif

  <div class="card bg-dark">
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Site name</label>
          <input type="text" class="form-control" wire:model.defer="site_name">
          @error('site_name') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-6">
          <label class="form-label">Support email</label>
          <input type="email" class="form-control" wire:model.defer="support_email">
          @error('support_email') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>
        <div class="col-md-12">
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="regOpen" wire:model="registration_open">
            <label class="form-check-label" for="regOpen">Registrations open</label>
          </div>
        </div>
      </div>
    </div>
    <div class="card-footer d-flex justify-content-end">
      <button class="btn btn-primary" wire:click="save" wire:loading.attr="disabled">
        <span wire:loading.remove>Save settings</span>
        <span wire:loading class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
      </button>
    </div>
  </div>
</div>
