<div id="admin-broadcast">
  @if (session('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
  @endif

  <div class="row g-3">
    <div class="col-xl-4">
      <div class="card bg-dark h-100">
        <div class="card-body">
          <h5 class="card-title mb-3">Audience</h5>

          <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" id="sendAll" wire:model.live="sendToAll">
            <label class="form-check-label" for="sendAll">Send to all users</label>
          </div>

          <div class="mb-3">
            <label class="form-label">Search users</label>
            <input type="text" class="form-control" placeholder="Name or email…" wire:model.live.debounce.300ms="search" @disabled($sendToAll)>
          </div>

          <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="small text-muted">Top {{ $results->count() }} results</div>
            <div class="btn-group btn-group-sm">
              <button class="btn btn-outline-light" wire:click="toggleSelectShown(true)" @disabled($sendToAll)>Select shown</button>
              <button class="btn btn-outline-secondary" wire:click="toggleSelectShown(false)" @disabled($sendToAll)>Unselect shown</button>
            </div>
          </div>

          <div class="list-group mb-3" style="max-height: 280px; overflow:auto;">
            @forelse($results as $u)
              <label class="list-group-item list-group-item-action d-flex align-items-center gap-2">
                <input class="form-check-input me-1" type="checkbox" value="{{ $u->id }}" wire:model.live="selectedIds" @disabled($sendToAll)>
                <div class="flex-fill">
                  <div>{{ $u->name }}</div>
                  <div class="small text-muted">{{ $u->email }}</div>
                </div>
                @if($u->email_verified_at)
                  <span class="badge bg-success">verified</span>
                @else
                  <span class="badge bg-secondary">unverified</span>
                @endif
              </label>
            @empty
              <div class="text-muted small px-2 py-2">No matches.</div>
            @endforelse
          </div>

          <div class="small text-muted">
            Selected: <strong>{{ count($selectedIds) }}</strong> @if(!$sendToAll)(persists even if not shown)@endif
          </div>
          @error('selectedIds') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
        </div>
      </div>
    </div>

    <div class="col-xl-8">
      <div class="card bg-dark mb-3">
        <div class="card-body">
          <h5 class="card-title mb-3">In-app notification</h5>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Title</label>
              <input type="text" class="form-control" wire:model.defer="note_title" placeholder="Short headline">
              @error('note_title') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6">
              <label class="form-label">Optional URL</label>
              <input type="text" class="form-control" wire:model.defer="note_url" placeholder="https://…">
              @error('note_url') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
            <div class="col-12">
              <label class="form-label">Message</label>
              <textarea class="form-control" rows="4" wire:model.defer="note_body" placeholder="Write the notification text…"></textarea>
              @error('note_body') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
          <button class="btn btn-outline-light" wire:click="sendNotification">
            Send notification
          </button>
        </div>
      </div>

      <div class="card bg-dark">
        <div class="card-body">
          <h5 class="card-title mb-3">Email</h5>
          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label">Subject</label>
              <input type="text" class="form-control" wire:model.defer="email_subject" placeholder="Subject">
              @error('email_subject') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4">
              <label class="form-label">Recipients</label>
              <div class="form-check form-switch mt-2">
                <input class="form-check-input" type="checkbox" id="onlyVerified" wire:model="email_verified_only">
                <label class="form-check-label" for="onlyVerified">Only verified emails</label>
              </div>
            </div>
            <div class="col-12">
              <label class="form-label">Body</label>
              <textarea class="form-control" rows="6" wire:model.defer="email_body" placeholder="Write the email…"></textarea>
              @error('email_body') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>
          </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
          <button class="btn btn-outline-light" wire:click="sendEmail">
            Send email
          </button>
        </div>
      </div>
    </div>
  </div>
</div>
