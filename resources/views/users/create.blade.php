@extends('layouts.app')

@section('content')
<section class="content-header bg-primary text-white py-3 mb-4 shadow-sm rounded">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-12">
                <h3 class="mb-0">
                    <i class="fas fa-user-plus mr-2"></i> Create New User
                </h3>
            </div>
        </div>
    </div>
</section>

<div class="content px-3">

    {{-- Validation Errors --}}
    @include('adminlte-templates::common.errors')

    <div class="card shadow border-0">
        <div class="card-header bg-light border-bottom">
            <h5 class="mb-0 text-primary fw-bold"> <i class="fas fa-user-plus mr-2"></i> User Information</h5>
        </div>

        {!! Form::open(['route' => 'users.store']) !!}
        <div class="card-body">
            <div class="row g-3">
                @include('users.fields') {{-- this should contain the form inputs --}}
            </div>
        </div>

        <div class="card-footer bg-light d-flex justify-content-between">
            {!! Form::submit('💾 Save User', ['class' => 'btn btn-success']) !!}
            <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
        {!! Form::close() !!}
    </div>
</div>
@endsection
