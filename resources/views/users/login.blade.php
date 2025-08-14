<x-head>
    <div class="container-fluid vh-100">
        <div class="row h-100">
            <!-- Left Column (Logo and Info) -->
            <div class="col-xl-6 d-none d-xl-flex flex-column justify-content-center text-body" style="background-color:#151515; padding-left: 4rem; position: relative;">
                <a href="/">
                    <img src="{{ asset('images/logo_w.svg') }}" alt="Logo" style="position: absolute; top: 2rem; width: 75px; height: auto;">
                </a>
                <div class="text-wrapper" style="max-width: 700px; text-align: left;">
                    <p class="text-uppercase text-white" style="font-weight: 900; font-size: 4rem;">
                        Dedicated to advancing medical research
                    </p>
                </div>
            </div>

            <!-- Right Column (Login Form) -->
            <div class="container col-xl-6 d-flex align-items-center justify-content-center flex-column">

                <!-- Logo mobile -->
                <div class="sign-logo">
                    <a href="/">
                        <img src="{{ asset('images/logo_w.svg') }}" alt="Logo">
                    </a>
                </div>

                  <div class="card login-card p-3 border-0"
                     style="background: linear-gradient(90deg, #E96479, #8A80FF); background-size: cover; background-position: center;"
                     x-data="{ showPassword: false }">
                    <div class="card-header border-0 text-center" style="background-color: transparent;">
                        <h2 class="fw-bold text-white">Welcome</h2>
                        <p class="login-subtitle text-white">Sign in to access your account.</p>
                    </div>

                    <div class="card-body">
                        <livewire:login-form />

                    </div>

                    <!-- Sign Up Link -->
                    <div class="card-footer text-center text-white" style="background-color: transparent;">
                        Don't have an account? <a class="text-white" href="{{ route('register') }}">Sign up</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-head>
