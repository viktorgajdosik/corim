<div id="admin-listings-index">
  @if (session('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
  @endif

  <div class="card bg-dark mb-3">
    <div class="card-body">
      <div class="row g-2 align-items-end">
        <div class="col-md-6">
          <label class="form-label">Search</label>
          <input type="text" class="form-control" placeholder="Title or author…" wire:model.live.debounce.300ms="search">
        </div>
        <div class="col-md-3">
          <label class="form-label">State</label>
          <select class="form-select" wire:model.live="isOpen">
            <option value="">All</option>
            <option value="1">Open</option>
            <option value="0">Closed</option>
          </select>
        </div>
      </div>
    </div>
  </div>

  <div class="card bg-dark">
    <div class="table-responsive">
      <table class="table table-dark table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>Title</th><th>Author</th><th>Created</th><th>Status</th><th></th>
          </tr>
        </thead>
        <tbody>
          @forelse($rows as $l)
            <tr>
              <td><a href="{{ route('listings.show', $l->id) }}" class="link-light text-decoration-none">{{ $l->title }}</a></td>
              <td class="text-muted">{{ $l->author }}</td>
              <td class="text-muted">{{ $l->created_at->format('d/m/Y') }}</td>
              <td>
                <span class="badge {{ $l->is_open?'bg-success':'bg-secondary' }}">{{ $l->is_open ? 'Open' : 'Closed' }}</span>
              </td>
              <td class="text-end">
                <div class="btn-group btn-group-sm">
                  <button class="btn btn-outline-light" wire:click="toggleOpen({{ $l->id }})">
                    {{ $l->is_open ? 'Close' : 'Open' }}
                  </button>
                  <button class="btn btn-outline-danger" wire:click="deleteListing({{ $l->id }})" onclick="return confirm('Delete this listing?')">
                    Delete
                  </button>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted py-4">No listings found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">
      {{ $rows->links() }}
    </div>
  </div>
</div>
