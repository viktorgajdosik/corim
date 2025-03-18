<x-head>
    <div class="container d-flex align-items-center justify-content-center vh-100">
        <div class="card login-card p-4 bg-dark border-0">
            <h2 class="text-center text-white mb-1">Forgot Password</h2>
            <p class="text-center text-white">Enter your email for a password reset link.</p>

            @if (session('status'))
                <div class="alert alert-success text-center">A password reset link has been sent to your email!</div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" novalidate>
                @csrf
                <!-- Email Input with Floating Label -->
                <div class="form-floating mb-3">
                    <input type="email" class="form-control border-secondary bg-white @error('email') is-invalid @enderror" id="email" placeholder="Enter email" required name="email" value="{{ old('email') }}">
                    <label for="email">Email address</label>
                    @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary w-100">Send Password Reset Link</button>
            </form>

            <div class="text-center mt-2">
                <a class="text-white" href="{{ route('login') }}">Back to login</a>
            </div>
        </div>
    </div>
</x-head>
