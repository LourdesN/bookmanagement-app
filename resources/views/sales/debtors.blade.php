@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">📌 Debtors List</h2>

    @if($debtors->isEmpty())
        <div class="alert alert-info">No debtors found. All payments are up to date.</div>
    @else
    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Customer</th>
                <th>Book</th>
                <th>Total</th>
                <th>Amount Paid</th>
                <th>Balance Due</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($debtors as $sale)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $sale->customer->first_name }} {{ $sale->customer->last_name }}</td>
                <td>{{ $sale->book->title }}</td>
                <td>Ksh {{ number_format($sale->total, 2) }}</td>
                <td>Ksh {{ number_format($sale->amount_paid, 2) }}</td>
                <td class="text-danger fw-bold">Ksh {{ number_format($sale->total - $sale->amount_paid, 2) }}</td>
                <td>
                    <span class="badge bg-warning text-dark">{{ $sale->payment_status }}</span>
                </td>
                <td>
                    <a href="{{ route('payments.create', ['sale_id' => $sale->id]) }}" class="btn btn-sm btn-primary">
                        💰 Make Payment
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection
