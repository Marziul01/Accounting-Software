<!DOCTYPE html>
<html lang="bn">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>বিনিয়োগ রিপোর্ট - {{ $subcategory->name }}</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap');
    @import url('https://fonts.googleapis.com/css2?family=Tiro+Bangla:ital@0;1&display=swap');

    body {
      font-family: "Hind Siliguri", sans-serif;
      background-color: #f8f9fa;
    }
    @media print {
      .no-print { display: none !important; }
      body { -webkit-print-color-adjust: exact !important; }
      .img{ width: 15% !important;}
    }
    .report-header, .report-footer {
      text-align: center;
      padding: 10px 0;
    }
    .report-header { border-bottom: 2px solid #000; margin-bottom: 20px; }
    .card-header { background-color: #343a40; color: white; }
    .table-light th { background-color: #f1f1f1 !important; font-size: 12px; }
    .category-total, .grand-total { background-color: #d4edda; font-weight: bold; }
    .print-button { margin: 20px 0; }
    .summary-box { background: #fff3cd; padding: 15px; }
    .tiro-font { font-family: 'Tiro Bangla', serif; }
    table tbody tr td { background-color: transparent !important; font-size: 12px; }
    table.table tbody tr:nth-of-type(odd) { background-color: #d4edda !important; }
    table.table tbody tr:nth-of-type(even) { background-color: #fff3cd !important; }
    .last-row td{
      border-bottom: 2px solid #00a652 !important;
    }
    .report-footer .text-center p{
      margin-bottom: 5px;
      font-weight: 500;
      font-size: 16px !important;
    }
    .img{
      width: 6%;
    }
  </style>
</head>
<body>

@php
    function bn_number($number) {
        $eng = ['0','1','2','3','4','5','6','7','8','9'];
        $bang = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
        return '<span class="tiro-font">'.str_replace($eng, $bang, $number).'</span>';
    }

    $subLossTotal = 0;
   $subGainTotal = 0;
    $subNetTotal = 0;
    
@endphp

<div class="container-fluid my-4">
  <div class="report-header">
    <img src="{{ asset($setting->site_logo) }}"  height="100%" class="img"  alt="">
    <h2>{{ $setting->site_name_bangla }}</h2>
    <h4>{{ $subcategory->name }} এর বিনিয়োগ রিপোর্ট </h4>
    <p>{!! bn_number(\Carbon\Carbon::parse($startDate)->format('d-m-y')) !!} থেকে {!! bn_number(\Carbon\Carbon::parse($endDate)->format('d-m-y')) !!} পর্যন্ত</p>
  </div>

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
              <th class="text-end">শুরুর পরিমাণ / পূর্বের ব্যালেন্স</th>
              <th class="text-end">জমা</th>
              <th class="text-end">উত্তোলন</th>
              <th class="text-end">লাভ/ক্ষতি</th>
            </tr>
          </thead>
          <tbody>
            @foreach($investments->where('investment_sub_category_id', $subcategory->id) as $investment)
              @php
                $totalDeposits = $investment->allTransactions->where('transaction_type', 'Deposit')->sum('amount');
                                $totalWithdrawals = $investment->allTransactions->where('transaction_type', 'Withdraw')->sum('amount');
                                $initialAmount = $investment->amount - $totalDeposits + $totalWithdrawals;

                                // 2. Filtered transactions (between start and end)
                                $depositInRange = $investment->transactions->where('transaction_type', 'Deposit')->sum('amount');
                                $withdrawInRange = $investment->transactions->where('transaction_type', 'Withdraw')->sum('amount');
                                $currentAmount = $depositInRange - $withdrawInRange;

                                

                                if ($startDate <= $investment->date ) {
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
                                
                                @if($previousAmount !== null  && $previousAmount !=  $initialAmount )
                                    <td class="text-end">{!! bn_number(number_format($previousAmount, 2)) !!} টাকা</td>
                                @else
                                    <td class="text-end">{!! bn_number(number_format($initialAmount, 2)) !!} টাকা</td>
                                @endif
                                <td class="text-end">{!! bn_number(number_format($depositInRange, 2)) !!} টাকা</td>
                                <td class="text-end">{!! bn_number(number_format($withdrawInRange, 2)) !!} টাকা</td>
                                <td class="text-end">
                                    @if ($currentAmount < 0)
                                        <span class="badge bg-success">লাভ: {!! bn_number(number_format(abs($currentAmount), 2)) !!} টাকা</span>
                                    @elseif ($currentAmount > 0)
                                        <span class="badge bg-danger">ক্ষতি: {!! bn_number(number_format($currentAmount, 2)) !!} টাকা</span>
                                    @else
                                        <span class="badge bg-secondary">ব্যালেন্স সমান</span>
                                    @endif
                                </td>
                                
                            </tr>
            @endforeach
            <tr class="category-total bg-light">
              <td colspan="6" class="text-end"><strong>মোট ক্ষতি:</strong></td>
              <td class="text-end text-danger"><strong>{!! bn_number(number_format(abs($subLossTotal), 2)) !!} টাকা</strong></td>
            </tr>
            <tr class="category-total bg-light">
              <td colspan="6" class="text-end"><strong>মোট লাভ:</strong></td>
              <td class="text-end text-success"><strong>{!! bn_number(number_format(abs($subGainTotal), 2)) !!} টাকা</strong></td>
            </tr>
            <tr class="category-total bg-warning">
              <td colspan="6" class="text-end"><strong>লাভ / ক্ষতি:</strong></td>
              <td class="text-end">
                <strong>
                  @if ($subNetTotal < 0)
                    <span class="text-success">{!! bn_number(number_format(abs($subNetTotal), 2)) !!} টাকা (লাভ)</span>
                  @elseif ($subNetTotal > 0)
                    <span class="text-danger">{!! bn_number(number_format(abs($subNetTotal), 2)) !!} টাকা (ক্ষতি)</span>
                  @else
                    {!! bn_number('0.00') !!} টাকা (সমান)
                  @endif
                </strong>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="report-footer mt-4">
    <div class="text-center">
      <p class="bangla-text">{{ $setting->site_name_bangla }}</p>

      <p class="bangla-text">
        ঠিকানা: {!! preg_replace_callback('/[০-৯]+/u', function($m) { return '<span class="tiro-font">'.$m[0].'</span>'; }, e($setting->site_address)) !!}
      </p>

      <p class="bangla-text">
        ইমেইল: {!! preg_replace_callback('/[০-৯]+/u', function($m) { return '<span class="tiro-font">'.$m[0].'</span>'; }, e($setting->site_email)) !!}
      </p>

      <p class="bangla-text">
        ওয়েবসাইট : {!! preg_replace_callback('/[০-৯]+/u', function($m) { return '<span class="tiro-font">'.$m[0].'</span>'; }, e($setting->site_website ?? 'www.example.com')) !!}
      </p>
      
    </div>

    @php
        use Illuminate\Support\Carbon;

        $banglaMonths = [
            'January' => 'জানুয়ারি', 'February' => 'ফেব্রুয়ারি', 'March' => 'মার্চ',
            'April' => 'এপ্রিল', 'May' => 'মে', 'June' => 'জুন',
            'July' => 'জুলাই', 'August' => 'আগস্ট', 'September' => 'সেপ্টেম্বর',
            'October' => 'অক্টোবর', 'November' => 'নভেম্বর', 'December' => 'ডিসেম্বর'
        ];

        $banglaMeridiem = ['AM' => 'পূর্বাহ্ণ', 'PM' => 'অপরাহ্ণ'];

        $now = Carbon::now();
        $formatted = $now->format('d F, Y h:i A'); // Example: 31 May, 2025 09:45 PM

        // Translate English month and AM/PM to Bangla
        $formatted = str_replace(array_keys($banglaMonths), array_values($banglaMonths), $formatted);
        $formatted = str_replace(array_keys($banglaMeridiem), array_values($banglaMeridiem), $formatted);

        $banglaDateTime = bn_number($formatted);
    @endphp

    <p class="mt-4">রাসেল বুক দ্বারা প্রস্তুতকৃত - {!! $banglaDateTime !!}  </p>
  </div>

  <div class="text-center no-print">
    <button onclick="window.print()" class="btn btn-primary print-button">প্রিন্ট করুন</button>
  </div>
</div>

</body>
</html>
