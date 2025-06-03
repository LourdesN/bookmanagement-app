<!DOCTYPE html>
<html>
<head>
    <title>Payments Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <h2>Payments Report</h2>

    <table>
        <thead>
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
            @foreach($payments as $payment)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $payment->sale_id }}</td>
                    <td>{{ $payment->sale->customer->first_name ?? 'N/A' }} {{ $payment->sale->customer->last_name ?? '' }}</td>
                    <td>{{ $payment->sale->book->title ?? 'N/A' }}</td>
                    <td>Ksh {{ number_format($payment->amount, 2) }}</td>
                    <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M, Y') }}</td>
                    <td>{{ $payment->created_at->format('d M, Y h:i A') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
