<x-head>
    <div x-data="{ showPassword: false, showConfirmPassword: false, hasError: {{ $errors->has('password') ? 'true' : 'false' }} }">
        <div class="container-fluid vh-100">
            <div class="row h-100">
                <!-- Left Column (Logo and Info) -->
                <div class="col-xl-5 d-none d-xl-flex flex-column justify-content-center bg-primary text-white" style="padding-left: 3rem; position: relative;">
                    <a href="/">
                        <img src="{{ asset('images/logo_w.svg') }}" alt="Logo" style="position: absolute; top: 2rem; width: 75px; height: auto;">
                    </a>
                    <div class="text-wrapper" style="max-width: 450px; text-align: left;">
                        <h2 class="fw-bold">Create your account</h2>
                        <p style="font-size: 1.1rem;">
                            to join a dynamic community focused on advancing medical research. Connect with researchers, showcase your projects, and contribute to meaningful progress in healthcare.
                        </p>
                    </div>
                </div>

                <!-- Right Column (Registration Form) -->
                <div class="col-xl-7 d-flex align-items-center justify-content-center">
                    <div class="card login-card" style="background-color: transparent; border: none;">
                        <div class="card-header border-0 text-center" style="background-color: transparent;">
                            <h2 class="fw-bold" style="color: #000000;">Sign Up</h2>
                            <p class="login-subtitle">Create your account.</p>
                        </div>
                        <div class="card-body">
                            <form action="/users" method="POST" novalidate>
                                @csrf
                                <!-- Name Input with Floating Label -->
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name" name="name" placeholder="Forname, Surname, Titles" value="{{ old('name') }}" required>
                                    <label for="name">Forname, Surname, Titles</label>
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Email Input with Floating Label -->
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email" name="email" placeholder="Organisation email address" value="{{ old('email') }}" required>
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
                                            class="form-control form-control-lg @error('password') is-invalid @enderror"
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
                                            class="form-control form-control-lg"
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
                                <div class="mt-2 text-center">
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
