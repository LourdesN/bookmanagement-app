@extends('layouts.app')

@section('content')
@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-lg rounded-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">💳 Record Payment</h4>
                </div>

                <div class="card-body">

                    <form method="POST" action="{{ route ('payments.store') }}">
                        @csrf

                        @if($sale)
                            <input type="hidden" name="sale_id" value="{{ $sale->id }}">

                            <div class="form-group mb-3">
                                <label class="fw-bold">Customer Name:</label>
                                <input type="text" class="form-control bg-light" 
                                       value="{{ $sale->customer->first_name }} {{ $sale->customer->last_name }}" readonly>
                            </div>

                            <div class="form-group mb-3">
                                <label class="fw-bold">Total Remaining:</label>
                                <input type="text" class="form-control bg-light text-danger fw-bold" 
                                       value="Ksh {{ number_format($sale->total - $sale->amount_paid, 2) }}" readonly>
                            </div>
                        @endif

                        <div class="form-group mb-3">
                            <label class="fw-bold">Amount Paying Now:</label>
                            <input type="number" name="amount" class="form-control" required min="1"
                                   max="{{ $sale ? $sale->total - $sale->amount_paid : '' }}"
                                   placeholder="Enter amount to pay">
                        </div>

                        <div class="form-group mb-4">
                            <label class="fw-bold">Payment Date:</label>
                            <input type="date" name="payment_date" class="form-control" 
                                   value="{{ date('Y-m-d') }}" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-check-circle me-1"></i> Submit Payment
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
