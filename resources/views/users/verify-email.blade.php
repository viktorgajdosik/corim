<x-head>
    <div class="container d-flex align-items-center justify-content-center vh-100">
        <div class="card login-card bg-dark p-4">
            <h2 class="text-center text-white mb-1">Verify Your Email</h2>
            <p class="text-center text-white">We’ve sent an email verification link.</p>

            @if (session('status') == 'verification-link-sent')
                <div class="alert alert-success text-center">A new verification link has been sent!</div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn btn-primary w-100">Resend Verification Email</button>
            </form>

            <a class="text-center text-white" href="/">
                Home
            </a>
        </div>
    </div>
</x-head>
