<div class="container">
    <h3>Collection Detail - Reference: {{ $referenceNumber }}</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Client Name</th>
                <th>Due Date</th>
                <th>Loan Balance</th>
                <th>Daily</th>
                <th>Collection</th>
                <th>Type</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($payments as $payment)
                <tr>
                    <td>{{ $payment->fullname }}</td>
                    <td>{{ \Carbon\Carbon::parse($payment->due_date)->format('Y-m-d') }}</td>
                    <td>₱{{ number_format($payment->loan_balance, 2) }}</td>
                    <td>₱{{ number_format($payment->loan_daily, 2) }}</td>
                    <td>₱{{ number_format($payment->collection, 2) }}</td>
                    <td>{{ $payment->type }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
