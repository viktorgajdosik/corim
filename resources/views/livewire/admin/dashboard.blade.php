<div id="admin-dashboard">
  @push('styles')
  <style>.stat-card .h2{font-weight:700}</style>
  @endpush

  <div class="row g-3 mb-3">
    <div class="col-6 col-md-3">
      <div class="card h-100 stat-card">
        <div class="card-body">
          <div class="text-muted mb-1">Users</div>
          <div class="h2 mb-0">{{ $users }}</div>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card h-100 stat-card">
        <div class="card-body">
          <div class="text-muted mb-1">Listings</div>
          <div class="h2 mb-0">{{ $listings }}</div>
          <small class="text-muted">Open: {{ $listingsOpen }}</small>
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="card h-100 stat-card">
        <div class="card-body">
          <div class="text-muted mb-1">Applications</div>
          <div class="h2 mb-0">{{ $applications }}</div>
          <small class="text-muted">Accepted: {{ $applicationsAccepted }}</small>
        </div>
      </div>
    </div>
  </div>

  {{-- Admin analytics with organization selector --}}
  @livewire('admin.org-analytics')
</div>
