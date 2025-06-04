@extends('layouts.app')

@section('content')
<section class="content-header">
    <div class="container-fluid">
        <div class="row align-items-center mb-3">
            <div class="col-md-6">
                <h1 class="mb-0">👤 Customer Details</h1>
            </div>
            <div class="col-md-6 text-md-end mt-2 mt-md-0">
                <a class="btn btn-outline-secondary" href="{{ route('customers.index') }}">
                    ← Back
                </a>
            </div>
        </div>
    </div>
</section>

<div class="content px-3">
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-primary text-white rounded-top-4">
            <h5 class="mb-0">Customer Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                @include('customers.show_fields')
            </div>
        </div>
    </div>
</div>
<style>
    .card {
        background-color: #fff;
        border-radius: 0.75rem;
    }

    h1 {
        font-size: 1.5rem;
        margin-bottom: 0;
    }

    .btn {
        margin-left: 405px;
    }
    .card-header {
        border-bottom: none;
    }
    .card-body {
        padding: 1.5rem;
    }
    .rounded-top-4 {
        border-top-left-radius: 0.75rem;
        border-top-right-radius: 0.75rem;
    }
    .shadow-sm {
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    .border-0 {
        border: none;
    }
    .rounded-4 {
        border-radius: 0.75rem;
    }
    .text-md-end {
        text-align: right;
    }
    .mb-0 {
        margin-bottom: 0;
    }
    .mb-3 {
        margin-bottom: 1rem;
    }
    .mt-2 {
        margin-top: 0.5rem;
    }
    .mt-md-0 {
        margin-top: 0;
    }       
</style>
@endsection
