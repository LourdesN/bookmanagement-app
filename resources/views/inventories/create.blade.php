@extends('layouts.app')

@section('content')
<section class="content-header bg-info text-white py-3 shadow-sm mb-4">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <h1 class="mb-0">
    <i class="fas fa-boxes me-2"></i> Create Inventories
    <i class="fas fa-info-circle text-warning ms-2" data-bs-toggle="tooltip" title="Tip: Adding a delivery will auto-create an inventory"></i>
</h1>
            </div>
        </div>
    </div>
</section>

<div class="content px-3">
    @include('adminlte-templates::common.errors')

    <div class="card shadow border-0">
        <div class="card-header bg-light border-bottom">
            <h5 class="mb-0 fw-bold text-info">Inventory Details</h5>
        </div>
    <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
    <strong>⚠️ Heads up!</strong> Inventories are automatically created when you add a delivery. Only use this form if you really need to add one manually.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

        {!! Form::open(['route' => 'inventories.store']) !!}
            <div class="card-body">
                <div class="row g-3">
                    @include('inventories.fields')
                </div>
            </div>

            <div class="card-footer bg-light d-flex justify-content-between">
                {!! Form::submit('✅ Save', ['class' => 'btn btn-success']) !!}
                <a href="{{ route('inventories.index') }}" class="btn btn-secondary">❌ Cancel</a>
            </div>
        {!! Form::close() !!}
    </div>
</div>
@endsection
@yield('scripts')
<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
