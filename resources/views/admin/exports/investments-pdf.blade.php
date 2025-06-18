<!DOCTYPE html>
<html>
<head>
    <title>Investments PDF</title>
    <style>
    @font-face {
        font-family: 'solaimanlipi';
        src: url("{{ storage_path('fonts/SolaimanLipi.ttf') }}") format('truetype');
        font-weight: normal;
        font-style: normal;
    }
    body {
        font-family: 'solaimanlipi', sans-serif;
        font-size: 12px;
        direction: ltr;
    }
    .section { margin-bottom: 20px; }
    .title { font-weight: bold; margin-bottom: 5px; }
    table {
        border-collapse: collapse;
        width: 100%;
    }
    th, td {
        border: 1px solid #333;
        padding: 5px;
    }
    thead {
        background-color: #f2f2f2;
    }
</style>
</head>
<body>
    <h2>All Investments & Transactions</h2>

    @foreach ($investments as $investment)
        <div class="section">
            <div class="title">Investment: {{ $investment->name }}</div>
            <div>Category: {{ $investment->investmentSubCategory->investmentCategory->name ?? '' }}</div>
            <div>Subcategory: {{ $investment->investmentSubCategory->name ?? '' }}</div>
            <div>Description: {{ $investment->description ?? '' }}</div>
            <div>Date: {{ $investment->date ?? '' }}</div>

            @if($investment->transactions->count())
                <table border="1" cellpadding="5" cellspacing="0" width="100%" style="margin-top: 10px; border-collapse: collapse;">
                    <thead style="background-color: #f2f2f2;">
                        <tr>
                            <th>ID</th>
                            <th>Transaction Type</th>
                            <th>Amount</th>
                            <th>Transaction Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($investment->transactions as $tx)
                            <tr>
                                <td>{{ $tx->id }}</td>
                                <td>{{ $tx->transaction_type }}</td>
                                <td>{{ number_format($tx->amount, 2) }}</td>
                                <td>{{ $tx->transaction_date }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No transactions</p>
            @endif
        </div>
    @endforeach
</body>
</html>
