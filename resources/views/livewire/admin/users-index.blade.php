<div id="admin-users-index">
  @if (session('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
  @endif

  <div class="card bg-dark mb-3">
    <div class="card-body">
      <div class="row g-2 align-items-end">
        <div class="col-md-6">
          <label class="form-label">Search</label>
          <input type="text" class="form-control" placeholder="Name or email…" wire:model.debounce.400ms="search">
        </div>
        <div class="col-md-3">
          <label class="form-label">Role filter</label>
          <select class="form-select" wire:model="adminOnly">
            <option value="">All</option>
            <option value="1">Admins</option>
            <option value="0">Non-admins</option>
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
            <th>Name</th><th>Email</th><th>Joined</th><th>Admin</th><th></th>
          </tr>
        </thead>
        <tbody>
          @forelse($rows as $u)
            <tr>
              <td>{{ $u->name }}</td>
              <td class="text-muted">{{ $u->email }}</td>
              <td class="text-muted">{{ $u->created_at->format('d/m/Y') }}</td>
              <td>
                <span class="badge {{ $u->is_admin?'bg-success':'bg-secondary' }}">
                  {{ $u->is_admin ? 'Yes' : 'No' }}
                </span>
              </td>
              <td class="text-end">
                <div class="btn-group btn-group-sm">
                  @if($u->id !== auth()->id())
                    <button class="btn btn-outline-light" wire:click="toggleAdmin({{ $u->id }})">
                      {{ $u->is_admin ? 'Remove admin' : 'Make admin' }}
                    </button>
                    <button class="btn btn-outline-danger" wire:click="deleteUser({{ $u->id }})" onclick="return confirm('Delete this user?')">
                      Delete
                    </button>
                  @else
                    <button class="btn btn-outline-secondary" disabled>It’s you</button>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-center text-muted py-4">No users found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">
      {{ $rows->links() }}
    </div>
  </div>
</div>
