<x-head>
    <div class="container-fluid vh-100">
        <div class="row h-100">
            <!-- Left Column (Logo and Info) -->
            <div class="col-xl-5 d-none d-xl-flex flex-column justify-content-center bg-primary text-white" style="padding-left: 3rem; position: relative;">
                <a href="/">
                    <img src="{{ asset('images/logo_w.svg') }}" alt="Logo" style="position: absolute; top: 2rem; width: 75px; height: auto;">
                </a>
                <div class="text-wrapper" style="max-width: 450px; text-align: left;">
                    <h2 class="fw-bold">Log in</h2>
                    <p style="font-size: 1.1rem;">
                        To access your personalized dashboard and stay up-to-date with important updates. Your account connects you to a community dedicated to advancing medical research and innovation.
                    </p>
                </div>
            </div>

            <!-- Right Column (Login Form) -->
            <div class="col-xl-7 d-flex align-items-center justify-content-center">
                <div class="card login-card" style="background-color: transparent; border: none;" x-data="{ showPassword: false }">
                    <div class="card-header border-0 text-center" style="background-color: transparent;">
                        <h2 class="fw-bold" style="color: #000000;">Welcome</h2>
                        <p class="login-subtitle">Sign in to access your account.</p>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="/users/authenticate" novalidate>
                            @csrf
                          <!-- Email Input with Floating Label -->
<div class="form-floating mb-3">
    <input type="email" class="form-control form-control-md @error('email') is-invalid @enderror" id="email" placeholder="Enter email" required name="email" value="{{ old('email') }}"> <!-- Adjusted padding to maintain input size -->
    <label for="email">Email address</label>
    @error('email')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<!-- Password Input with Floating Label and Eye Addon -->
<div class="mb-3 position-relative" x-data="{ showPassword: false, hasError: {{ $errors->has('password') ? 'true' : 'false' }} }">
    <div class="form-floating">
        <input
            :type="showPassword ? 'text' : 'password'"
            class="form-control form-control-md @error('password') is-invalid @enderror"
            id="password"
            placeholder="Password"
            required
            name="password"
            value="{{ old('password') }}"
            style="padding-inline-end: 2.5rem"
            @input="hasError = false">
        <label for="password">Password</label>

        <!-- Eye button positioned inside the input -->
        <button
            type="button"
            class="btn bg-white text-secondary position-absolute"
            @click="showPassword = !showPassword"
            style="right: 1px; top: 50%; transform: translateY(-50%); border: none;"
            x-show="!hasError">
            <i :class="showPassword ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
        </button>
    </div>

    @error('password')
    <div class="invalid-feedback d-block" x-init="hasError = true">{{ $message }}</div>
    @enderror
</div>

                            <!-- Sign In Button -->
                            <div class="mb-3">
                                <button type="submit" class="btn btn-sign btn-lg w-100">Sign in</button>
                            </div>
                            <div class="mt-2 text-center">
                                <a href="#">Forgot Password?</a>
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
