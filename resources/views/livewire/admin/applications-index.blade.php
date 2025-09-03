<div id="admin-applications-index">
  @if (session('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
  @endif

  <div class="card bg-dark mb-3">
    <div class="card-body">
      <div class="row g-2 align-items-end">
        <div class="col-md-6">
          <label class="form-label">Search</label>
          <input type="text" class="form-control" placeholder="Applicant or listing…" wire:model.debounce.400ms="search">
        </div>
        <div class="col-md-3">
          <label class="form-label">Status</label>
          <select class="form-select" wire:model="status">
            <option value="">All</option>
            <option value="accepted">Accepted</option>
            <option value="awaiting">Awaiting / Not accepted</option>
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
            <th>Applicant</th><th>Listing</th><th>Applied</th><th>Status</th><th></th>
          </tr>
        </thead>
        <tbody>
          @forelse($rows as $a)
            <tr>
              <td>{{ $a->user?->name ?? '—' }}</td>
              <td>
                @if($a->listing)
                  <a href="{{ route('listings.show', $a->listing->id) }}" class="link-light text-decoration-none">
                    {{ $a->listing->title }}
                  </a>
                @else
                  —
                @endif
              </td>
              <td class="text-muted">{{ $a->created_at->format('d/m/Y') }}</td>
              <td>
                <span class="badge {{ $a->accepted ? 'bg-success' : 'bg-secondary' }}">
                  {{ $a->accepted ? 'Accepted' : 'Awaiting' }}
                </span>
              </td>
              <td class="text-end">
                <div class="btn-group btn-group-sm">
                  @if(!$a->accepted)
                    <button class="btn btn-outline-success" wire:click="accept({{ $a->id }})">Accept</button>
                  @else
                    <button class="btn btn-outline-secondary" wire:click="deny({{ $a->id }})">Mark Awaiting</button>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted py-4">No applications found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">
      {{ $rows->links() }}
    </div>
  </div>
</div>
