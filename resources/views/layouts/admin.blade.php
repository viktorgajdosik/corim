<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">

  {{-- Livewire full-page title support + fallback to Blade section --}}
  <title>{{ $title ?? \Illuminate\Support\Facades\View::yieldContent('title','Admin') }}</title>

  @vite(['resources/sass/app.scss', 'resources/js/app.js'])
  @livewireStyles
  <style>
    .admin-shell{min-height:100vh;background:#0e0f11}
    .admin-sidebar{width:240px;background:#121316;border-right:1px solid rgba(255,255,255,.08)}
    .admin-brand{font-weight:700;letter-spacing:.5px}
    .nav-link.active{background:rgba(255,255,255,.08);border-radius:.5rem}
    .content-wrap{flex:1;min-width:0}
  </style>
</head>
<body class="admin-shell d-flex">
  <aside class="admin-sidebar d-flex flex-column p-3">
    <a class="navbar-brand admin-brand mb-3 text-white text-decoration-none" href="{{ route('admin.dashboard') }}">
      <i class="fa fa-shield-alt me-2"></i> Admin
    </a>

    <ul class="nav nav-pills flex-column gap-1 mb-auto">
      <li class="nav-item">
        <a class="nav-link text-white {{ request()->routeIs('admin.dashboard')?'active':'' }}" href="{{ route('admin.dashboard') }}">
          <i class="fa fa-chart-bar me-2"></i> Dashboard
        </a>
      </li>
      <li><a class="nav-link text-white {{ request()->routeIs('admin.users')?'active':'' }}" href="{{ route('admin.users') }}"><i class="fa fa-users me-2"></i> Users</a></li>
      <li><a class="nav-link text-white {{ request()->routeIs('admin.listings')?'active':'' }}" href="{{ route('admin.listings') }}"><i class="fa fa-list me-2"></i> Listings</a></li>
      <li><a class="nav-link text-white {{ request()->routeIs('admin.applications')?'active':'' }}" href="{{ route('admin.applications') }}"><i class="fa fa-id-badge me-2"></i> Applications</a></li>
      <li><a class="nav-link text-white {{ request()->routeIs('admin.settings')?'active':'' }}" href="{{ route('admin.settings') }}"><i class="fa fa-cog me-2"></i> Settings</a></li>
    </ul>

    <div class="mt-3 pt-3 border-top border-secondary-subtle small text-muted">
      <div class="mb-2"><i class="fa fa-user-shield me-1"></i>{{ auth()->user()->name }}</div>
      <div class="d-flex gap-2">
        <a href="{{ url('/') }}" class="btn btn-outline-light btn-sm">Back to site</a>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button class="btn btn-outline-danger btn-sm">Logout</button>
        </form>
      </div>
    </div>
  </aside>

  <main class="content-wrap d-flex flex-column">
    <nav class="navbar navbar-dark border-bottom border-secondary-subtle" style="background:#101114">
      <div class="container-fluid">
        {{-- Livewire page title + Blade fallback --}}
        <span class="navbar-text text-white">{{ $title ?? \Illuminate\Support\Facades\View::yieldContent('title','Admin') }}</span>
      </div>
    </nav>

    <div class="container-fluid p-4">
      {{-- Livewire full-page components render into $slot --}}
      {{ $slot ?? '' }}

      {{-- also keep classic Blade support just in case --}}
      @yield('content')
    </div>
  </main>

  @livewireScripts
</body>
</html>
