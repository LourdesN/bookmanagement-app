@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row">
        <!-- Profile Info -->
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">👤 My Profile</h4>
                </div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $user->name }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                </div>
            </div>
        </div>

        <!-- Password Update Form -->
        <div class="col-md-7">
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">🔒 Change Password</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.updatePassword') }}" method="POST">
                        @csrf

                        <div class="mb-3 position-relative">
                            <label>Current Password</label>
                            <div class="input-group">
                                <input type="password" name="current_password" class="form-control" required id="current_password">
                                <span class="input-group-text" onclick="togglePassword('current_password')">
                                    👁️
                                </span>
                            </div>
                            @error('current_password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3 position-relative">
                            <label>New Password</label>
                            <div class="input-group">
                                <input type="password" name="new_password" class="form-control" required id="new_password">
                                <span class="input-group-text" onclick="togglePassword('new_password')">
                                    👁️
                                </span>
                            </div>
                        </div>

                        <div class="mb-4 position-relative">
                            <label>Confirm New Password</label>
                            <div class="input-group">
                                <input type="password" name="new_password_confirmation" class="form-control" required id="confirm_password">
                                <span class="input-group-text" onclick="togglePassword('confirm_password')">
                                    👁️
                                </span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-success w-100">Update Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for toggling password visibility -->
<script>
    function togglePassword(fieldId) {
        const input = document.getElementById(fieldId);
        input.type = input.type === "password" ? "text" : "password";
    }
</script>
@endsection
