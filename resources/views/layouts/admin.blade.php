<!doctype html>
<html lang="en" data-bs-theme="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
  <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon.png') }}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16x16.png') }}">
  <link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}">
  <link rel="mask-icon" href="{{ asset('favicon/safari-pinned-tab.svg') }}" color="#5bbad5">
  <meta name="msapplication-TileColor" content="#da532c">
  <meta name="theme-color" content="#000">

  <title>{{ $title ?? \Illuminate\Support\Facades\View::yieldContent('title','Admin') }}</title>

  @vite(['resources/sass/app.scss', 'resources/js/app.js'])
  @livewireStyles

  <style>
    /* ===== Colors / BG ===== */
    :root{
      --bg-body:#0a0c0f;
      --bg-topbar:#0e1013;
      --bg-canvas:#0b0d10;
      --line:rgba(255,255,255,.08);
      --ink:#e6e9ee;
      --ink-dim:#c7cbd4;
      --ink-muted:#9ca3af;
      --hover:rgba(255,255,255,.06);
      --active:rgba(255,255,255,.10);
    }

    /* ===== Topbar ===== */
    .admin-navbar{background:var(--bg-topbar);border-bottom:1px solid var(--line)}
    .admin-brand{font-weight:700;letter-spacing:.3px;font-size:.92rem}

    /* ===== Offcanvas ===== */
    .offcanvas.admin-offcanvas{
      /* THIS controls the width in Bootstrap 5 */
      --bs-offcanvas-width: 220px;   /* <- tweak here if you want even smaller */
      background:var(--bg-canvas);
      border-right:1px solid var(--line);
    }
    .offcanvas.admin-offcanvas .offcanvas-header{
      border-bottom:1px solid var(--line);padding:.55rem .7rem
    }
    .offcanvas.admin-offcanvas .offcanvas-title{font-size:.9rem}

    .offcanvas.admin-offcanvas .offcanvas-body{
      display:flex;flex-direction:column;gap:.2rem;color:var(--ink);
      font-size:.82rem; line-height:1.25;  /* smaller text inside */
    }

    /* ===== Section toggles (square) ===== */
    .section-toggle{
      width:100%;display:flex;align-items:center;justify-content:space-between;
      padding:.35rem .5rem;background:transparent;border:0;color:var(--ink);
      font-weight:600;letter-spacing:.2px;border-radius:0;font-size:.8rem
    }
    .section-toggle:hover{background:var(--hover);color:#fff}
    .section-toggle .chev{transition:transform .2s ease;font-size:.78rem;opacity:.9}

    /* ===== Nav links: tighter + square ===== */
    .admin-nav{margin:.05rem 0}
    .admin-nav .nav-link{
      display:flex;align-items:center;gap:.45rem;
      padding:0;
      height: 3rem;
      border-radius:0;
      color:var(--ink);font-weight:500;opacity:.95;
      font-size:.82rem;
    }
    .admin-nav .nav-link i{
      width:15px;text-align:center;font-size:.85rem;opacity:.9
    }
    .admin-nav .nav-link.active{background:var(--active);color:#fff;opacity:1}
    .admin-nav .nav-link:hover{background:var(--hover);color:#fff}

    .admin-footer{border-top:1px solid var(--line);color:var(--ink-muted);padding-top:.6rem;margin-top:auto}

    /* ===== Page background ===== */
    body.admin-content{min-height:100vh;background:var(--bg-body)}
  </style>
</head>
<body class="admin-content d-flex flex-column">

  {{-- Top bar --}}
  <nav class="navbar navbar-dark admin-navbar">
    <div class="container-fluid">
      <div class="d-flex align-items-center gap-2">
        <button class="btn btn-outline-light btn-sm" type="button"
                data-bs-toggle="offcanvas" data-bs-target="#adminOffcanvas"
                aria-controls="adminOffcanvas" aria-label="Open menu">
          <i class="fa fa-bars"></i>
        </button>
        <a class="navbar-brand admin-brand d-none d-sm-inline" href="{{ route('admin.dashboard') }}">
          <i class="fa fa-shield"></i> Admin
        </a>
      </div>

      <span class="navbar-text text-white">{{ $title ?? \Illuminate\Support\Facades\View::yieldContent('title','Admin') }}</span>

      <div class="d-flex align-items-center gap-2">
        <a href="{{ url('/') }}" class="btn btn-outline-light btn-sm" title="Back to site">Site</a>
        <form method="POST" action="{{ route('logout') }}" class="m-0">
          @csrf
          <button class="btn btn-outline-danger btn-sm">Logout</button>
        </form>
      </div>
    </div>
  </nav>

  {{-- Offcanvas (collapsible sections inside) --}}
  <div class="offcanvas offcanvas-start admin-offcanvas" tabindex="-1" id="adminOffcanvas" aria-labelledby="adminOffcanvasLabel">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title admin-brand" id="adminOffcanvasLabel">
        <i class="fa fa-shield"></i> Admin
      </h5>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body">

      {{-- Section: Overview --}}
      <button class="section-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#sec-overview" aria-expanded="true" aria-controls="sec-overview">
        <span><i class="fa fa-chart-line me-2"></i> Overview</span>
        <i class="fa fa-chevron-down chev"></i>
      </button>
      <div class="collapse show" id="sec-overview">
        <nav class="admin-nav nav flex-column mb-2">
          <a class="nav-link {{ request()->routeIs('admin.dashboard')?'active':'' }}" href="{{ route('admin.dashboard') }}">
            <i class="fa fa-chart-bar"></i> <span>Dashboard</span>
          </a>
        </nav>
      </div>

      {{-- Section: Manage --}}
      <button class="section-toggle mt-1" type="button" data-bs-toggle="collapse" data-bs-target="#sec-manage" aria-expanded="true" aria-controls="sec-manage">
        <span><i class="fa fa-toolbox me-2"></i> Manage</span>
        <i class="fa fa-chevron-down chev"></i>
      </button>
      <div class="collapse show" id="sec-manage">
        <nav class="admin-nav nav flex-column mb-2">
          <a class="nav-link {{ request()->routeIs('admin.users')?'active':'' }}" href="{{ route('admin.users') }}">
            <i class="fa fa-users"></i> <span>Users</span>
          </a>
          <a class="nav-link {{ request()->routeIs('admin.listings')?'active':'' }}" href="{{ route('admin.listings') }}">
            <i class="fa fa-list"></i> <span>Listings</span>
          </a>
          <a class="nav-link {{ request()->routeIs('admin.applications')?'active':'' }}" href="{{ route('admin.applications') }}">
            <i class="fa fa-id-badge"></i> <span>Applications</span>
          </a>
          @if (\Illuminate\Support\Facades\Route::has('admin.broadcast'))
            <a class="nav-link {{ request()->routeIs('admin.broadcast')?'active':'' }}" href="{{ route('admin.broadcast') }}">
              <i class="fa fa-bullhorn"></i> <span>Broadcast</span>
            </a>
          @endif
        </nav>
      </div>

      {{-- Section: Site --}}
      <button class="section-toggle mt-1" type="button" data-bs-toggle="collapse" data-bs-target="#sec-site" aria-expanded="true" aria-controls="sec-site">
        <span><i class="fa fa-sitemap me-2"></i> Site</span>
        <i class="fa fa-chevron-down chev"></i>
      </button>
      <div class="collapse show" id="sec-site">
        <nav class="admin-nav nav flex-column mb-2">
          <a class="nav-link {{ request()->routeIs('admin.banners')?'active':'' }}" href="{{ route('admin.banners') }}">
            <i class="fa fa-image"></i> <span>Home carousel</span>
          </a>
          <a class="nav-link {{ request()->routeIs('admin.institutions')?'active':'' }}"
   href="{{ route('admin.institutions') }}">
  <i class="fa fa-building-o"></i> <span>Institutions</span>
</a>

          <a class="nav-link {{ request()->routeIs('admin.settings')?'active':'' }}" href="{{ route('admin.settings') }}">
            <i class="fa fa-cog"></i> <span>Settings</span>
          </a>
        </nav>
      </div>

      <div class="admin-footer small">
        <div class="mb-2"><i class="fa fa-user-shield me-1"></i>{{ auth()->user()->name }}</div>
      </div>
    </div>
  </div>

  {{-- Page content --}}
  <main class="container-fluid p-4 flex-grow-1">
    {{ $slot ?? '' }}
    @yield('content')
  </main>

  @livewireScripts

  <script>
    // rotate chevrons on collapse show/hide
    document.addEventListener('shown.bs.collapse', e => {
      const btn = document.querySelector('[data-bs-target="#' + e.target.id + '"] .chev');
      if (btn) btn.style.transform = 'rotate(180deg)';
    });
    document.addEventListener('hidden.bs.collapse', e => {
      const btn = document.querySelector('[data-bs-target="#' + e.target.id + '"] .chev');
      if (btn) btn.style.transform = 'rotate(0deg)';
    });
  </script>
</body>
</html>
