<!DOCTYPE html>
<html lang="bn">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="1024">
  <title>{{ $bankAccount->bank_name }} এর রিপোর্ট</title>
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
@endphp

<div class="container-fluid my-4">
  <div class="report-header">
    <img src="{{ asset($setting->site_logo) }}" height="100%" class="img" alt="">
            <h3>{{ $setting->site_name_bangla }}</h2>
    <h5>{{ $bankAccount->bank_name }} এর রিপোর্ট</h4>
    <p class=""> {!! bn_number($startDate ?? 'সর্বপ্রথম') !!} থেকে {!! bn_number($endDate ?? now()->format('Y-m-d')) !!} পর্যন্ত </p>
  </div>

  @if($transactions->count())
    <div class="card mb-4">
      <div class="card-header">
        <strong>লেনদেন বিবরণ</strong>
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
        <th>পরিমাণ</th>
        <th>ক্রমিক</th>
        <th>তারিখ</th>
        <th>বিবরণী</th>
        <th>পরিমাণ</th>
    </tr>
</thead>
<tbody>
    @php
        $deposits = $transactions->where('transaction_type', 'credit')->sortBy('transaction_date')->values();
        $withdraws = $transactions->where('transaction_type', 'debit')->sortBy('transaction_date')->values();

        $maxCount = max($deposits->count(), $withdraws->count());
        $totalDeposit = 0;
        $totalWithdraw = 0;
    @endphp

    @for ($i = 0; $i < $maxCount; $i++)
        <tr>
            {{-- Deposit --}}
            @if (isset($deposits[$i]))
                @php
                    $deposit = $deposits[$i];
                    $totalDeposit += $deposit->amount;
                @endphp
                <td>{!! bn_number($i + 1) !!}</td>
                <td>{!! bn_number(\Carbon\Carbon::parse($deposit->transaction_date)->format('Y-m-d')) !!}</td>
                <td >{{ $deposit->description }} </td>
                <td class="text-end">{!! bn_number(number_format($deposit->amount, 2)) !!} টাকা</td>
            @else
                <td colspan="4"></td>
            @endif

            {{-- Withdraw --}}
            @if (isset($withdraws[$i]))
                @php
                    $withdraw = $withdraws[$i];
                    $totalWithdraw += $withdraw->amount;
                @endphp
                <td>{!! bn_number($i + 1) !!}</td>
                <td>{!! bn_number(\Carbon\Carbon::parse($withdraw->transaction_date)->format('Y-m-d')) !!}</td>
                <td >{{ $withdraw->description }} </td>
                <td class="text-end">{!! bn_number(number_format($withdraw->amount, 2)) !!} টাকা</td>
            @else
                <td colspan="4"></td>
            @endif
        </tr>
    @endfor

    {{-- Totals --}}
    @php
        $currentBalance = $totalDeposit - $totalWithdraw;
    @endphp

    <tr class="table-info">
        <td colspan="4" class="text-end"><strong>মোট জমা : {!! bn_number(number_format($totalDeposit, 2)) !!} টাকা</strong></td>
        <td colspan="4" class="text-end"><strong>মোট উত্তোলন : {!! bn_number(number_format($totalWithdraw, 2)) !!} টাকা</strong></td>
    </tr>
    
</tbody>

          </table>
        </div>
      </div>
    </div>
  @else
    <p class="text-danger text-center">এই সময়ের মধ্যে কোনো লেনদেন পাওয়া যায়নি।</p>
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
          <td><strong>ব্যাংক অ্যাকাউন্ট ধারকের নাম</strong></td>
          <td>{{ $bankAccount->account_holder_name }}</td>
        </tr>
        <tr>
          <td><strong>ব্যাংক অ্যাকাউন্ট ধরণ</strong></td>
          <td>{{ $bankAccount->account_type ?? 'নেই' }}</td>
        </tr>
        <tr class="grand-total">
          <td><strong>বর্তমান ব্যালেন্স</strong></td>
          <td><strong class="tiro">{!! bn_number(number_format($currentBalance, 2)) !!} টাকা</strong></td>
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
                        e($setting->site_link ?? 'www.example.com'),
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
