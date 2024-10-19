<x-head>
    <div x-data="{ showPassword: false, showConfirmPassword: false, hasError: {{ $errors->has('password') ? 'true' : 'false' }} }">
        <div class="container-fluid vh-100">
            <div class="row h-100">
                <!-- Left Column (Logo and Info) -->
                <div class="col-xl-6 d-none d-xl-flex flex-column justify-content-center text-body" style="background-color:#000; padding-left: 4rem; position: relative;">
                    <a href="/">
                        <img src="{{ asset('images/logo_w.svg') }}" alt="Logo" style="position: absolute; top: 2rem; width: 75px; height: auto;">
                    </a>
                    <div class="text-wrapper" style="max-width: 700px; text-align: left;">
                        <p class="text-uppercase text-white" style="font-weight: 900; font-size: 4rem;">
                         Connect with peers to contribute
                        </p>
                    </div>
                </div>

                <!-- Right Column (Registration Form) -->
                <div class="container p-0 col-xl-6 d-flex align-items-center justify-content-center">
                     <!-- Logo mobile -->
                <div class="sign-logo ">
                    <a href="/">
                        <img src="{{ asset('images/logo_w.svg') }}" alt="Logo">
                    </a>
                </div>
                    <div class="card login-card" style="background-color: transparent; border: none;">
                        <div class="card-header border-0 text-center" style="background-color: transparent;">
                            <h2 class="fw-bold" style="color: #fff;">Sign Up</h2>
                            <p class="login-subtitle text-white">Create your account.</p>
                        </div>
                        <div class="card-body">
                            <form action="/users" method="POST" novalidate>
                                @csrf
                                <!-- Name Input with Floating Label -->
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control form-control-md @error('name') is-invalid @enderror" id="name" name="name" placeholder="Forname, Surname, Titles" value="{{ old('name') }}" required>
                                    <label for="name">Forname, Surname, Titles</label>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Email Input with Floating Label -->
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control form-control-md @error('email') is-invalid @enderror" id="email" name="email" placeholder="Organisation email address" value="{{ old('email') }}" required>
                                    <label for="email">Organisation email address</label>
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Password Input with Eye Addon -->
                                <div class="mb-3 position-relative" x-data="{ showPassword: false, hasError: {{ $errors->has('password') ? 'true' : 'false' }} }">
                                    <div class="form-floating">
                                        <input
                                            :type="showPassword ? 'text' : 'password'"
                                            class="form-control form-control-md @error('password') is-invalid @enderror"
                                            id="password"
                                            name="password"
                                            placeholder="Password"
                                            required
                                            value="{{ old('password') }}"
                                            style="padding-inline-end: 2.5rem"
                                            @input="hasError = false">
                                        <label for="password">Password</label>

                                        <!-- Eye button positioned inside the input -->
                                        <button
                                            class="btn bg-white text-secondary position-absolute"
                                            type="button"
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

                                <!-- Confirm Password Input with Eye Addon -->
                                <div class="mb-3 position-relative" x-data="{ showConfirmPassword: false }">
                                    <div class="form-floating">
                                        <input
                                            :type="showConfirmPassword ? 'text' : 'password'"
                                            class="form-control form-control-md"
                                            id="password_confirmation"
                                            name="password_confirmation"
                                            placeholder="Confirm Password"
                                            required
                                            style="padding-inline-end: 2.5rem">
                                        <label for="password_confirmation">Confirm Password</label>

                                        <!-- Eye button positioned inside the input -->
                                        <button
                                            class="btn bg-white text-secondary position-absolute"
                                            type="button"
                                            @click="showConfirmPassword = !showConfirmPassword"
                                            style="right: 1px; top: 50%; transform: translateY(-50%); border: none;">
                                            <i :class="showConfirmPassword ? 'fa fa-eye-slash' : 'fa fa-eye'"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Sign Up Button -->
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-sign btn-lg w-100">Sign up</button>
                                </div>
                                <div class="mt-2 text-center text-white">
                                    Already have an account? <a href="/login">Sign in</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-head>
