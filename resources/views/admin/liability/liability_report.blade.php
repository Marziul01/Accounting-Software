<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="1024">
    <title>{{ $liability->name }} দায় রিপোর্ট</title>
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

        $category = $liability->category->name ?? '';
        $subcategory = $liability->subcategory->name ?? '';

    @endphp
    @php

        $initialAmount = $liability->allTransactions->first()->amount ?? 0;

        // 2. Filtered transactions (between start and end)
        $depositInRange = $transactions->where('transaction_type', 'Deposit')->sum('amount');
        $withdrawInRange = $transactions->where('transaction_type', 'Withdraw')->sum('amount');
        $currentAmount = $depositInRange - $withdrawInRange;

        if ($startDate <= $liability->entry_date) {
            // Start date is before investment was created, so only show current with initial

            $previousAmount = null;
        } else {
            // Start date is on or after investment date
            $depositBeforeStart = $liability->allTransactions
                ->where('transaction_type', 'Deposit')
                ->where('transaction_date', '<', $startDate)
                ->sum('amount');

            $withdrawBeforeStart = $liability->allTransactions
                ->where('transaction_type', 'Withdraw')
                ->where('transaction_date', '<', $startDate)
                ->sum('amount');

            $previousAmount = $depositBeforeStart - $withdrawBeforeStart;
        }
    @endphp
    <div class="container-fluid my-4">
        <div class="report-header text-center border-bottom mb-4">
            <img src="{{ asset($setting->site_logo) }}" height="100%" class="img" alt="">
            <h3>{{ $setting->site_name_bangla }}</h2>
                <h6>{{ $subcategory }}</h4>
                    <h6>{{ $liability->name }} দায়ের রিপোর্ট</h4>
                        <p class=""> {!! bn_number($startDate ?? 'সর্বপ্রথম') !!} থেকে {!! bn_number($endDate ?? now()->format('Y-m-d')) !!} পর্যন্ত </p>
        </div>

        <div class="d-flex justify-content-center mt-4">
            <table class="table table-bordered mb-4">
                <tbody>
                    <tr>
                        <td><strong>নামঃ</strong></td>
                        <td>{{ $liability->name ?? '' }}</td>
                        <td><strong>জাতীয় আইডি কার্ড নাম্বার</strong></td>
                        <td>{{ $liability->national_id ?? '' }}</td>
                    </tr>
                    <tr>
                        <td><strong>মোবাইল নাম্বার</strong></td>
                        <td>{{ $liability->mobile ?? '' }}</td>
                        <td><strong>ইমেইলের ঠিকানা</strong></td>
                        <td>{{ $liability->email ?? '' }}</td>
                    </tr>

                    <tr>
                        <td><strong>বর্তমান ঠিকানা</strong></td>
                        <td>{{ $liability->present_address ?? '' }}</td>
                        <td><strong>স্থায়ী ঠিকানা</strong></td>
                        <td>{{ $liability->permanent_address ?? '' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if ($liability->transactions->count())
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <strong>লেনদেনের বিবরণ</strong>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered m-0">
                            <thead class="table-light">
                                <tr>
                                    <th colspan="4" class="w-50">
                                            <div class="text-center w-full py-2">জমা</div>
                                        </th>
                                        <th colspan="4" class="w-50">
                                            <div class="text-center w-full py-2">উত্তোলন</div>
                                        </th>
                                </tr>
                                <tr>
                                    <th>ক্রমিক</th>
                                    <th>তারিখ</th>
                                    <th>বিবরণী</th>
                                    <th class="text-end">পরিমাণ</th>

                                    <th>ক্রমিক</th>
                                    <th>তারিখ</th>
                                    <th>বিবরণী</th>
                                    <th class="text-end">পরিমাণ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $depositTransactions = $transactions
                                        ->where('transaction_type', 'Deposit')
                                        ->values();
                                    $withdrawTransactions = $transactions
                                        ->where('transaction_type', 'Withdraw')
                                        ->values();
                                    $maxCount = max($depositTransactions->count(), $withdrawTransactions->count());
                                @endphp

                                @for ($i = 0; $i < $maxCount; $i++)
                                    <tr>
                                        {{-- জমা --}}
                                        @if (isset($depositTransactions[$i]))
                                            <td>{!! bn_number($i + 1) !!}</td>
                                            <td>{!! bn_number(\Carbon\Carbon::parse($depositTransactions[$i]->transaction_date)->format('d-m-y')) !!}</td>
                                            <td>{{ $depositTransactions[$i]->description }}</td>
                                            <td class="text-end">{!! bn_number(number_format($depositTransactions[$i]->amount, 2)) !!} টাকা</td>
                                        @else
                                            <td colspan="4" class="text-center text-muted">-</td>
                                        @endif

                                        {{-- উত্তোলন --}}
                                        @if (isset($withdrawTransactions[$i]))
                                            <td>{!! bn_number($i + 1) !!}</td>
                                            <td>{!! bn_number(\Carbon\Carbon::parse($withdrawTransactions[$i]->transaction_date)->format('d-m-y')) !!}</td>
                                            <td>{{ $withdrawTransactions[$i]->description }}</td>
                                            <td class="text-end">{!! bn_number(number_format($withdrawTransactions[$i]->amount, 2)) !!} টাকা</td>
                                        @else
                                            <td colspan="4" class="text-center text-muted">-</td>
                                        @endif
                                    </tr>
                                @endfor

                                <tr class="table-info">
                                    <td colspan="3" class="text-end"><strong>মোট জমা</strong></td>
                                    <td class="text-end">{!! bn_number(number_format($depositInRange, 2)) !!} টাকা</td>
                                    <td colspan="3" class="text-end"><strong>মোট উত্তোলন</strong></td>
                                    <td class="text-end">{!! bn_number(number_format($withdrawInRange, 2)) !!} টাকা</td>
                                </tr>

                                {{-- পূর্বের ব্যালেন্স --}}
                                <tr class="table-success">
                                    <td colspan="3" class="text-end"><strong>প্রারম্ভিক জমা / পূর্বের
                                            ব্যালেন্স</strong></td>
                                    <td class="text-end">{!! bn_number(number_format($previousAmount ?? $initialAmount, 2)) !!} টাকা</td>
                                    <td colspan="3" class="text-end"><strong>বর্তমান দায়
                                            </strong></td>
                                    <td class="text-end">{!! bn_number(number_format($currentAmount, 2)) !!} টাকা</td>
                                </tr>

                                
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        @else
            <p class="text-danger text-center">এই দায়ের জন্য কোনো লেনদেন পাওয়া যায়নি।</p>
        @endif

        <div class="d-flex justify-content-center mt-4">
            <table class="table table-bordered w-auto summary-box mb-0" style="min-width: 400px;">
                <thead>
                    <tr>
                        <th colspan="2" class="text-center bg-warning">সারাংশ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>দায়ের নাম</strong></td>
                        <td>{{ $liability->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>বিবরণ</strong></td>
                        <td>{{ $liability->description ?? 'নেই' }}</td>
                    </tr>
                    <tr>
                        <td><strong>ক্যাটাগরি</strong></td>
                        <td>{{ $category }} > {{ $subcategory }}</td>
                    </tr>
                    <tr class="grand-total">
                        <td><strong>বর্তমান দায়</strong></td>
                        <td class="tiro"><strong>{{ number_format($currentAmount, 2) }} Tk</strong></td>
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
