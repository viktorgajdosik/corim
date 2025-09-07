<x-head>

    <!-- Fixed Bottom Navigation -->
    <div class="fixed-bottom">
        <div class="container m-0 p-0 mw-100">
            <ul class="nav justify-content-between navbar-bottom-ul w-100">
    <li class="nav-item">
        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="/">
            <i class="fa fa-home"></i>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->is('listings/manage*', 'listings/show-manage*') ? 'active' : '' }}" href="/listings/manage">
            <i class="fa fa-list"></i>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->is('users/profile*') ? 'active' : '' }}" href="/users/profile">
            <i class="fa fa-user"></i>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->is('listings/create') ? 'active' : '' }}" href="/listings/create">
            <i class="fa fa-plus-circle"></i>
        </a>
    </li>

    {{-- Notifications (component should render its own <li>) --}}
    @livewire('notifications-bell')

    {{-- Admin link: only for authenticated admins --}}
    @auth
        @if(auth()->user()->is_admin ?? false)
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}"
                   href="{{ route('admin.dashboard') }}" title="Admin">
                    <i class="fa fa-shield"></i>
                </a>
            </li>
        @endif
    @endauth

    @auth
        <li class="nav-item">
            <form method="POST" class="m-0" action="/logout">
                @csrf
                <button type="submit" class="nav-link" style="border: none; background: none;">
                    <i class="fa fa-sign-out"></i>
                </button>
            </form>
        </li>
    @else
        <li class="nav-item">
            <a class="nav-link {{ request()->is('login*','register*') ? 'active' : '' }}" href="/login">
                <i class="fa fa-sign-in"></i>
            </a>
        </li>
    @endauth
</ul>

        </div>
    </div>

    <!-- Page Content -->
    <div class="container container-custom-main mt-3">
        @yield('content')
    </div>

<!-- Footer -->
<footer class="site-footer mt-4" style="margin-bottom: 100px;">
  <div class="container py-5">
    <div class="row gy-5">
      <!-- Brand / About -->
      <div class="col-12 col-lg-5">
        <h5 class="mb-2 fw-semibold">CORIM</h5>
        <p class="mb-3 text-muted-70 small">
          Collaborative Research Initiative in Medicine — connecting students and authors to drive clinical and basic science projects.
        </p>

        <!-- Subtle contact button under the brand -->
<a href="{{ url('/contact') }}"
   class="footer-cta footer-cta--ghost rounded-pill d-inline-flex align-items-center gap-2">
  <i class="fa fa-envelope"></i>
  <span>Contact us</span>
</a>
      </div>

      <!-- Quick Links -->
      <div class="col-6 col-lg-3">
        <h6 class="mb-3 text-muted-70 small text-uppercase">Quick Links</h6>
        <ul class="list-unstyled d-grid gap-2 small">
          <li><a href="{{ url('/') }}" class="footer-link">Home</a></li>
          <li><a href="{{ route('listings.manage') }}" class="footer-link">Manage Listings</a></li>
          <li><a href="{{ route('listings.create') }}" class="footer-link">Create Listing</a></li>
          <li><a href="{{ route('users.profile') }}" class="footer-link">Profile</a></li>
          <li><a href="{{ route('notifications.index') }}" class="footer-link">Notifications</a></li>
        </ul>
      </div>

      <!-- Resources / Policies -->
      <div class="col-6 col-lg-2">
        <h6 class="mb-3 text-muted-70 small text-uppercase">Resources</h6>
        <ul class="list-unstyled d-grid gap-2 small">
          <li><a href="#" class="footer-link" title="Planned uptime / incident page">Status</a></li>
          <li><a href="#" class="footer-link" title="Author & participant rules and best practices">Guidelines</a></li>
          <li><a href="#" class="footer-link">Privacy</a></li>
          <li><a href="#" class="footer-link">Terms</a></li>
          <li><a href="#" class="footer-link">Contact</a></li>
        </ul>
      </div>

      <!-- Institutions CTA -->
      <div class="col-12 col-lg-2 text-center text-lg-start">
        <h6 class="mb-2 text-muted-70 small text-uppercase">For Institutions</h6>
        <p class="text-muted-60 small mb-3">
          Partner with CORIM to onboard mentors and streamline student participation.
        </p>

        <!-- Smaller, centered, elegant CTA -->
<!-- Register institution (solid) -->
<a href="{{ url('/institutions/register') }}"
   class="footer-cta footer-cta--solid rounded-pill d-inline-flex align-items-center gap-2">
  <i class="fa fa-building-o"></i>
  <span>Register institution</span>
</a>
      </div>
    </div>

    <hr class="border-secondary opacity-25 my-4">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 small text-muted-60">
      <div>&copy; {{ date('Y') }} CORIM — Collaborative Research Initiative in Medicine</div>
      <div class="d-flex gap-3">
        <a href="#" class="footer-link" title="Planned uptime / incident page">Status</a>
        <a href="#" class="footer-link">Sitemap</a>
      </div>
    </div>
  </div>
</footer>




</x-head>
