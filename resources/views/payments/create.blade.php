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
                            <input type="number" id="amount" name="amount" class="form-control" required min="1" placeholder="Enter amount to pay">
                            <div id="amount-error" class="text-danger mt-1" style="display: none;"></div>
                        </div>
                        @if($sale)
                        <script>
                       // This safely assigns the PHP value into a JavaScript variable
                          const remainingAmount = {{ json_encode($sale->total - $sale->amount_paid) }};
                        </script>
                        @endif

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
@section('scripts')
<script>
    document.querySelector('form').addEventListener('submit', function(e) {
        const amountInput = document.querySelector('input[name="amount"]');
        const amount = parseFloat(amountInput.value);
        const maxAmount = parseFloat(amountInput.getAttribute('max'));

        if (amount > maxAmount) {
            e.preventDefault();
            alert(`The amount cannot exceed the remaining balance of Ksh ${maxAmount.toFixed(2)}`);
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const amountInput = document.getElementById('amount');
        const errorDiv = document.getElementById('amount-error');

        amountInput.addEventListener('input', function () {
            const enteredValue = parseFloat(amountInput.value);
            if (!isNaN(enteredValue) && enteredValue > remainingAmount) {
                errorDiv.style.display = 'block';
                errorDiv.textContent = `The amount cannot exceed Ksh ${remainingAmount.toLocaleString()}`;
                amountInput.classList.add('is-invalid');
            } else {
                errorDiv.style.display = 'none';
                errorDiv.textContent = '';
                amountInput.classList.remove('is-invalid');
            }
        });
    });
</script>
@endsection

