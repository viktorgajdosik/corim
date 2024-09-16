<x-head>
    <div class="container">
        <div class="row justify-content-center">
            <div class="card login-card">
                <div class="card-header border-bottom-0" style="background-color: transparent; text-align: center;">
                    <div class="mb-2 mt-2" style="background-color: #007BFF; width: 50px; height: 50px; border-radius: 50%; margin: 0 auto;">
                        <i class="fa fa-user fa-2x" style="color: white; padding: 8px;"></i>
                    </div>
                    <h3 style="color: black;">Welcome</h3>
                    <p class="login-subtitle">Sign in to access your account.</p>
                </div>
                <div class="card-body">
                    <form method="POST" action="/users/authenticate">
                        @csrf
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-prepend" style="width: 40px;">
                                    <span class="input-group-text bg-light border-0" style="width: 80px; justify-content: center; border-radius:7px 0px 0px 7px;">
                                        <i class="fa fa-envelope"></i>
                                    </span>
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
                                    <span class="input-group-text bg-light border-0" style="width: 80px; justify-content: center; border-radius:7px 0px 0px 7px;">
                                        <i class="fa fa-lock"></i>
                                    </span>
                                </div>
                                <input type="password" class="form-control bg-light border-0" id="password" name="password" placeholder="Password" required>
                                <div class="input-group-append">
                                    <span class="input-group-text bg-light border-0" style="cursor: pointer; background-color: white; border-radius:0px 7px 7px 0px;" onclick="togglePasswordVisibility()">
                                        <i class="fa fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                            @error('password')
                            <p class="text-danger mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary btn-block" id="loginButton" disabled>Sign in</button>
                        </div>
                        <div class="mt-2">
                            <a href="" class="ml-auto">Forgot Password</a>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center border-top-1" style="background-color: transparent;">
                    Don't have an account? <a href="/register">Sign up</a>
                </div>
            </div>
        </div>
    </div>
</x-head>
