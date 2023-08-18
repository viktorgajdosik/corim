<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <script src="//unpkg.com/alpinejs" defer></script>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="{{ asset('css/styles.css') }}">

        <title>CORIM</title>
    </head>
    <body>
        <nav class="navbar navbar-expand-sm navbar-light bg-white" >
            <div class="container">
                <div class="navbar-brand">
                    <a href="/"><img src="{{asset('images/logo.svg')}}" height="30">
                    </a></div>
                    <div class="navbar-spacing"></div>
                    <form class="form-inline mobile-search-form" action="/" method="get">
                        <div class="input-group">
                            <input class="form-control" type="search" placeholder="Search" aria-label="Search" name="search"/>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="submit"><i class="fa fa-search"></i></button>
                            </div>
                        </div>
                    </form>
            </div>
        </nav>

        <div class="sidebar">
            <a href="/" class="logo">
                <img src="{{asset('images/logo.svg')}}" alt="CORIM Logo" height="30">
            </a>

            <div class="list-group list-group-flush">
                <a href="/listings/manage" class="list-group-item list-group-item-action">
                    <i class="fa fa-list"></i><span>Management</span>
                </a>
                <a href="/users/profile" class="list-group-item list-group-item-action">
                    <i class="fa fa-user"></i><span>Profile</span>
                </a>
                <a href="/listings/create" class="list-group-item list-group-item-action">
                    <i class="fa fa-plus-circle"></i><span>Create Listing</span>
                </a>
                <a href="" class="list-group-item list-group-item-action">
                    <i class="fa fa-bell"></i><span>Notifications</span>
                </a>
                @auth
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="list-group-item list-group-item-action">
                        <i class="fa fa-sign-out"></i><span>Log out</span>
                    </button>
                </form>
                @else
                <a href="/login" class="list-group-item list-group-item-action">
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
                <a class="nav-link" href="/listings/manage"><i class="fa fa-list"></i></a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="/users/profile"><i class="fa fa-user"></i></a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="/listings/create"><i class="fa fa-plus-circle"></i></a>
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
                        <a class="nav-link" href="/login"><i class="fa fa-sign-in"></i></a>
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
</body>
</html>
