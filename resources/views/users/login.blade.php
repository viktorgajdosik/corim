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
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}">
        <link rel="mask-icon" href="{{ asset('favicon/safari-pinned-tab.svg') }}" color="#5bbad5">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="theme-color" content="#ffffff">
        <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
        <script src="{{asset('js/script.js') }}"></script>

        <title>CORIM</title>
    </head>
    <body>
        <div class="container">
    <div class="row justify-content-center">

            <div class="card login-card">
                <div class="card-header border-bottom-0" style="background-color: transparent; text-align: center;">
                    <div style="background-color: aliceblue; width: 80px; height: 80px; border-radius: 50%; margin: 0 auto; ">
                        <i class="fa fa-user fa-3x" style="color: #007BFF; padding: 16px;"></i>
                    </div>
                    <h3 style="color: #007BFF;">Sign in</h3>
                </div>
                <div class="card-body" >
                    <form method="POST" action="/users/authenticate">
                        @csrf
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend" style="width: 40px;">
                                    <span class="input-group-text bg-light border-0" style="width: 80px; justify-content: center; border-radius:35px 0px 0px 35px;"><i class="fa fa-envelope"></i></span>
                                </div>
                                <input type="email" class="form-control bg-light border-0" id="email" name="email" placeholder="Enter email" required value="{{ old('email') }}">
                            </div>
                            @error('email')
                            <p class="text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend" style="width: 40px;">
                                    <span class="input-group-text bg-light border-0" style="width: 80px; justify-content: center; border-radius:35px 0px 0px 35px;;"><i class="fa fa-lock"></i></span>
                                </div>
                                <input type="password" class="form-control bg-light border-0" id="password" name="password" placeholder="Password" required>
                                <div class="input-group-append">
                                    <span class="input-group-text bg-light border-0" style="cursor: pointer; background-color: white; border-radius:0px 35px 35px 0px;;" onclick="togglePasswordVisibility()">
                                        <i class="fa fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                            @error('password')
                            <p class="text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary btn-block" id="loginButton" disabled> Sign in</button>
                        </div>
                        <div class="mt-2">
                            <a href="" class="ml-auto">Forgot Password</a>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center border-top-1" style="background-color: transparent;" >
                    Don't have an account? <a href="/register">Sign up</a>
                </div>
            </div>
        </div>
        </div>


        <x-flash-message />


    </body>
    </html>
