<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script src="//unpkg.com/alpinejs" defer></script>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="apple-touch-icon" sizes="180x180" href="{{asset('favicon/apple-touch-icon.png')}}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{asset('favicon/favicon-32x32.png')}}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{asset('favicon/favicon-16x16.png')}}">
        <link rel="manifest" href="{{asset('favicon/site.webmanifest')}}">
        <link rel="mask-icon" href="{{asset('favicon/safari-pinned-tab.svg')}}" color="#5bbad5">
        <meta name="msapplication-TileColor" content="#2d89ef">
        <meta name="theme-color" content="#ffffff">
        <link rel="stylesheet" href="{{ asset('css/styles.css') }}">

        <title>CORIM</title>
    </head>
    <body class="bg-light">
    <div id="loadingSpinner" class="loading-spinner">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>

        <nav class="navbar navbar-expand-sm navbar-light bg-white" >

            <div class="container">
                <div class="navbar-brand">
                    <a href="/"><img src="{{asset('images/logo.svg')}}" height="30">
                    </a></div>
                    <div class="navbar-spacing"></div>
                    <form class="form-inline mobile-search-form" action="/" method="get">
                        <div class="input-group">
                            <input class="form-control border-0 bg-light" type="search" placeholder="Search listings" aria-label="Search" name="search"/>
                            <div class="input-group-append">
                                <button class="btn btn-light" style="color: #007BFF" type="submit"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </form>


            </div>

        </nav>

        <div class="sidebar bg-dark border-0">
            <a href="/" class="logo">
                <img src="{{asset('images/logo.svg')}}" alt="CORIM Logo" height="30">
            </a>

            <div class="list-group list-group-flush">
                <a href="/listings/manage" class="list-group-item bg-dark list-group-item-action {{ request()->is('listings/manage*') ? 'active' : '' }}">
                    <i class="fa fa-list"></i><span>Management</span>
                </a>
                <a href="/users/profile" class="list-group-item bg-dark list-group-item-action {{ request()->is('users/profile*') ? 'active' : '' }}">
                    <i class="fa fa-user"></i><span>Profile</span>
                </a>
                <a href="/listings/create" class="list-group-item bg-dark list-group-item-action {{ request()->is('listings/create*') ? 'active' : '' }}">
                    <i class="fa fa-plus-circle"></i><span>Create Listing</span>
                </a>
                <a href="" class="list-group-item bg-dark list-group-item-action">
                    <i class="fa fa-bell"></i><span>Notifications</span>
                </a>
                @auth
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="list-group-item bg-dark list-group-item-action">
                        <i class="fa fa-sign-out"></i><span>Log out</span>
                    </button>
                </form>
                @else
                <a href="/login" class="list-group-item bg-dark list-group-item-action {{ request()->is('login*','register*') ? 'active' : '' }}">
                    <i class="fa fa-sign-in"></i><span>Sign in/Sign up</span>
                </a>
                @endauth
            </div>

        </div>

        <div class="fixed-bottom">
          <nav class=" navbar-light bg-white">
            <div class="container">
              <ul class="navbar-nav d-flex justify-content-between w-100">
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
                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit" class="nav-link" style="border: none; background: none;">
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
          </nav>
        </div>
<div class="container mt-4">
{{$slot}}
</div>
<footer class="mt-4" style="margin-bottom: 100px;">
    <div class="container text-center">
        <p>&copy; <?php echo date('Y'); ?> CORIM - Collaborative Research Initiative in Medicine</p>
    </div>
</footer>
<x-flash-message />

<script>
    $(function () {
  $('[data-toggle="popover"]').popover()
})
</script>

<script>
    // Hide the loading spinner when the page is fully loaded
    window.addEventListener('load', function () {
        var loadingSpinner = document.getElementById('loadingSpinner');
        if (loadingSpinner) {
            loadingSpinner.style.display = 'none';
        }
    });
</script>

</body>
</html>
