<x-head>
    <div class="container-fluid vh-100">
        <div class="row h-100">
            <!-- Left Column - Full Blue with Centered Logo, Header, and Text -->
            <div class="col-xl-5 d-none d-xl-flex flex-column align-items-center justify-content-center bg-primary text-white">
                <div class="text-wrapper" style="max-width: 400px; text-align: left;">
                    <h2 class="font-weight-bold">Collaborative research</h2>
                    <p>
                        Join the effort to advance medical research through collaboration. Together, we can drive innovation and improve healthcare outcomes globally.
                    </p>
                </div>
                </p>
            </div>

            <!-- Right Column - Transparent Login Card -->
            <div class="col-xl-7 d-flex align-items-center justify-content-center">
                <div class="card login-card" style="background-color: transparent; border: none;">
                    <div class="card-header border-bottom-0 text-center" style="background-color: transparent;">
                        <h2 class="font-weight-bold" style="color: #000000;">Welcome</h2>
                        <p class="login-subtitle">Sign in to access your account.</p>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="/users/authenticate">
                            @csrf
                            <!-- Email Input -->
                            <div class="form-group">
                                <div class="input-group-lg">
                                    <input type="email" class="form-control custom-input" id="email" name="email" placeholder="Enter email" required value="{{ old('email') }}">
                                </div>
                                @error('email')
                                <p class="text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                           <!-- Password Input -->
<div class="form-group">
    <div class="input-group input-group-lg">
        <input type="password" class="form-control custom-input-1" id="password" name="password" placeholder="Password" required>
        <div class="input-group-append">
            <span class="input-group-text icon-box-1" style="cursor: pointer;" onclick="togglePasswordVisibility()">
                <i class="fa fa-eye icon"></i>
            </span>
        </div>
    </div>
    @error('password')
    <p class="text-danger mt-1">{{ $message }}</p>
    @enderror
</div>


                            <!-- Sign In Button -->
                            <div class="mt-3">
                                <button type="submit" class="btn-lg btn-primary btn-block" id="loginButton">Sign in</button>
                            </div>
                            <div class="mt-2 text-center">
                                <a href="#">Forgot Password</a>
                            </div>
                        </form>
                    </div>

                    <!-- Sign Up Link -->
                    <div class="card-footer text-center" style="background-color: transparent;">
                        Don't have an account? <a href="/register">Sign up</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-head>
