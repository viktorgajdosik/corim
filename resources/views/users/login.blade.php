<x-layout>

        <div class="row justify-content-center">

                <div class="card border rounded">
                    <div class="card-header border-bottom-0" style="background-color: white; text-align: center;">
                        <div style="background-color: aliceblue; width: 80px; height: 80px; border-radius: 50%; margin: 0 auto;">
                            <i class="fa fa-user fa-3x" style="color: #007BFF; padding: 20px;"></i>
                        </div>
                        <h3 style="color: #007BFF;">Sign in</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="/users/authenticate">
                            @csrf
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend" style="width: 40px;">
                                        <span class="input-group-text bg-light border-0" style="width: 80px; justify-content: center"><i class="fa fa-envelope"></i></span>
                                    </div>
                                    <input type="email" class="form-control bg-light border-0" id="email" name="email" placeholder="Enter email" required value="{{ old('email') }}">
                                </div>
                                @error('email')
                                <p class="text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group-prepend" style="width: 40px;">
                                        <span class="input-group-text bg-light border-0" style="width: 80px; justify-content: center"><i class="fa fa-lock"></i></span>
                                    </div>
                                    <input type="password" class="form-control bg-light border-0" id="password" name="password" placeholder="Password" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-light border-0" style="cursor: pointer; background-color: white;" onclick="togglePasswordVisibility()">
                                            <i class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                </div>
                                @error('password')
                                <p class="text-danger mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary btn-block"> Sign in</button>
                            </div>
                            <div class="mt-2">
                                <a href="" class="ml-auto">Forgot Password</a>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center border-top-0">
                        Don't have an account? <a href="/register">Sign up</a>
                    </div>
                </div>
            </div>



        <script>
            function togglePasswordVisibility() {
                var passwordInput = document.getElementById('password');
                var passwordIcon = document.querySelector('.input-group-append .fa-eye, .input-group-append .fa-eye-slash');

                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    passwordIcon.classList.remove('fa-eye');
                    passwordIcon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    passwordIcon.classList.remove('fa-eye-slash');
                    passwordIcon.classList.add('fa-eye');
                }
            }
        </script>
</x-layout>
