<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $investment->name }} বিনিয়োগ রিপোর্ট</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Tiro+Bangla:ital@0;1&display=swap');

        body {
            font-family: "Hind Siliguri", sans-serif;
            background-color: #f8f9fa;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                -webkit-print-color-adjust: exact !important;
            }

            .img {
                width: 15% !important;
            }
        }

        .report-header,
        .report-footer {
            text-align: center;
            padding: 10px 0;
        }

        .report-header {
            border-bottom: 2px solid #000;
            margin-bottom: 20px;
        }

        .card-header {
            background-color: #343a40;
            color: white;
        }

        .table-light th {
            background-color: #f1f1f1 !important;
            font-size: 12px;
        }

        .summary-box {
            background: #fff3cd;
            padding: 15px;
        }

        .tiro-font {
            font-family: 'Tiro Bangla', serif;
        }

        table tbody tr td {
            font-size: 12px;
        }

        .summary-box {
            background: #fff3cd;
            padding: 15px;
        }

        .tiro-font {
            font-family: 'Tiro Bangla', serif;
        }

        table tbody tr td {
            background-color: transparent !important;
            font-size: 12px;
        }

        table.table tbody tr:nth-of-type(odd) {
            background-color: #d4edda !important;
        }

        table.table tbody tr:nth-of-type(even) {
            background-color: #fff3cd !important;
        }

        .last-row td {
            border-bottom: 2px solid #00a652 !important;
        }

        .report-footer .text-center p {
            margin-bottom: 5px;
            font-weight: 500;
            font-size: 16px !important;
        }

        .img {
            width: 6%;
        }
    </style>
</head>

