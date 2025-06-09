@extends('layouts.app')

@section('content')
<section class="content-header bg-info text-white py-3 shadow-sm mb-4">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <h1 class="mb-0">
                    <i class="fas fa-truck me-2"></i> Create Supplier
                </h1>
            </div>
        </div>
    </div>
</section>

<div class="content px-3">
    @include('adminlte-templates::common.errors')

    <div class="card shadow-lg border-0 rounded">
        <div class="card-header bg-light border-bottom">
            <h5 class="mb-0 fw-bold text-info">📦 Supplier Information</h5>
        </div>

        {!! Form::open(['route' => 'suppliers.store']) !!}
            <div class="card-body">
                <div class="row g-3">
                    @include('suppliers.fields')
                </div>
            </div>

            <div class="card-footer bg-light d-flex justify-content-between">
                {!! Form::submit('💾 Save', ['class' => 'btn btn-success']) !!}
                <a href="{{ route('suppliers.index') }}" class="btn btn-secondary">❌ Cancel</a>
            </div>
        {!! Form::close() !!}
    </div>
</div>
@endsection
