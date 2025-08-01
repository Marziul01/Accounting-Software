<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="1024">
    <title>সম্পূর্ণ বিনিয়োগ রিপোর্ট</title>
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

            .signature_img {
                width: 20% !important;
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
            text-align: left !important;
        }

        .img {
            width: 6%;
        }

        .report-footer {
            text-align: left !important;
        }

        .signature_text {
            width: fit-content;
            padding: 5px 70px 0 2px;
            border-top: #000 solid 1px;

        }

        .signature_img {
            width: 10%;
            height: auto;
            padding-bottom: 3px;
            border-bottom: #000 solid 1px;
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
        $totalLoss = 0;
        $totalGain = 0;
    @endphp

    <div class="container-fluid my-4">
        <div class="report-header">
            <img src="{{ asset($setting->site_logo) }}" height="100%" class="img" alt="">
            <h2>{{ $setting->site_name_bangla }}</h2>
            <h4>সম্পূর্ণ বিনিয়োগ রিপোর্ট</h4>
            <p>{!! bn_number(\Carbon\Carbon::parse($startDate)->format('d-m-y')) !!} থেকে {!! bn_number(\Carbon\Carbon::parse($endDate)->format('d-m-y')) !!} পর্যন্ত</p>
        </div>

        @foreach ($categories as $category)
            @php
                $categoryLossTotal = 0;
                $categoryGainTotal = 0;
                $categoryNetTotal = 0;
            @endphp
            <div class="mb-5">
                <h5 class="text-center border-bottom pb-2">{{ $category->name }}</h5>

                @foreach ($category->investmentSubCategories as $subcategory)
                    @php

                        $subLossTotal = 0;
                        $subGainTotal = 0;
                        $subNetTotal = 0;

                    @endphp

                    <div class="card mb-4">
                        <div class="card-header"><strong>{{ $subcategory->name ?? 'বিনা সাবক্যাটেগরি' }}</strong></div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-bordered m-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ক্রমিক নম্বর</th>
                                            <th>তারিখ</th>
                                            <th>নাম</th>
                                            <th class="text-end">শুরুর পরিমাণ</th>
                                            <th class="text-end">জমা</th>
                                            <th class="text-end">উত্তোলন</th>
                                            <th class="text-end">লাভ/ক্ষতি</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($investments->where('investment_sub_category_id', $subcategory->id) as $investment)
                                            @php
                                                $initialAmount = $investment->allTransactions->first()->amount ?? 0;

                                                // 2. Filtered transactions (between start and end)
                                                $depositInRange = $investment->transactions
                                                    ->where('transaction_type', 'Deposit')
                                                    ->sum('amount');
                                                $withdrawInRange = $investment->transactions
                                                    ->where('transaction_type', 'Withdraw')
                                                    ->sum('amount');
                                                $investIncome = $investment->investIncome->sum('amount');
                                                $investExpense = $investment->investExpense->sum('amount');

                                                $currentAmount = $depositInRange - $withdrawInRange - $investExpense;

                                                if (
                                                    $investment->allTransactions->isNotEmpty() &&
                                                    $investment->allTransactions->first()->transaction_date >=
                                                        $startDate
                                                ) {
                                                    // If the first transaction is on or after the start date, previous amount is just the initial amount
                                                    $previousAmount = $initialAmount;
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
                                                    
                                                    $beforeinvestExpense = $investment->allinvestExpense
                                                        ->where('date', '<', $startDate)
                                                        ->sum('amount');
                                                    

                                                    $previousAmount = $depositBeforeStart - $withdrawBeforeStart - $beforeinvestExpense ;
                                                }

                                                // 3. Calculate gain/loss
                                                $subLossTotal += $depositInRange;
                                                $subGainTotal += $withdrawInRange;
                                                $subNetTotal = $subLossTotal - $subGainTotal;

                                            @endphp
                                            @php $isLast = $loop->last; @endphp
                                            <tr class="{{ $isLast ? 'last-row' : '' }}">
                                                <td>{!! bn_number($loop->iteration) !!}</td>
                                                <td>{!! bn_number(\Carbon\Carbon::parse($investment->date)->format('d-m-y')) !!}</td>
                                                <td>{{ $investment->name }}</td>


                                                <td class="text-end">{!! bn_number(number_format($previousAmount, 2)) !!} টাকা</td>

                                                <td class="text-end">{!! bn_number(number_format($depositInRange, 2)) !!} টাকা</td>
                                                <td class="text-end">{!! bn_number(number_format($withdrawInRange, 2)) !!} টাকা</td>
                                                <td class="text-end">
                                                    {!! bn_number(number_format($currentAmount, 2)) !!} টাকা
                                                </td>

                                            </tr>
                                        @endforeach
                                        @php

                                            $categoryLossTotal += $subLossTotal;
                                            $categoryGainTotal += $subGainTotal;
                                            $categoryNetTotal += $subNetTotal;

                                        @endphp
                                        <tr class="category-total bg-light">
                                            <td colspan="6" class="text-end"><strong>মোট জমা:</strong></td>
                                            <td class="text-end text-danger"><strong>{!! bn_number(number_format($subLossTotal, 2)) !!}
                                                    টাকা</strong></td>
                                        </tr>
                                        <tr class="category-total bg-light">
                                            <td colspan="6" class="text-end"><strong>মোট উত্তোলন:</strong></td>
                                            <td class="text-end text-success"><strong>{!! bn_number(number_format($subGainTotal, 2)) !!}
                                                    টাকা</strong></td>
                                        </tr>
                                        <tr class="category-total bg-warning">
                                            <td colspan="6" class="text-end"><strong>লাভ / ক্ষতি:</strong></td>
                                            <td class="text-end">
                                                <strong>
                                                    {!! bn_number(number_format($subNetTotal, 2)) !!} টাকা
                                                </strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
                @php

                    $totalLoss += $categoryLossTotal;
                    $totalGain += $categoryGainTotal;
                    $grandTotal += $categoryNetTotal;
                @endphp
                <div class="d-flex justify-content-center">
                    <table class="table table-bordered w-auto mb-3" style="min-width: 300px;">
                        <thead>
                            <tr>
                                <th colspan="2" class="text-center bg-light">{{ $category->name }} সারাংশ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>সর্বমোট জমা</strong></td>
                                <td>{!! bn_number(number_format($categoryLossTotal, 2)) !!} টাকা</td>
                            </tr>
                            <tr>
                                <td><strong>সর্বমোট উত্তোলন</strong></td>
                                <td>{!! bn_number(number_format($categoryGainTotal, 2)) !!} টাকা</td>
                            </tr>
                            <tr>
                                <td><strong>লাভ / ক্ষতি</strong></td>
                                <td>
                                    <strong>{!! bn_number(number_format($categoryNetTotal, 2)) !!} টাকা</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
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
                        <td><strong>সর্বমোট জমা</strong></td>
                        <td>{!! bn_number(number_format($totalLoss, 2)) !!} টাকা</td>
                    </tr>
                    <tr>
                        <td><strong>সর্বমোট উত্তোলন</strong></td>
                        <td>{!! bn_number(number_format($totalGain, 2)) !!} টাকা</td>
                    </tr>
                    <tr>
                        <td><strong>
                                লাভ / ক্ষতি
                            </strong></td>
                        <td>
                            <strong>{!! bn_number(number_format($grandTotal, 2)) !!} টাকা</strong>

                        </td>
                    </tr>
                    <tr>
                        <td><strong>মোট বিনিয়োগ হতে আয় :</strong></td>
                        <td>{!! bn_number(number_format($totalIncome, 2)) !!} টাকা</td>
                    </tr>
                    <tr>
                        <td><strong>মোট বিনিয়োগ হতে ব্যায় :</strong></td>
                        <td>{!! bn_number(number_format($totalExpense, 2)) !!} টাকা</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="report-footer mt-4">
            <div class="text-center">
                <div class="d-flex justify-content-start mb-3">
                    <img src="{{ asset($setting->signature) }}" height="100%" class="signature_img" alt="">
                </div>
                

                <p class="bangla-text">{{ $setting->site_owner }}</p>

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

            <p class="mt-4 text-center">রাসেল বুক দ্বারা প্রস্তুতকৃত - {!! $banglaDateTime !!} </p>
        </div>


        <div class="text-center no-print">
            <button onclick="window.print()" class="btn btn-primary mt-3">প্রিন্ট করুন</button>
        </div>
    </div>

</body>

</html>
