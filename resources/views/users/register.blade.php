<x-head>
    <div x-data="{ showPassword: false, showConfirmPassword: false, hasError: {{ $errors->has('password') ? 'true' : 'false' }} }">
        <div class="container-fluid vh-100">
            <div class="row h-100">
                <!-- Left Column (Logo and Info) -->
                <div class="col-xl-6 d-none d-xl-flex flex-column justify-content-center text-body" style="background-color:#151515; padding-left: 4rem; position: relative;">
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
                <div class="container p-3 col-xl-6 d-flex align-items-center justify-content-center">
                    <!-- Logo mobile -->
                    <div class="sign-logo">
                        <a href="/">
                            <img src="{{ asset('images/logo_w.svg') }}" alt="Logo">
                        </a>
                    </div>

                    <div class="card login-card bg-dark">
                        <div class="card-header border-0 text-center" style="background-color: transparent;">
                            <h2 class="fw-bold" style="color: #fff;">Sign Up</h2>
                            <p class="login-subtitle text-white mb-1">Create your account.</p>
                        </div>

                        <div class="card-body">
                            <livewire:register-user />

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-head>
