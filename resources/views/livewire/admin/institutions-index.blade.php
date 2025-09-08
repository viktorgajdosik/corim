<div id="admin-institutions">
  @if (session('message'))
    <div class="alert alert-success">{{ session('message') }}</div>
  @endif

  <div class="card bg-dark mb-3">
    <div class="card-body">
      <div class="row g-2 align-items-end">
        <div class="col-md-6">
          <label class="form-label">Search</label>
          <input type="text" class="form-control" placeholder="Name, domain, email…"
                 wire:model.live.debounce.300ms="search">
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-lg-6">
      <div class="card bg-dark h-100">
        <div class="card-header">Pending requests</div>
        <div class="table-responsive">
          <table class="table table-dark table-hover align-middle mb-0">
            <thead><tr><th>Name</th><th>Domain</th><th>Contact</th><th></th></tr></thead>
            <tbody>
              @forelse($pending as $r)
                <tr>
                  <td>
                    <div class="fw-semibold">{{ $r->name }}</div>
                    @if($r->website_url)
                      <small class="text-muted">{{ $r->website_url }}</small>
                    @endif
                    @if($r->message)
                      <div class="small text-secondary mt-1">{{ $r->message }}</div>
                    @endif
                  </td>
                  <td class="text-muted">{{ '@'.$r->org_domain }}</td>
                  <td class="text-muted">{{ $r->contact_email }}</td>
                  <td class="text-end">
                    <div class="btn-group btn-group-sm">
                      <button class="btn btn-outline-success" wire:click="approve({{ $r->id }})">Approve</button>
                      <button class="btn btn-outline-secondary" wire:click="decline({{ $r->id }})">Decline</button>
                    </div>
                  </td>
                </tr>
              @empty
                <tr><td colspan="4" class="text-center text-muted py-4">No pending requests.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="card-footer">{{ $pending->links() }}</div>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="card bg-dark h-100">
        <div class="card-header">Approved institutions</div>
        <div class="table-responsive">
          <table class="table table-dark table-hover align-middle mb-0">
            <thead><tr><th>Name</th><th>Domain</th><th>Website</th><th></th></tr></thead>
            <tbody>
              @forelse($institutions as $i)
                <tr>
                  <td>{{ $i->name }}</td>
                  <td class="text-muted">{{ '@'.$i->domain }}</td>
                  <td class="text-muted">
                    @if($i->website_url)
                      <a href="{{ $i->website_url }}" class="link-light" target="_blank" rel="noopener">Open</a>
                    @else — @endif
                  </td>
                  <td class="text-end">
                    <button class="btn btn-outline-danger btn-sm" wire:click="removeInstitution({{ $i->id }})"
                            onclick="return confirm('Remove this institution? Registered users remain untouched.')">
                      Remove
                    </button>
                  </td>
                </tr>
              @empty
                <tr><td colspan="4" class="text-center text-muted py-4">No institutions yet.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="card-footer">{{ $institutions->links() }}</div>
      </div>
    </div>
  </div>
</div>
