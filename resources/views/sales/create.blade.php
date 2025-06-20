@extends('layouts.app')

@section('content')
<section class="content-header bg-info text-white py-3 shadow-sm mb-4">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <h1 class="mb-0">
                    <i class="fas fa-dollar-sign me-2"></i> Create Sales
                </h1>
            </div>
        </div>
    </div>
</section>

<div class="content px-3">
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
    @include('adminlte-templates::common.errors')

    <div class="card shadow border-0">
        <div class="card-header bg-light border-bottom">
            <h5 class="mb-0 fw-bold text-info">Sale Details</h5>
        </div>

        {!! Form::open(['route' => 'sales.store']) !!}
           @csrf
            <div class="card-body">
                <div class="row g-3">
                    @include('sales.fields')
                </div>
            </div>

            <div class="card-footer bg-light d-flex justify-content-between">
                {!! Form::submit('💾 Save', ['class' => 'btn btn-primary']) !!}
                <a href="{{ route('sales.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        {!! Form::close() !!}
    </div>
</div>
@endsection
