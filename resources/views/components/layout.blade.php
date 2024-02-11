<x-head>
        <div class="sidebar">

            <div class="list-group list-group-flush">
                <a href="/listings/manage" class="list-group-item list-group-item-action {{ request()->is('listings/manage*') ? 'active' : '' }}">
                    <i class="fa fa-list sidebar-icon"></i><span>Management</span>
                </a>
                <a href="/users/profile" class="list-group-item list-group-item-action {{ request()->is('users/profile*') ? 'active' : '' }}">
                    <i class="fa fa-user sidebar-icon"></i><span>Profile</span>
                </a>
                <a href="/listings/create" class="list-group-item list-group-item-action {{ request()->is('listings/create*') ? 'active' : '' }}">
                    <i class="fa fa-plus-circle sidebar-icon"></i><span>Create Listing</span>
                </a>
                <a href="" class="list-group-item list-group-item-action">
                    <i class="fa fa-bell sidebar-icon"></i><span>Notifications</span>
                </a>
                @auth
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="list-group-item list-group-item-action">
                        <i class="fa fa-sign-out sidebar-icon"></i><span>Log out</span>
                    </button>
                </form>
                @else
                <a href="/login" class="list-group-item list-group-item-action {{ request()->is('login*','register*') ? 'active' : '' }}" style="margin-bottom: 10px">
                    <i class="fa fa-sign-in sidebar-icon"></i><span>Sign in/Sign up</span>
                </a>
                @endauth
            </div>

        </div>

        <div class="fixed-bottom">

              <ul class="navbar-nav navbar-bottom-ul d-flex justify-content-between w-100">
                <li class="nav-item">
                <a class="nav-link {{ request()->is('listings/manage*') ? 'active' : '' }}" href="/listings/manage"><i class="fa fa-list"></i></a>
                </li>
                <li class="nav-item">
                <a class="nav-link {{ request()->is('users/profile*') ? 'active' : '' }}" href="/users/profile"><i class="fa fa-user"></i></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->is('listings/create') ? 'active' : '' }}" href="/listings/create"><i class="fa fa-plus-circle"></i></a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href=""><i class="fa fa-bell"></i></a>
                </li>
                @auth
                <li class="nav-item">
                    <form method="POST" class="m-0" action="/logout">
                        @csrf
                        <button type="submit" class="nav-link nav-link-out" style="border: none; background: none;">
                            <i class="fa fa-sign-out"></i>
                        </button>
                    </form>
                   </li>
                    @else
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('login*','register*') ? 'active' : '' }}" href="/login"><i class="fa fa-sign-in"></i></a>
                        </li>
                    @endauth

              </ul>


        </div>

<div class="container container-custom-main">
{{$slot}}
<footer class="mt-4" style="margin-bottom: 100px;">
    <div class="container text-center">
        <p>&copy; <?php echo date('Y'); ?> CORIM - Collaborative Research Initiative in Medicine</p>
    </div>
</footer>
</div>

</x-head>

