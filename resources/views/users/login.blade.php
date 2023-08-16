<x-layout>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header" style="background-color: #FFFFFF; text-align: center;">
                    <h3>Sign in to CORIM</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="/users/authenticate">
                        @csrf
                        <div class="form-group">
                            <label for="email">University Email address</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" required value="{{old('email')}}">
                            @error('email')
                            <p class="text-danger mt-1">{{$message}}</p>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                            @error('password')
                            <p class="text-danger mt-1">{{$message}}</p>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Login</button>
                    </form>
                    <div class="mt-3">
                        Don't have an account? <a href="/register">Sign up</a>
                    </div>
                    <div class="mb-3">
                        <a href="forgot_password.php">Forgot Password</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout>