<body>

    @php
        function bn_number($number)
        {
            $eng = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            $bang = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
            return '<span class="tiro-font">' . str_replace($eng, $bang, $number) . '</span>';
        }
    @endphp

    @php
        // 1. Total transactions (all time)
        $totalDeposits = $investment->allTransactions->where('transaction_type', 'Deposit')->sum('amount');
        $totalWithdrawals = $investment->allTransactions->where('transaction_type', 'Withdraw')->sum('amount');
        $initialAmount = $investment->amount - $totalDeposits + $totalWithdrawals;

        // 2. Filtered transactions (between start and end)
        $depositInRange = $transactions->where('transaction_type', 'Deposit')->sum('amount');
        $withdrawInRange = $transactions->where('transaction_type', 'Withdraw')->sum('amount');
        $currentAmount = $depositInRange - $withdrawInRange;

        if ($startDate <= $investment->date) {
            // Start date is before investment was created, so only show current with initial
            $currentAmount += $initialAmount;
            $depositInRange += $initialAmount;
            $previousAmount = null;
        } else {
            // Start date is on or after investment date
            $depositBeforeStart = $investment->allTransactions
                ->where('transaction_type', 'Deposit')
                ->where('transaction_date', '<', $startDate)
                ->sum('amount');

            $withdrawBeforeStart = $investment->allTransactions
                ->where('transaction_type', 'Withdraw')
                ->where('transaction_date', '<', $startDate)
                ->sum('amount');

            $previousAmount = $initialAmount + $depositBeforeStart - $withdrawBeforeStart;
        }

        $deposits = $transactions->where('transaction_type', 'Deposit')->sum('amount');
        $withdrawals = $transactions->where('transaction_type', 'Withdraw')->sum('amount');

    @endphp

    <div class="container my-4">
        <div class="report-header">
            <img src="{{ asset($setting->site_logo) }}" height="100%" class="img" alt="">
            <h2>{{ $setting->site_name_bangla }}</h2>
            <h4>{{ $investment->name }} এর বিনিয়োগ রিপোর্ট</h4>
            <p> {!! bn_number($startDate ?? 'সর্বপ্রথম') !!} থেকে {!! bn_number($endDate ?? now()->format('Y-m-d')) !!} পর্যন্ত </p>
            <p><strong>নাম:</strong> {{ $investment->name }}</p>
            <p><strong>বিনিয়োগ তারিখ:</strong> {!! bn_number($investment->date) !!}</p>
            <p><strong>বিনিয়োগ বিস্তারিত:</strong> {{ $investment->description ?? 'N/A' }}</p>
            <p><strong>ক্যাটেগরি:</strong> {{ $investment->investmentSubCategory->investmentCategory->name ?? 'N/A' }} |
                <strong>সাবক্যাটেগরি:</strong> {{ $investment->investmentSubCategory->name ?? 'N/A' }}</p>
        </div>

        <div class="card">
            <div class="card-header">লেনদেন বিবরণী</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered m-0">
                        <thead class="table-light">
                            <tr>
                                <th>ক্রমিক নম্বর</th>
                                <th>তারিখ</th>
                                <th>ধরণ</th>
                                <th>বিবরণী</th>
                                <th class="text-end">পরিমাণ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions->sortBy('transaction_date') as $txn)
                                @php $isLast = $loop->last; @endphp
                                <tr class="{{ $isLast ? 'last-row' : '' }}">
                                    <td>{!! bn_number($loop->iteration) !!}</td>
                                    <td>{!! bn_number(\Carbon\Carbon::parse($txn->transaction_date)->format('d-m-y')) !!}</td>
                                    <td>{{ $txn->transaction_type == 'Deposit' ? 'জমা' : 'উত্তোলন' }}</td>
                                    <td>{{ $txn->description}}</td>
                                    <td class="text-end">{!! bn_number(number_format($txn->amount, 2)) !!} টাকা</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">কোনো লেনদেন পাওয়া যায়নি</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-4">
            <table class="table table-bordered w-auto summary-box mb-0" style="min-width: 350px;">
                <thead>
                    <tr>
                        <th colspan="2" class="text-center bg-warning">সারাংশ</th>
                    </tr>
                </thead>
                <tbody>

                    <tr>
                        <td><strong>শুরুর পরিমাণ</strong></td>
                        <td>{!! bn_number(number_format(abs($initialAmount), 2)) !!} টাকা</td>
                    </tr>
                    @if ($previousAmount !== null && $previousAmount != $initialAmount)
                        <tr>
                            <td><strong>পূর্বের ব্যালেন্স </strong></td>
                            <td>{!! bn_number(number_format(abs($previousAmount), 2)) !!} টাকা</td>
                        </tr>
                    @endif
                    <tr>
                        <td><strong>মোট জমা</strong></td>
                        <td>{!! bn_number(number_format(abs($depositInRange), 2)) !!} টাকা</td>
                    </tr>
                    <tr>
                        <td><strong>মোট উত্তোলন</strong></td>
                        <td>{!! bn_number(number_format(abs($withdrawInRange), 2)) !!} টাকা</td>
                    </tr>

                    <tr>
                        <td><strong>লাভ / ক্ষতি</strong></td>
                        <td>
                            @if ($currentAmount < 0)
                                <span class="text-success">লাভ: {!! bn_number(number_format(abs($currentAmount), 2)) !!} টাকা</span>
                            @elseif($currentAmount > 0)
                                <span class="text-danger">ক্ষতি: {!! bn_number(number_format(abs($currentAmount), 2)) !!} টাকা</span>
                            @else
                                <span class="text-muted">ব্যালেন্স সমান</span>
                            @endif
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="report-footer mt-4">
            <div class="text-center">
                <p class="bangla-text">{{ $setting->site_name_bangla }}</p>

                <p class="bangla-text">
                    ঠিকানা: {!! preg_replace_callback(
                        '/[০-৯]+/u',
                        function ($m) {
                            return '<span class="tiro-font">' . $m[0] . '</span>';
                        },
                        e($setting->site_address),
                    ) !!}
                </p>

                <p class="bangla-text">
                    ইমেইল: {!! preg_replace_callback(
                        '/[০-৯]+/u',
                        function ($m) {
                            return '<span class="tiro-font">' . $m[0] . '</span>';
                        },
                        e($setting->site_email),
                    ) !!}
                </p>

                <p class="bangla-text">
                    ওয়েবসাইট : {!! preg_replace_callback(
                        '/[০-৯]+/u',
                        function ($m) {
                            return '<span class="tiro-font">' . $m[0] . '</span>';
                        },
                        e($setting->site_website ?? 'www.example.com'),
                    ) !!}
                </p>

            </div>

            @php
                use Illuminate\Support\Carbon;

                $banglaMonths = [
                    'January' => 'জানুয়ারি',
                    'February' => 'ফেব্রুয়ারি',
                    'March' => 'মার্চ',
                    'April' => 'এপ্রিল',
                    'May' => 'মে',
                    'June' => 'জুন',
                    'July' => 'জুলাই',
                    'August' => 'আগস্ট',
                    'September' => 'সেপ্টেম্বর',
                    'October' => 'অক্টোবর',
                    'November' => 'নভেম্বর',
                    'December' => 'ডিসেম্বর',
                ];

                $banglaMeridiem = ['AM' => 'পূর্বাহ্ণ', 'PM' => 'অপরাহ্ণ'];

                $now = Carbon::now();
                $formatted = $now->format('d F, Y h:i A'); // Example: 31 May, 2025 09:45 PM

                // Translate English month and AM/PM to Bangla
                $formatted = str_replace(array_keys($banglaMonths), array_values($banglaMonths), $formatted);
                $formatted = str_replace(array_keys($banglaMeridiem), array_values($banglaMeridiem), $formatted);

                $banglaDateTime = bn_number($formatted);
            @endphp

            <p class="mt-4">রাসেল বুক দ্বারা প্রস্তুতকৃত - {!! $banglaDateTime !!} </p>
        </div>

        <div class="text-center no-print">
            <button onclick="window.print()" class="btn btn-primary mt-3">প্রিন্ট করুন</button>
        </div>
    </div>

</body>

</html>
