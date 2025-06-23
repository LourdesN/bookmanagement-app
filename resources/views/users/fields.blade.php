<!-- Name Field -->
<div class="form-group col-sm-6">
    {!! Form::label('name', 'Name:') !!}
    {!! Form::text('name', null, ['class' => 'form-control', 'required', 'maxlength' => 191, 'maxlength' => 191]) !!}
</div>

<!-- Email Field -->
<div class="form-group col-sm-6">
    {!! Form::label('email', 'Email:') !!}
    {!! Form::email('email', null, ['class' => 'form-control', 'required', 'maxlength' => 191, 'maxlength' => 191]) !!}
</div>

<!-- password Field -->
<div class="form-group col-sm-6">
    {!! Form::label('password', 'Password:') !!}
    {!! Form::password('password', ['class' => 'form-control', 'required', 'id' => 'password', 'min:8', 'confirmed', 'maxlength' => 191, 'maxlength' => 191]) !!}
</div>

<!-- Confirm Password Field -->
<div class="form-group col-sm-6">
    {!! Form::label('password_confirmation', 'Confirm Password:') !!}
    {!! Form::password('password_confirmation', ['class' => 'form-control', 'required', 'id' => 'password_confirmation', 'maxlength' => 191, 'maxlength' => 191]) !!}
</div>      

<!-- JavaScript to validate matching passwords -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('form');
        form.addEventListener('submit', function (e) {
            const password = document.getElementById('password').value.trim();
            const confirmPassword = document.getElementById('password_confirmation').value.trim();

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('⚠️ Passwords do not match. Please confirm them again.');
            }
        });
    });
</script>