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
            <th>Name</th><th>Email</th><th>Joined</th><th>Admin</th><th>Status</th><th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($rows as $u)
            <tr>
              <td>
                {{-- Click name to open the right drawer (no separate button) --}}
                <a href="#"
                   class="link-light text-decoration-none"
                   data-bs-toggle="offcanvas"
                   data-bs-target="#userDetailCanvas"
                   wire:click.prevent="showUser({{ $u->id }})">
                  {{ $u->name }}
                </a>
              </td>
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

                        {{-- Ban with optional reason (inline, like you have) --}}
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

  {{-- ================== User details offcanvas (all sections at once) ================== --}}
  <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="userDetailCanvas" aria-labelledby="userDetailCanvasLabel" wire:ignore.self>
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="userDetailCanvasLabel">
        @if($showUserId && $userDetails['user'])
          {{ $userDetails['user']['name'] }} <span class="text-muted small">(#{{ $userDetails['user']['id'] }})</span>
        @else
          User details
        @endif
      </h5>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      @if($showUserId && $userDetails['user'])
        <div class="mb-3 small text-muted">
          <div>Email: <span class="text-white">{{ $userDetails['user']['email'] }}</span></div>
          <div>Org/Dept: <span class="text-white">{{ $userDetails['user']['organization'] ?? '—' }}</span> /
            <span class="text-white">{{ $userDetails['user']['department'] ?? '—' }}</span></div>
          <div>Joined: <span class="text-white">{{ $userDetails['user']['created_at'] }}</span></div>
          @if($userDetails['user']['banned_at'])
            <div class="text-danger">Banned: {{ $userDetails['user']['banned_at'] }}</div>
          @endif
          @if($userDetails['user']['deactivated_at'])
            <div class="text-warning">Deactivated: {{ $userDetails['user']['deactivated_at'] }}</div>
          @endif
        </div>

        <h6 class="mb-2">Listings (latest 20)</h6>
        @forelse($userDetails['listings'] as $l)
          <div class="d-flex justify-content-between small py-1 border-bottom border-secondary-subtle">
            <div>
              <a href="{{ route('listings.show', $l['id']) }}" class="link-light text-decoration-none">{{ $l['title'] }}</a>
              <span class="ms-2 badge {{ $l['is_open'] ? 'bg-success' : 'bg-secondary' }}">{{ $l['is_open']?'Open':'Closed' }}</span>
            </div>
            <div class="text-muted">{{ $l['created_at'] }}</div>
          </div>
        @empty
          <div class="text-muted small mb-3">No listings.</div>
        @endforelse

        <h6 class="mt-3 mb-2">Applications (latest 20)</h6>
        @forelse($userDetails['applications'] as $a)
          <div class="d-flex justify-content-between small py-1 border-bottom border-secondary-subtle">
            <div>
              @if($a['listing_id'])
                <a href="{{ route('listings.show', $a['listing_id']) }}" class="link-light text-decoration-none">{{ $a['listing_title'] }}</a>
              @else
                —
              @endif
              <span class="ms-2 badge {{ $a['accepted'] ? 'bg-success' : 'bg-secondary' }}">{{ $a['accepted']?'Accepted':'Awaiting' }}</span>
            </div>
            <div class="text-muted">{{ $a['created_at'] }}</div>
          </div>
        @empty
          <div class="text-muted small mb-3">No applications.</div>
        @endforelse

        <h6 class="mt-3 mb-2">Tasks (assigned; latest 20)</h6>
        @forelse($userDetails['tasks'] as $t)
          <div class="d-flex justify-content-between small py-1 border-bottom border-secondary-subtle">
            <div>
              <span class="text-white">{{ $t['name'] }}</span>
              <span class="ms-2 badge bg-info text-dark">{{ $t['status'] }}</span>
              @if($t['listing_id'])
                <span class="ms-2 text-muted">in</span>
                <a href="{{ route('listings.show', $t['listing_id']) }}" class="link-light text-decoration-none">{{ $t['listing_title'] }}</a>
              @endif
            </div>
            <div class="text-muted">{{ $t['created_at'] }}</div>
          </div>
        @empty
          <div class="text-muted small">No tasks.</div>
        @endforelse
      @else
        <div class="text-muted small">Select a user to view details.</div>
      @endif
    </div>
  </div>

  {{-- Tiny JS hook to open the canvas when Livewire says so (also works if you click the name link) --}}
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const detailEl = document.getElementById('userDetailCanvas');
      window.addEventListener('show-user-canvas', () => {
        const c = new bootstrap.Offcanvas(detailEl);
        c.show();
      });
    });
  </script>
</div>
