<x-laravel-ui-adminlte::adminlte-layout>

    <body class="hold-transition login-page" style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-image: linear-gradient(120deg, #a1c4fd 0%, #c2e9fb 100%);">
        <div class="login-box">
            <!-- Branding -->
            

            <div class="card shadow-lg">
                <div class="card-body login-card-body">

                <div class="login-logo">
                <a href="{{ url('/') }}" style="font-weight: bold; font-size: 28px; color: #007bff;">
                    <i class="fas fa-book-reader"></i> {{ config('app.name') }}
                </a>
            </div>
                    <p class="login-box-msg" style="font-size: 18px; color: #343a40;">
                        <strong>Welcome back!</strong> Please sign in to continue
                    </p>

                    <form method="POST" action="{{ url('/login') }}">
                        @csrf

                        <!-- Email -->
                        <div class="input-group mb-3">
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="Email Address"
                                   class="form-control @error('email') is-invalid @enderror" autofocus required>
                            <div class="input-group-append">
                                <div class="input-group-text bg-primary text-white">
                                    <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                            @error('email')
                                <span class="error invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="input-group mb-3">
                            <input type="password" name="password" placeholder="Password"
                                   class="form-control @error('password') is-invalid @enderror" required>
                            <div class="input-group-append">
                                <div class="input-group-text bg-primary text-white">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                            @error('password')
                                <span class="error invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Remember Me & Submit -->
                        <div class="row align-items-center">
                            <div class="col-6">
                                <div class="icheck-primary">
                                    <input type="checkbox" id="remember" name="remember">
                                    <label for="remember">Remember Me</label>
                                </div>
                            </div>
                            <div class="col-6 text-right">
                                <button type="submit" class="btn btn-primary btn-block shadow-sm">
                                    <i class="fas fa-sign-in-alt"></i> Sign In
                                </button>
                            </div>
                        </div>
                    </form>

                    <hr class="my-3">

                    <!-- Additional Links -->
                    <p class="mb-1 text-center">
                        <a href="{{ route('password.request') }}">Forgot Password?</a>
                    </p>
                    <p class="mb-0 text-center">
                        <a href="{{ route('register') }}" class="text-center">Create a New Account</a>
                    </p>
                </div>
            </div>
        </div>
    </body>
</x-laravel-ui-adminlte::adminlte-layout>

