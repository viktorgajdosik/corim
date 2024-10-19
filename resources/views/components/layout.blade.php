<x-head>


        <!-- Search Bar -->
        <nav class="navbar z-3" style="position: fixed; top: 0; width: 100%;">
            <div class="container container-custom-top">
                <form class="form-inline w-100 p-0 m-0" action="/" method="get">
                    <div class="input-group">
                        <input class="form-control border-0 bg-white flex-grow-1" type="search" placeholder="Search listings" aria-label="Search" name="search" style="border-top-left-radius: 7rem; border-bottom-left-radius: 7rem;" />
                        <button class="btn btn-lg search-btn input-group-text" type="submit">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </nav>

<!-- Fixed Bottom Navigation -->
<div class="fixed-bottom">
    <div class="container m-0 p-0 mw-100">
        <ul class="nav justify-content-between navbar-bottom-ul w-100">
            <!-- Home Link -->
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
            <li class="nav-item">
                <a class="nav-link" href="">
                    <i class="fa fa-bell"></i>
                </a>
            </li>
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
        <div class="container container-custom-main">
            {{$slot}}
        </div>

        <footer class="mt-4" style="margin-bottom: 100px;">
            <div class="container text-center text-white">
                <p>&copy; <?php echo date('Y'); ?> CORIM - Collaborative Research Initiative in Medicine</p>
            </div>
        </footer>
</x-head>
