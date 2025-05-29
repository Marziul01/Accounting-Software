<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $subsubcategory->name }} দায় রিপোর্ট</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&family=Tiro+Bangla&display=swap');
        body { font-family: "Hind Siliguri", sans-serif; background-color: #f8f9fa; }
        .tiro { font-family: "Tiro Bangla", serif; }
        @media print { .no-print { display: none !important; } body { -webkit-print-color-adjust: exact !important; } }
        .report-header, .report-footer { text-align: center; padding: 10px 0; }
        .report-header { border-bottom: 2px solid #000; margin-bottom: 20px; }
        .card-header { background-color: #343a40; color: white; }
        .table-light th { background-color: #f1f1f1 !important; font-size: 12px; }
        .category-total, .grand-total, .subcategory-total { background-color: #d4edda; font-weight: bold; }
        .summary-box { background: #fff3cd; padding: 15px; }
        table tbody tr td { background-color: transparent !important; font-size: 12px; }
        table.table tbody tr:nth-of-type(odd) { background-color: #d4edda !important; }
        table.table tbody tr:nth-of-type(even) { background-color: #fff3cd !important; }
    </style>
</head>
<body>
@php
        function bn_number($number) {
                $eng = ['0','1','2','3','4','5','6','7','8','9'];
                $bang = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
                return str_replace($eng, $bang, $number);
        }

        $totalAmount = 0;
@endphp

<div class="container-fluid my-4">
    <div class="report-header text-center border-bottom mb-4">
        <h2>রাসেল বুক</h2>
        <h4>{{ $subsubcategory->name }} সাব-সাব-ক্যাটাগরি দায় রিপোর্ট</h4>
        <p class="tiro">{{ bn_number($startDate ? $startDate->format('Y-m-d') : 'সর্বপ্রথম') }} থেকে {{ bn_number($endDate ? $endDate->format('Y-m-d') : now()->format('Y-m-d')) }} পর্যন্ত</p>
    </div>

    @if($subsubcategory->liabilities->count())
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                <strong>{{ $subsubcategory->name }} এর দায়সমূহের তালিকা</strong>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered m-0">
                        <thead class="table-light">
                            <tr>
                                <th>তারিখ</th>
                                <th>নাম</th>
                                <th class="text-end">প্রারম্ভিক দায়</th>
                                <th class="text-end">নতুন দায়</th>
                                <th class="text-end">পরিশোধ</th>
                                <th class="text-end">বর্তমান দায়</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subsubcategory->liabilities->sortBy('entry_date') as $liability)
                                @php
                                    $totalIncrease = $liability->transactions->where('transaction_type', 'Increase')->sum('amount');
                                    $totalPayment = $liability->transactions->where('transaction_type', 'Payment')->sum('amount');
                                    $initialLiability = $liability->amount - $totalIncrease + $totalPayment;
                                    $totalAmount += $liability->amount;
                                @endphp
                                <tr>
                                    <td class="tiro">{{ bn_number(\Carbon\Carbon::parse($liability->entry_date)->format('Y-m-d')) }}</td>
                                    <td>{{ $liability->name }}</td>
                                    <td class="text-end tiro">{{ bn_number(number_format($initialLiability, 2)) }} টাকা</td>
                                    <td class="text-end tiro">{{ bn_number(number_format($totalIncrease, 2)) }} টাকা</td>
                                    <td class="text-end tiro">{{ bn_number(number_format($totalPayment, 2)) }} টাকা</td>
                                    <td class="text-end tiro">{{ bn_number(number_format($liability->amount, 2)) }} টাকা</td>
                                </tr>
                            @endforeach
                            <tr class="table-warning">
                                <td colspan="5" class="text-end"><strong>মোট:</strong></td>
                                <td class="text-end tiro"><strong>{{ bn_number(number_format($totalAmount, 2)) }} টাকা</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <p class="text-danger text-center">{{ $subsubcategory->name }} এর জন্য কোনো দায় পাওয়া যায়নি।</p>
    @endif

    <div class="d-flex justify-content-center mt-4">
        <table class="table table-bordered w-auto summary-box mb-0" style="min-width: 350px;">
            <thead>
                <tr>
                    <th colspan="2" class="text-center bg-warning">সারাংশ</th>
                </tr>
            </thead>
            <tbody>
                <tr class="grand-total">
                    <td><strong>সর্বমোট {{ $subsubcategory->name }} দায়</strong></td>
                    <td class="tiro"><strong>{{ bn_number(number_format($totalAmount, 2)) }} টাকা</strong></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="report-footer mt-4 text-center">
        <p class="tiro">রাসেল বুক দ্বারা প্রস্তুতকৃত - {{ bn_number(now()->format('d M, Y h:i A')) }}</p>
    </div>

    <div class="text-center no-print">
        <button onclick="window.print()" class="btn btn-primary mt-3">প্রিন্ট করুন</button>
    </div>
</div>
</body>
</html>
