<x-laravel-ui-adminlte::adminlte-layout>
    <body class="hold-transition register-page" style=" background-image: linear-gradient(120deg, #a1c4fd 0%, #c2e9fb 100%);">
        <div class="register-box">
        
            <div class="card shadow-lg rounded">
                <div class="card-body register-card-body">
                <div class="login-logo">
                <a href="{{ url('/') }}" style="font-weight: bold; font-size: 28px; color: #007bff;">
                    <i class="fas fa-book-reader"></i> {{ config('app.name') }}
                </a>
            </div>
                    <p class="login-box-msg text-primary fw-bold">Create your account</p>

                    <form method="post" action="{{ route('register') }}">
                        @csrf

                        <div class="input-group mb-3">
                            <input type="text" name="name" value="{{ old('name') }}"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="Full Name" autofocus>
                            <div class="input-group-append">
                                <div class="input-group-text bg-primary"><span class="fas fa-user "></span></div>
                            </div>
                            @error('name')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="input-group mb-3">
                            <input type="email" name="email" value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror"
                                placeholder="Email Address">
                            <div class="input-group-append">
                                <div class="input-group-text bg-primary"><span class="fas fa-envelope"></span></div>
                            </div>
                            @error('email')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="input-group mb-3">
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="Password">
                            <div class="input-group-append">
                                <div class="input-group-text bg-primary"><span class="fas fa-lock"></span></div>
                            </div>
                            @error('password')
                                <span class="invalid-feedback d-block" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="input-group mb-3">
                            <input type="password" name="password_confirmation"
                                class="form-control"
                                placeholder="Confirm Password">
                            <div class="input-group-append">
                                <div class="input-group-text bg-primary"><span class="fas fa-lock"></span></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="icheck-primary">
                                    <input type="checkbox" id="agreeTerms" name="terms" value="agree">
                                    <label for="agreeTerms">
                                        I agree to the <a href="#">terms</a>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-block rounded-pill shadow-sm">Register</button>
                            </div>
                        </div>
                    </form>

                    <p class="mt-3 mb-0 text-center">
                        <a href="{{ route('login') }}" class="text-primary">Already have an account?</a>
                    </p>
                </div>
            </div>
        </div>
    </body>
</x-laravel-ui-adminlte::adminlte-layout>
