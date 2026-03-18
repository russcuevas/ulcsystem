<div class="content-wrapper">
    <section class="content pt-3">
        <div class="container-fluid">

            <!-- Client Info -->
            <div class="card">
                <div class="card-header">
                    <h3>{{ $client->fullname }} - Loans</h3>
                </div>
                <div class="card-body">
                    <p><strong>Phone:</strong> {{ $client->phone }}</p>
                    <p><strong>Address:</strong> {{ $client->address }}</p>
                </div>
            </div>

            <!-- Loans Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Loan Records</h3>
                </div>

                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>PN Number</th>
                                <th>Release #</th>
                                <th>Loan Amount</th>
                                <th>Balance</th>
                                <th>Daily</th>
                                <th>Status</th>
                                <th>Loan From</th>
                                <th>Loan To</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($loans as $loan)
                                <tr>
                                    <td>{{ $loan->pn_number }}</td>
                                    <td>{{ $loan->release_number }}</td>
                                    <td>{{ number_format($loan->loan_amount, 2) }}</td>
                                    <td>{{ number_format($loan->balance, 2) }}</td>
                                    <td>{{ number_format($loan->daily, 2) }}</td>
                                    <td>{{ $loan->status }}</td>
                                    <td>{{ $loan->loan_from }}</td>
                                    <td>{{ $loan->loan_to }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No loans found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </section>
</div>
