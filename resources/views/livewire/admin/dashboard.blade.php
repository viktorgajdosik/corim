<div id="admin-dashboard">
  @push('styles')
  <style>
    .stat-card .h2 { font-weight: 700; }
  </style>
  @endpush

  <h1 class="mb-3">Admin dashboard</h1>

  <div class="row g-3">
    <div class="col-md-3">
      <div class="card h-100 stat-card">
        <div class="card-body">
          <div class="text-muted mb-1">Users</div>
          <div class="h2 mb-0">{{ $users }}</div>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card h-100 stat-card">
        <div class="card-body">
          <div class="text-muted mb-1">Listings</div>
          <div class="h2 mb-0">{{ $listings }}</div>
          <small class="text-muted">Open: {{ $listingsOpen }}</small>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card h-100 stat-card">
        <div class="card-body">
          <div class="text-muted mb-1">Applications</div>
          <div class="h2 mb-0">{{ $applications }}</div>
          <small class="text-muted">Accepted: {{ $applicationsAccepted }}</small>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
  <script>/* dashboard-only JS if needed */</script>
  @endpush
</div>
