<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'SolaimanLipi', sans-serif; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td, th { border: 1px solid #000; padding: 6px; }
        .tiro-font {
            font-family: 'SolaimanLipi', sans-serif;
        }
        .report-header{
            text-align: center;
        }
        .signature_img {
            width: 15%;
            height: auto;
            padding-bottom: 3px;
            border-bottom: #000 solid 1px;
        }
    </style>
</head>
<body>
    @php
    if (!function_exists('bn_number')) {
  function bn_number($number)
        {
            $eng = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            $bang = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
            return '<span class="tiro-font">' . str_replace($eng, $bang, $number) . '</span>';
        }
    }
@endphp
@php

        $initialAmount = $asset->allTransactions->first()->amount ?? 0;

        // 2. Filtered transactions (between start and end)
        $depositInRange = $asset->transactions->where('transaction_type', 'Deposit')->sum('amount');
        $withdrawInRange = $asset->transactions->where('transaction_type', 'Withdraw')->sum('amount');
        $currentAmount = $depositInRange - $withdrawInRange;

        $startDate = $asset->transactions->min('transaction_date');
        $endDate = $asset->transactions->max('transaction_date');

@endphp
    <div class="report-header">
        <img src="{{ public_path($setting->site_logo) }}" height="auto" width="15%" class="img" alt="">
        <p><strong>প্রকাশের তারিখ:</strong> {!! bn_number(now()->format('d-m-Y')) !!}</p>
    </div>

    <table>
        <tr>
            <td>
                <strong>প্রেরকঃ</strong><br>
                {{ $setting->site_owner }}<br>
                ঠিকানাঃ {{ $setting->site_address }}
            </td>
            <td>
                <strong>প্রাপকঃ</strong><br>
                নামঃ {{ $asset->name }}<br>
                ঠিকানাঃ {{ $asset->present_address }}
            </td>
        </tr>
    </table>

    <h4>সংক্ষিপ্ত বিবরণী </h4>
    <div class="d-flex justify-content-center mt-4">
            <table class="table table-bordered mb-4">
                <tbody>
                    <tr>
                        <td><strong>বিবরণের তারিখ </strong></td>
                        <td>{!! bn_number($startDate ?? 'সর্বপ্রথম') !!} থেকে {!! bn_number($endDate ?? now()->format('Y-m-d')) !!}</td>
                        <td><strong>প্রারম্ভিক স্থিতি </strong></td>
                        <td>{!! bn_number(number_format($initialAmount, 2)) !!}</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td><strong>মোট প্রদানকৃত ঋণের পরিমান</strong></td>
                        <td>{!! bn_number(number_format($depositInRange, 2)) !!}</td>
                    </tr>
                    <tr>
                        <td><strong>হিসাবের ধরন</strong></td>
                        <td>ঋণ প্রদান</td>
                        <td><strong>মোট আদায়কৃত ঋণের পরিমান</strong></td>
                        <td>{!! bn_number(number_format($withdrawInRange, 2)) !!}</td>
                    </tr>
                    <tr>
                        <td><strong>মুদ্রার ধরন</strong></td>
                        <td>বাংলাদেশী টাকা</td>
                        <td><strong>মোট অবশিষ্ট অপরিশোধিত ঋণের স্থিতির পরিমান</strong></td>
                        <td>{!! bn_number(number_format($currentAmount, 2)) !!}</td>
                    </tr>
                    
                </tbody>
            </table>
        </div>

        @if ($asset->transactions->count())
            <div class="card mb-4 mt-4">
                <div class="card-header bg-dark text-white">
                    <strong>লেনদেন এর বিস্তারিত বিবরণী</strong>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered m-0">
                            <thead class="table-light">
                                <tr>
                                    
                                    
                                    
                                        <th colspan="4" class="w-50">
                                            <div class="text-center w-full py-2">প্রদান</div>
                                        </th>
                                        <th colspan="4" class="w-50">
                                            <div class="text-center w-full py-2">প্রাপ্তি</div>
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
                                    $depositTransactions = $asset->transactions
                                        ->where('transaction_type', 'Deposit')
                                        ->values();
                                    $withdrawTransactions = $asset->transactions
                                        ->where('transaction_type', 'Withdraw')
                                        ->values();
                                    $maxCount = max($depositTransactions->count(), $withdrawTransactions->count());
                                @endphp

                                @for ($i = 0; $i < $maxCount; $i++)
                                    <tr>
                                        {{-- Deposit --}}
                                        @if (isset($depositTransactions[$i]))
                                            <td>{!! bn_number($i + 1) !!}</td>
                                            <td>{!! bn_number(\Carbon\Carbon::parse($depositTransactions[$i]->transaction_date)->format('d-m-y')) !!}</td>
                                            <td>{{ $depositTransactions[$i]->description }}</td>
                                            <td class="text-end">{!! bn_number(number_format($depositTransactions[$i]->amount, 2)) !!} টাকা</td>
                                        @else
                                            <td colspan="4" class="text-center text-muted">-</td>
                                        @endif

                                        {{-- Withdraw --}}
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

                                {{-- Summary Rows --}}
                                <tr class="table-info">
                                    <td colspan="3" class="text-end"><strong>মোট প্রদান</strong></td>
                                    <td class="text-end">{!! bn_number(number_format($depositInRange, 2)) !!} টাকা</td>

                                    <td colspan="3" class="text-end"><strong>মোট প্রাপ্তি</strong></td>
                                    <td class="text-end">{!! bn_number(number_format($withdrawInRange, 2)) !!} টাকা</td>
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
                    {{-- <tr>
                        <th colspan="2" class="text-center bg-warning">সারাংশ</th>
                    </tr> --}}
                </thead>
                <tbody>
                    <tr>
                        <td><strong>মোট প্রদান</strong></td>
                        <td>{!! bn_number(number_format($depositInRange, 2)) !!}</td>
                    </tr>
                    <tr>
                        <td><strong>মোট প্রাপ্তি</strong></td>
                        <td>{!! bn_number(number_format($withdrawInRange, 2)) !!}</td>
                    </tr>
                    <tr>
                        <td><strong>স্থিতি</strong></td>
                        <td><strong>
                                {!! bn_number(number_format($currentAmount, 2)) !!}
                                টাকা
                            </strong></td>
                    </tr>
                    
                </tbody>
            </table>
        </div>

    <p style="margin-top:20px;"><strong>বিশেষ দ্রষ্টব্যঃ</strong> লেনদেন এ কোন রকম অসংগতি পাওয়া গেলে সেটা আলোচনা করে ঠিক করে নেয়ার অনুরোধ করা হলো। ধন্যবাদ।</p>

    <br><br>
    <p>
        <div class="w-100">
            <img src="{{ public_path($setting->signature) }}" height="100%" class="signature_img" alt="">
        </div>
        {{ $setting->site_owner }}<br>
        ঠিকানা: {{ $setting->site_address }}<br>
        ইমেইল: {{ $setting->site_email }}<br>
        ওয়েবসাইট : {{ $setting->site_link }}
    </p>
</body>
</html>
