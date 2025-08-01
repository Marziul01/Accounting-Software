<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="1024">
    <title>সম্পূর্ণ সম্পদ রিপোর্ট</title>
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

    @endphp

    <div class="container-fluid my-4">
        <div class="report-header">
            <img src="{{ asset($setting->site_logo) }}" height="100%" class="img" alt="">
            <h3>{{ $setting->site_name_bangla }}</h2>
            <h5>সম্পূর্ণ সম্পদ রিপোর্ট</h4>
            <p class=""> {!! bn_number($startDate ?? 'সর্বপ্রথম') !!} থেকে {!! bn_number($endDate ?? now()->format('Y-m-d')) !!} পর্যন্ত </p>
        </div>

        @foreach ($categories as $category)
            @php $categorytotal = 0; @endphp
            @if ($category->assetSubCategories->count())
                <div class="mb-5">
                    <h5 class="text-center border-bottom pb-2">{{ $category->name }}</h5>

                    @foreach ($category->assetSubCategories as $subcategory)
                        @php
                            $subdeposit = 0;
                            $subwithdraw = 0;
                            $subtotal = 0;
                        @endphp
                        {{-- @if ($subcategory->assetSubSubCategories->count())
                            @foreach ($subcategory->assetSubSubCategories as $subsubcategory)
                                @php
                                    $subsubdeposit = 0;
                                    $subsubwithdraw = 0;
                                    $subsubtotal = 0;
                                @endphp --}}
                                @if ($assets->count())
                                    <div class="card mb-4">
                                        <div class="card-header">
                                            <strong>{{ $subcategory->name }}</strong>
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
                                                        @foreach ($assets->where('subcategory_id', $subcategory->id) as $asset)
                                                            @php
                                                                $initialAmount = $asset->allTransactions->first()->amount ?? 0;

        // 2. Filtered transactions (between start and end)
        $depositInRange = $asset->transactions->where('transaction_type', 'Deposit')->sum('amount');
        $withdrawInRange = $asset->transactions->where('transaction_type', 'Withdraw')->sum('amount');
        $currentAmount = $depositInRange - $withdrawInRange;

        if ($asset->allTransactions->isNotEmpty() && $asset->allTransactions->first()->transaction_date >= $startDate) {
            $previousAmount = $initialAmount;
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

            $previousAmount = $depositBeforeStart - $withdrawBeforeStart;
        }

                                                                $subdeposit += $depositInRange;
                                                                $subwithdraw += $withdrawInRange;
                                                                $subtotal = $subdeposit - $subwithdraw;

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
                                                                    {!! bn_number(number_format($currentAmount, 2)) !!}
                                                                            টাকা
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                        @php

                                                            $categorytotal += $subtotal;
                                                        @endphp

                                                        <tr class="subsubcategory-total">
                                                            <td colspan="6" class="text-end">
                                                                <strong>{{ $subcategory->name }} মোট:</strong>
                                                            </td>
                                                            <td class="text-end tiro"><strong>
                                                                    {!! bn_number(number_format($subtotal, 2)) !!}
                                                                            টাকা
                                                                </strong></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="">
                                        <strong>{{ $subcategory->name }}</strong> এর জন্য
                                        কোনো সম্পদ নেই।
                                @endif
                                
                            {{-- @endforeach --}}
                            
                            {{-- <div class="text-end mb-2 pe-3 subcategory-total">
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
                        @endif --}}
                    @endforeach
                    @php

                                    $grandTotal += $categorytotal;
                                @endphp
                    <div class="text-end mt-3 pe-3 category-total">
                        <strong>{{ $category->name }} মোট:</strong>
                        <span class="tiro">
                            {!! bn_number(number_format($categorytotal, 2)) !!}
                                    টাকা
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
                            {!! bn_number(number_format($grandTotal, 2)) !!} টাকা
                        </td>
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
