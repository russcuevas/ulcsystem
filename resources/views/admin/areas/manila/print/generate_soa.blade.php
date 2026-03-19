<!DOCTYPE html>
<html>
<head>
    <title>ULC System</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        h2 { text-align: center; margin-bottom: 0; }
        .header { text-align: center; margin-bottom: 10px; }
        .header div { margin: 2px 0; }
        .details { margin-top: 10px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid #000; }
        th, td { padding: 5px; text-align: left; font-size: 12px; }
        th { background-color: #f2f2f2; }
        .total { font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body onload="window.print()">

    <h2>STATEMENT OF ACCOUNT</h2>

    <div class="header">
        <div class="fw-bold" style="font-size:16px;">ULTRARITZ LENDING CORPORATION</div>
    </div>

    <div class="details">
        <strong>Client:</strong> {{ $client->fullname }} <br>
        <strong>Phone:</strong> {{ $client->phone }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Ref #</th>
                <th>PN Number</th>
                <th>Due Date</th>
                <th>Daily</th>
                <th>Old Balance</th>
                <th>Collection</th>
                <th>Type</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payments as $payment)
                <tr>
                    <td>{{ $payment->reference_number }}</td>
                    <td>{{ $loan->pn_number }}</td>
                    <td>{{ \Carbon\Carbon::parse($payment->due_date)->format('M d, Y') }}</td>
                    <td>₱{{ number_format($payment->daily, 2) }}</td>
                    <td>₱{{ number_format($payment->old_balance, 2) }}</td>
                    <td>₱{{ number_format($payment->collection, 2) }}</td>
                    <td>{{ ucfirst($payment->type) }}</td>
                    <td>
                        Collected
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">No payments found</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>