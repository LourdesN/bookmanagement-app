<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inventory Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 20px;
        }

        h2 {
            text-align: center;
            text-transform: uppercase;
            margin-bottom: 30px;
            font-size: 18px;
            border-bottom: 2px solid #444;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #888;
            padding: 8px 10px;
            text-align: left;
        }

        th {
            background-color: #e6f2ff;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 11px;
            color: #777;
        }
    </style>
</head>
<body>
    <h2>Inventory Report</h2>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Book Title</th>
                <th>Quantity</th>
                <th>Location</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inventories as $index => $inventory)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $inventory->book->title ?? 'N/A' }}</td>
                    <td>{{ $inventory->quantity }}</td>
                    <td>{{ $inventory->location }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ \Carbon\Carbon::now()->format('F d, Y H:i') }}
    </div>
</body>
</html>
