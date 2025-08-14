<div class="login-form">
    <!-- Display Authentication Errors -->
    @if (session()->has('error'))
        <div class="alert alert-danger text-center small">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit.prevent="login" method="POST" class="w-100">
        @csrf

        <!-- Email Input -->
        <div class="form-floating mb-3">
            <input type="email" wire:model.live="email"
                   class="form-control form-control-md border-0 @error('email') is-invalid @enderror"
                   id="email" placeholder="Enter email" required>
            <label for="email">Email address</label>
            @error('email')
                <div class="invalid-feedback login-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password Input -->
        <div class="mb-3">
            <div class="form-floating">
                <input type="password" wire:model.live="password"
                       class="form-control form-control-md border-0 @error('password') is-invalid @enderror"
                       id="password"
                       placeholder="Password"
                       required>
                <label for="password">Password</label>
            </div>
            @error('password')
                <div class="invalid-feedback login-error d-block">{{ $message }}</div>
            @enderror
        </div>

        <!-- Sign In Button with Spinner -->
        <div class="mb-3">
            <button type="submit" class="btn btn-sign btn-lg w-100 d-flex align-items-center justify-content-center" wire:loading.attr="disabled">
                <span>Sign in</span>
                <div wire:loading wire:target="login" class="ms-2">
                    <div class="spinner-grow spinner-grow-sm text-dark" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </button>
        </div>

        <!-- Forgot Password -->
        <div class="mt-2 text-center">
            <a class="text-white" href="{{ route('password.request') }}">Forgot Password?</a>
        </div>
    </form>
</div>
