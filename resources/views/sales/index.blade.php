@extends('layouts.app')

@section('content')
    <section class="content-header bg-light py-3 mb-4 border-bottom">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="mb-0 text-primary">📘 Sales Records</h2>
                </div>
                <div class="col-md-6 text-md-right mt-3 mt-md-0">
                    <a class="btn btn-success mr-2" href="{{ route('sales.create') }}">
                        ➕ Add New Sale
                    </a>
                    <a class="btn btn-outline-primary" href="{{ route('sales.debtors') }}">
                        🧾 View Debtors
                    </a>
                </div>
            </div>
        </div>
    </section>

    <div class="content px-3">
        @include('sweetalert::alert')

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">📋 All Sales</h5>
            </div>

            <div class="card-body table-responsive p-0">
                @include('sales.table')
            </div>
        </div>
    </div>
@endsection
