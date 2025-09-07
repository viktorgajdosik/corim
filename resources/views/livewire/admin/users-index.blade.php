<div id="admin-users-index">
  @if (session('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
  @endif

  <div class="card bg-dark mb-3">
    <div class="card-body">
      <div class="row g-2 align-items-end">
        <div class="col-md-6">
          <label class="form-label">Search</label>
          <input type="text" class="form-control" placeholder="Name or email…" wire:model.live.debounce.300ms="search">
        </div>
        <div class="col-md-3">
          <label class="form-label">Role filter</label>
          <select class="form-select" wire:model.live="adminOnly">
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
            <th>Name</th><th>Email</th><th>Joined</th><th>Admin</th><th>Status</th><th></th>
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
              <td>
                @if($u->deactivated_at)
                  <span class="badge bg-warning text-dark">Deactivated</span>
                @elseif($u->banned_at)
                  <span class="badge bg-danger">Banned</span>
                @else
                  <span class="badge bg-success">Active</span>
                @endif

                @if(is_null($u->email_verified_at))
                  <span class="badge bg-secondary ms-1">Unverified</span>
                @endif
              </td>
              <td class="text-end">
                <div class="btn-group btn-group-sm">
                  @if($u->id !== auth()->id())
                    <button class="btn btn-outline-light" wire:click="toggleAdmin({{ $u->id }})">
                      {{ $u->is_admin ? 'Remove admin' : 'Make admin' }}
                    </button>

                    <div class="btn-group">
                      <button class="btn btn-outline-light dropdown-toggle" data-bs-toggle="dropdown">
                        More
                      </button>
                      <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                        <li>
                          <button class="dropdown-item" wire:click="sendResetLink({{ $u->id }})">
                            Send password reset email
                          </button>
                        </li>
                        @if(is_null($u->email_verified_at))
                          <li>
                            <button class="dropdown-item" wire:click="resendVerification({{ $u->id }})">
                              Resend verification email
                            </button>
                          </li>
                        @endif

                        <li><hr class="dropdown-divider"></li>

                        {{-- Ban with optional reason --}}
                        @if(!$u->banned_at)
                          <li class="px-3 py-2">
                            <label class="form-label small mb-1">Ban reason (optional)</label>
                            <input type="text"
                                   class="form-control form-control-sm bg-dark text-white mb-2"
                                   placeholder="Reason…"
                                   wire:model.defer="banReason.{{ $u->id }}">
                            <button class="btn btn-sm btn-outline-danger w-100"
                                    wire:click="banUser({{ $u->id }})">
                              Ban user
                            </button>
                          </li>
                        @else
                          <li>
                            <button class="dropdown-item" wire:click="unbanUser({{ $u->id }})">Unban user</button>
                          </li>
                        @endif

                        @if(!$u->deactivated_at)
                          <li><button class="dropdown-item" wire:click="deactivateUser({{ $u->id }})">Deactivate</button></li>
                        @else
                          <li><button class="dropdown-item" wire:click="activateUser({{ $u->id }})">Reactivate</button></li>
                        @endif

                        <li><button class="dropdown-item" wire:click="revokeSessions({{ $u->id }})">Revoke sessions</button></li>

                        <li><hr class="dropdown-divider"></li>

                        <li>
                          <button class="dropdown-item text-danger"
                                  wire:click="deleteUser({{ $u->id }})"
                                  onclick="return confirm('Delete this user?')">
                            Delete user
                          </button>
                        </li>
                      </ul>
                    </div>
                  @else
                    <button class="btn btn-outline-secondary" disabled>It’s you</button>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted py-4">No users found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="card-footer">
      {{ $rows->links() }}
    </div>
  </div>
</div>
