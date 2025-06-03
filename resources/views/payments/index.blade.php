@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Payments</h2>

   <a href="{{ route('payments.downloadPdf') }}" class="btn btn-success mb-3">
        <i class="fas fa-file-pdf"></i> Download PDF
    </a>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Sale ID</th>
                <th>Customer Name</th>
                <th>Book Title</th>
                <th>Amount</th>
                <th>Payment Date</th>
                <th>Recorded At</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payments as $payment)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $payment->sale_id }}</td>
                  <td>{{ $payment->sale->customer->first_name ?? 'N/A' }}</td>
                    <td>{{ $payment->sale->book->title ?? 'N/A' }}</td>
                    <td>Ksh {{ number_format($payment->amount, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M, Y') }}</td>
                    <td>{{ $payment->created_at->format('d M, Y h:i A') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-muted">No payments recorded yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
