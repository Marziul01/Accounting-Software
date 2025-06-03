<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>সম্পূর্ণ সম্পদ রিপোর্ট</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&family=Tiro+Bangla&display=swap');

        body {
            font-family: "Hind Siliguri", sans-serif;
            background-color: #f8f9fa;
        }

        .tiro-font {
            font-family: "Tiro Bangla", serif;
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

        .category-total,
        .subcategory-total,
        .subsubcategory-total,
        .grand-total {
            background-color: #d4edda;
            font-weight: bold;
        }

        .summary-box {
            background: #fff3cd;
            padding: 15px;
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

        $grandTotal = 0;

    @endphp

    <div class="container-fluid my-4">
        <div class="report-header">
            <img src="{{ asset($setting->site_logo) }}" height="100%" class="img" alt="">
            <h2>{{ $setting->site_name_bangla }}</h2>
            <h4>সম্পূর্ণ সম্পদ রিপোর্ট</h4>
            <p class=""> {!! bn_number($startDate ?? 'সর্বপ্রথম') !!} থেকে {!! bn_number($endDate ?? now()->format('Y-m-d')) !!} পর্যন্ত </p>
        </div>

        @foreach ($categories as $category)
            @php $categorytotal = 0; @endphp
            @if ($category->assetSubCategories->count())
                <div class="mb-5">
                    <h5 class="text-center border-bottom pb-2">{{ $category->name }}</h5>

                    @foreach ($category->assetSubCategories as $subcategory)
                        @php $subcategoryTotal = 0; @endphp
                        @if ($subcategory->assetSubSubCategories->count())
                            @foreach ($subcategory->assetSubSubCategories as $subsubcategory)
                                @php
                                    $subsubdeposit = 0;
                                    $subsubwithdraw = 0;
                                    $subsubtotal = 0;
                                @endphp
                                @if ($assets->count())
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <strong>{{ $subcategory->name }} → {{ $subsubcategory->name }}</strong>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-bordered m-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>ক্রমিক নম্বর</th>
                                                            <th>তারিখ</th>
                                                            <th>নাম</th>
                                                            <th class="text-end">প্রারম্ভিক জমা / পূর্বের ব্যালেন্স</th>
                                                            <th class="text-end">মোট জমা</th>
                                                            <th class="text-end">মোট উত্তোলন</th>
                                                            <th class="text-end">পরিমাণ</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($assets->where('subsubcategory_id', $subsubcategory->id) as $asset)
                                                            @php
                                                                $totalDeposits = $asset->allTransactions
                                                                    ->where('transaction_type', 'Deposit')
                                                                    ->sum('amount');
                                                                $totalWithdrawals = $asset->allTransactions
                                                                    ->where('transaction_type', 'Withdraw')
                                                                    ->sum('amount');
                                                                $initialAmount =
                                                                    $asset->amount - $totalDeposits + $totalWithdrawals;

                                                                // 2. Filtered transactions (between start and end)
                                                                $depositInRange = $asset->transactions
                                                                    ->where('transaction_type', 'Deposit')
                                                                    ->sum('amount');
                                                                $withdrawInRange = $asset->transactions
                                                                    ->where('transaction_type', 'Withdraw')
                                                                    ->sum('amount');
                                                                $currentAmount = $depositInRange - $withdrawInRange;

                                                                if ($startDate <= $asset->entry_date) {
                                                                    // Start date is before investment was created, so only show current with initial
                                                                    $currentAmount += $initialAmount;
                                                                    $depositInRange += $initialAmount;
                                                                    $previousAmount = null;
                                                                } else {
                                                                    // Start date is on or after investment date
                                                                    $depositBeforeStart = $asset->allTransactions
                                                                        ->where('transaction_type', 'Deposit')
                                                                        ->where('transaction_date', '<', $startDate)
                                                                        ->sum('amount');

                                                                    $withdrawBeforeStart = $asset->allTransactions
                                                                        ->where('transaction_type', 'Withdraw')
                                                                        ->where('transaction_date', '<', $startDate)
                                                                        ->sum('amount');

                                                                    $previousAmount =
                                                                        $initialAmount +
                                                                        $depositBeforeStart -
                                                                        $withdrawBeforeStart;
                                                                }

                                                                $subsubdeposit += $depositInRange;
                                                                $subsubwithdraw += $withdrawInRange;
                                                                $subsubtotal = $subsubdeposit - $subsubwithdraw;

                                                            @endphp
                                                            @php $isLast = $loop->last; @endphp
                                                            <tr class="{{ $isLast ? 'last-row' : '' }}">
                                                                <td>{!! bn_number($loop->iteration) !!}</td>
                                                                <td>{!! bn_number(\Carbon\Carbon::parse($asset->entry_date)->format('d-m-y')) !!}</td>
                                                                <td>{{ $asset->name }}</td>
                                                                <td class="text-end tiro">{!! $previousAmount ? bn_number(number_format($previousAmount, 2)) : bn_number(number_format($initialAmount, 2)) !!} টাকা
                                                                </td>
                                                                <td class="text-end tiro">{!! bn_number(number_format($depositInRange, 2)) !!} টাকা
                                                                </td>
                                                                <td class="text-end tiro">{!! bn_number(number_format($withdrawInRange, 2)) !!} টাকা
                                                                </td>
                                                                <td class="text-end tiro">
                                                                    @if ($currentAmount < 0)
                                                                        <span class="text-success">সম্পদের বিক্রয়:
                                                                            {!! bn_number(number_format(abs($currentAmount), 2)) !!}
                                                                            টাকা</span>
                                                                    @elseif ($currentAmount > 0)
                                                                        <span class="text-success">সম্পদ:
                                                                            {!! bn_number(number_format(abs($currentAmount), 2)) !!}
                                                                            টাকা</span>
                                                                    @else
                                                                        <span class="text-secondary">লাভ বা ক্ষতি
                                                                            নেই</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        @php
                                                            $subcategoryTotal += $subsubtotal;

                                                        @endphp

                                                        <tr class="subsubcategory-total">
                                                            <td colspan="6" class="text-end">
                                                                <strong>{{ $subsubcategory->name }} মোট:</strong>
                                                            </td>
                                                            <td class="text-end tiro"><strong>
                                                                    @if ($subsubtotal < 0)
                                                                        <span class="text-success">সম্পদের বিক্রয়:
                                                                            {!! bn_number(number_format(abs($subsubtotal), 2)) !!}
                                                                            টাকা</span>
                                                                    @elseif ($subsubtotal > 0)
                                                                        <span class="text-success">সম্পদ:
                                                                            {!! bn_number(number_format(abs($subsubtotal), 2)) !!}
                                                                            টাকা</span>
                                                                    @else
                                                                        <span class="text-secondary">লাভ বা ক্ষতি
                                                                            নেই</span>
                                                                    @endif
                                                                </strong></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="">
                                        <strong>{{ $subcategory->name }} → {{ $subsubcategory->name }}</strong> এর জন্য
                                        কোনো সম্পদ নেই।
                                @endif
                            @endforeach
                            @php

                                $categorytotal += $subcategoryTotal;
                            @endphp
                            <div class="text-end mb-2 pe-3 subcategory-total">
                                <strong>{{ $subcategory->name }} মোট:</strong>
                                <span class="tiro">
                                    @if ($subcategoryTotal < 0)
                                        <span class="text-success">সম্পদের বিক্রয়:
                                            {!! bn_number(number_format(abs($subcategoryTotal), 2)) !!}
                                            টাকা</span>
                                    @elseif ($subcategoryTotal > 0)
                                        <span class="text-success">সম্পদ:
                                            {!! bn_number(number_format(abs($subcategoryTotal), 2)) !!}
                                            টাকা</span>
                                    @else
                                        <span class="text-secondary">লাভ বা ক্ষতি
                                            নেই</span>
                                    @endif
                                </span>
                            </div>
                        @endif
                    @endforeach
                    @php

                        $grandTotal += $categorytotal;
                    @endphp
                    <div class="text-end mt-3 pe-3 category-total">
                        <strong>{{ $category->name }} মোট:</strong>
                        <span class="tiro">
                            @if ($categorytotal < 0)
                                <span class="text-success">সম্পদের বিক্রয়:
                                    {!! bn_number(number_format(abs($categorytotal), 2)) !!}
                                    টাকা</span>
                            @elseif ($categorytotal > 0)
                                <span class="text-success">সম্পদ:
                                    {!! bn_number(number_format(abs($categorytotal), 2)) !!}
                                    টাকা</span>
                            @else
                                <span class="text-secondary">লাভ বা ক্ষতি
                                    নেই</span>
                            @endif
                        </span>
                    </div>


                </div>
            @endif
        @endforeach

        <div class="d-flex justify-content-center mt-4">
            <table class="table table-bordered w-auto summary-box mb-0" style="min-width: 350px;">
                <thead>
                    <tr>
                        <th colspan="2" class="text-center bg-warning">সারাংশ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>সর্বমোট:</strong></td>
                        <td class="text-end tiro">
                            @if ($grandTotal < 0)
                                <span class="text-success">সম্পদের বিক্রয়:
                                    {!! bn_number(number_format(abs($grandTotal), 2)) !!} টাকা</span>
                            @elseif ($grandTotal > 0)
                                <span class="text-success">সম্পদ:
                                    {!! bn_number(number_format(abs($grandTotal), 2)) !!} টাকা</span>
                            @else
                                <span class="text-secondary">লাভ বা ক্ষতি নেই</span>
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
