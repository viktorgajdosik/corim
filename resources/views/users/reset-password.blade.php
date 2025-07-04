<x-head>
    <div class="container d-flex align-items-center justify-content-center vh-100">
        <div class="card login-card p-4 bg-dark border-0">
            <x-primary-heading class="text-center text-white mb-1">Reset Password</x-primary-heading>
            <x-text class="text-center text-white">Enter your new password below.</x-text>

            <form method="POST" class="custom-floating-label" action="{{ route('password.update') }}" novalidate>
                @csrf
                <input type="hidden" name="token" value="{{ request()->route('token') }}">

                <!-- Email Input with Floating Label -->
                <div class="form-floating mb-3">
                    <input type="email" class="form-control bg-dark text-white @error('email') is-invalid @enderror" id="email" placeholder="Enter email" required name="email" value="{{ old('email') }}">
                    <label for="email">Email address</label>
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- New Password Input with Floating Label -->
                <div class="form-floating mb-3">
                    <input type="password" class="form-control bg-dark text-white @error('password') is-invalid @enderror" id="password" placeholder="New Password" required name="password">
                    <label for="password">New Password</label>
                    @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Confirm Password Input with Floating Label -->
                <div class="form-floating mb-3">
                    <input type="password" class="form-control bg-dark text-white" id="password_confirmation" placeholder="Confirm New Password" required name="password_confirmation">
                    <label for="password_confirmation">Confirm New Password</label>
                </div>

                <button type="submit" class="btn btn-primary w-100">Reset Password</button>
            </form>
        </div>
    </div>
</x-head>
