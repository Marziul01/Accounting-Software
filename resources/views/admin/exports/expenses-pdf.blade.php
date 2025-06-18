<!DOCTYPE html>
<html>
<head>
    <title>Expenses PDF</title>
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
    <h2>All Expenses & Categories</h2>

    @foreach ($categories as $category)
        <div class="section">
            <div>Category: {{ $category->name ?? '' }}</div>
                @if($category->expenseSubCategories->count())
                @foreach ($category->expenseSubCategories as $subcategory)
                    <div>Subcategory: {{ $subcategory->name ?? '' }}</div>

                    @if($subcategory->expenses->count())
                        <table border="1" cellpadding="5" cellspacing="0" width="100%" style="margin-top: 10px; border-collapse: collapse;">
                            <thead style="background-color: #f2f2f2;">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($subcategory->expenses as $expense)
                                    <tr>
                                        <td>{{ $expense->id }}</td>
                                        <td>{{ $expense->name }}</td>
                                        <td>{{ number_format($expense->amount, 2) }}</td>
                                        <td>{{ $expense->date }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p>No expenses</p>
                    @endif
                @endforeach
                @endif
            </div>
    @endforeach
</body>
</html>
