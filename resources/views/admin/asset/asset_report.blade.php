<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $asset->name }} সম্পদ রিপোর্ট</title>
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

        $category = $asset->category->name ?? '';
        $subcategory = $asset->subcategory->name ?? '';
        
    @endphp

    @php
        // 1. Total transactions (all time)
        $totalDeposits = $asset->allTransactions->where('transaction_type', 'Deposit')->sum('amount');
        $totalWithdrawals = $asset->allTransactions->where('transaction_type', 'Withdraw')->sum('amount');
        $initialAmount = $asset->amount - $totalDeposits + $totalWithdrawals;

        // 2. Filtered transactions (between start and end)
        $depositInRange = $transactions->where('transaction_type', 'Deposit')->sum('amount');
        $withdrawInRange = $transactions->where('transaction_type', 'Withdraw')->sum('amount');
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

            $previousAmount = $initialAmount + $depositBeforeStart - $withdrawBeforeStart;
        }

    @endphp

    <div class="container-fluid my-4">
        <div class="report-header text-center border-bottom mb-4">
            <img src="{{ asset($setting->site_logo) }}" height="100%" class="img" alt="">
            <h2>{{ $setting->site_name_bangla }}</h2>
            <h4>{{ $subcategory }}</h4>
            <h4>{{ $asset->name }} এর সম্পদের রিপোর্ট</h4>
            <p class=""> {!! bn_number($startDate ?? 'সর্বপ্রথম') !!} থেকে {!! bn_number($endDate ?? now()->format('Y-m-d')) !!} পর্যন্ত </p>
        </div>

        <div class="d-flex justify-content-center mt-4">
    <table class="table table-bordered w-auto mb-4">
        <tbody>
            <tr>
                <td><strong>নামঃ</strong></td>
                <td>{{ $asset->name ?? '' }}</td>
                <td><strong>জাতীয় আইডি কার্ড নাম্বার</strong></td>
                <td>{{ $asset->national_id ?? '' }}</td>
            </tr>
            <tr>
                <td><strong>মোবাইল নাম্বার</strong></td>
                <td>{{ $asset->mobile ?? '' }}</td>
                <td><strong>ইমেইলের ঠিকানা</strong></td>
                <td>{{ $asset->email ?? '' }}</td>
            </tr>
            <tr>
                <td><strong>বাবার নাম</strong></td>
                <td>{{ $asset->father_name ?? '' }}</td>
                <td><strong>বাবার মোবাইল নাম্বার</strong></td>
                <td>{{ $asset->father_mobile ?? '' }}</td>
            </tr>
            <tr>
                <td><strong>মায়ের নাম</strong></td>
                <td>{{ $asset->mother_name ?? '' }}</td>
                <td><strong>মায়ের মোবাইল নাম্বার</strong></td>
                <td>{{ $asset->mother_mobile ?? '' }}</td>
            </tr>
            <tr>
                <td><strong>স্বামী বা স্ত্রীর নাম</strong></td>
                <td>{{ $asset->spouse_name ?? '' }}</td>
                <td><strong>স্বামী বা স্ত্রীর মোবাইল নাম্বার</strong></td>
                <td>{{ $asset->spouse_mobile ?? '' }}</td>
            </tr>
            <tr>
                <td><strong>বর্তমান ঠিকানা</strong></td>
                <td>{{ $asset->present_address ?? '' }}</td>
                <td><strong>স্থায়ী ঠিকানা</strong></td>
                <td>{{ $asset->permanent_address ?? '' }}</td>
            </tr>
        </tbody>
    </table>
    </div>

        @if ($asset->transactions->count())
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <strong>লেনদেনের বিবরণ</strong>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered m-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ক্রমিক নম্বর</th>
                                    <th>তারিখ</th>
                                    <th>ধরন</th>
                                    <th>বিবরণী</th>
                                    <th class="text-end">পরিমাণ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($transactions->isNotEmpty())
                                @foreach ($transactions->sortBy('transaction_date') as $transaction)
                                    @php $isLast = $loop->last; @endphp
                                    <tr class="{{ $isLast ? 'last-row' : '' }}">
                                        <td>{!! bn_number($loop->iteration) !!}</td>
                                        <td>{!! bn_number(\Carbon\Carbon::parse($transaction->transaction_date)->format('d-m-y')) !!}</td>
                                        <td>{{ $transaction->transaction_type == 'Deposit' ? 'জমা' : 'উত্তোলন' }}</td>
                                        <td>{{ $transaction->description }}</td>
                                        <td class="text-end ">{!! bn_number(number_format($transaction->amount, 2)) !!} টাকা</td>

                                    </tr>
                                @endforeach
                                @else
                                <tr class="table-success">
                                    <td colspan="5" class="text-center"><strong> কোন লেনদেন পাওয়া যায়নি </strong></td>
                                    
                                </tr>
                                @endif
                                <tr class="table-success">
                                    <td colspan="4" class="text-end"><strong>প্রারম্ভিক জমা / পূর্বের
                                            ব্যালেন্স</strong></td>
                                    <td class="text-end " colspan="2">{!! $previousAmount ? bn_number(number_format($previousAmount, 2)) : bn_number(number_format($initialAmount, 2)) !!} টাকা</td>
                                </tr>
                                <tr class="table-info">
                                    <td colspan="4" class="text-end"><strong>মোট জমা</strong></td>
                                    <td class="text-end" colspan="2">{!! bn_number(number_format($depositInRange, 2)) !!} টাকা</td>
                                </tr>
                                <tr class="table-warning">
                                    <td colspan="4" class="text-end"><strong>মোট উত্তোলন</strong></td>
                                    <td class="text-end " colspan="2">{!! bn_number(number_format($withdrawInRange, 2)) !!} টাকা</td>
                                </tr>

                                <tr class="table-primary">
                                    <td colspan="4" class="text-end"><strong>বর্তমান সম্পদ</strong></td>
                                    <td class="text-end " colspan="2"><strong>

                                            @if ($currentAmount < 0)
                                                <span class="badge bg-success">সম্পদের বিক্রয় :
                                                    {!! bn_number(number_format(abs($currentAmount), 2)) !!} টাকা</span>
                                            @elseif ($currentAmount > 0)
                                                <span class="badge bg-success">সম্পদ: {!! bn_number(number_format(abs($currentAmount), 2)) !!}
                                                    টাকা</span>
                                            @else
                                                <span class="badge bg-warning">লাভ বা ক্ষতি নেই</span>
                                            @endif
                                        </strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <p class="text-danger text-center">এই সম্পদের জন্য কোনো লেনদেন পাওয়া যায়নি।</p>
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
                        <td><strong>সম্পদের নাম</strong></td>
                        <td>{{ $asset->name }}</td>
                    </tr>
                    <tr>
                        <td><strong>বিবরণ</strong></td>
                        <td>{{ $asset->description ?? 'নেই' }}</td>
                    </tr>
                    <tr>
                        <td><strong>ক্যাটাগরি</strong></td>
                        <td>{{ $category }} > {{ $subcategory }}</td>
                    </tr>
                    <tr class="grand-total">
                        <td><strong>বর্তমান সম্পদ</strong></td>
                        <td class=""><strong>
                                @if ($currentAmount < 0)
                                    <span class="badge bg-success">সম্পদের বিক্রয় :
                                        {!! bn_number(number_format(abs($currentAmount), 2)) !!} টাকা</span>
                                @elseif ($currentAmount > 0)
                                    <span class="badge bg-success">সম্পদ: {!! bn_number(number_format(abs($currentAmount), 2)) !!}
                                        টাকা</span>
                                @else
                                    <span class="badge bg-warning">লাভ বা ক্ষতি নেই</span>
                                @endif
                            </strong></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="report-footer mt-4">
            <div class="text-center">
                <div class="d-flex justify-content-start mb-3">
                    <img src="{{ asset($setting->signature) }}" height="100%" class="signature_img" alt="">
                </div>
                <p class="signature_text mb-3">স্বাক্ষর</p>

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

            <p class="mt-4">রাসেল বুক দ্বারা প্রস্তুতকৃত - {!! $banglaDateTime !!} </p>
        </div>

        <div class="text-center no-print">
            <button onclick="window.print()" class="btn btn-primary mt-3">প্রিন্ট করুন</button>
        </div>
    </div>
</body>

</html>
