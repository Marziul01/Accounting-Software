<!DOCTYPE html>
<html lang="bn">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="1024">
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
            .no-print {
                display: none !important;
            }

            body {
                -webkit-print-color-adjust: exact !important;
            }

            .img {
                width: 15% !important;
            }
            .signature_img{
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
            font-weight: 900;
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
        .report-footer{
            text-align: left !important;
        }
        .signature_text{
            width: fit-content ;
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
    <p>{!! bn_number(\Carbon\Carbon::parse($startDate)->format('d-m-y')) !!} ইং থেকে {!! bn_number(\Carbon\Carbon::parse($endDate)->format('d-m-y')) !!} ইং পর্যন্ত</p>
  </div>

  <div class="card mb-4">
    <div class="card-header text-center">
      <strong>{{ $subcategory->name }}</strong>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-bordered m-0">
          <thead class="table-light">
            <tr>
              <th class="text-center">ক্রমিক নম্বর</th>
              <th class="text-center">তারিখ</th>
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
                $initialAmount = $investment->allTransactions->first()->amount ?? 0;

                            // 2. Filtered transactions (between start and end)
                            $depositInRange = $investment->transactions->where('transaction_type', 'Deposit')->sum('amount');
                            $withdrawInRange = $investment->transactions->where('transaction_type', 'Withdraw')->sum('amount');
                            $investIncome = $investment->investIncome->sum('amount');
                                        $investExpense = $investment->investExpense->sum('amount');

                                        $currentAmount = $depositInRange - $withdrawInRange - $investExpense;

                            

                            if ($investment->allTransactions->isNotEmpty() && $investment->allTransactions->first()->transaction_date >= $startDate) {
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
                <td class="text-center">{!! bn_number($loop->iteration) !!}</td>
                                <td class="text-center">{!! bn_number(\Carbon\Carbon::parse($investment->date)->format('d-m-y')) !!} ইং</td>
                                <td>{{ $investment->name }}</td>
                                
                                
                                    <td class="text-end">{!! bn_number(number_format($previousAmount, 2)) !!} টাকা</td>
                              
                                    
                                <td class="text-end">{!! bn_number(number_format($depositInRange, 2)) !!} টাকা</td>
                                <td class="text-end">{!! bn_number(number_format($withdrawInRange, 2)) !!} টাকা</td>
                                <td class="text-end">
                                    {!! bn_number(number_format($currentAmount, 2)) !!} টাকা
                                </td>
                                
                            </tr>
            @endforeach
            <tr class="category-total bg-light">
              <td colspan="6" class="text-end"><strong>মোট জমা:</strong></td>
              <td class="text-end text-success"><strong>{!! bn_number(number_format($subLossTotal, 2)) !!} টাকা</strong></td>
            </tr>
            <tr class="category-total bg-light">
              <td colspan="6" class="text-end"><strong>মোট উত্তোলন:</strong></td>
              <td class="text-end text-danger"><strong>{!! bn_number(number_format($subGainTotal, 2)) !!} টাকা</strong></td>
            </tr>
            <tr class="category-total bg-warning">
              <td colspan="6" class="text-end"><strong>লাভ / ক্ষতি:</strong></td>
              <td class="text-end">
                <strong>
                  {!! bn_number(number_format($subNetTotal, 2)) !!} টাকা
                </strong>
              </td>
            </tr>
            <tr>
                        <td colspan="6" class="text-end"><strong>মোট {{ $subcategory->name }} বিনিয়োগ হতে আয় :</strong></td>
                        <td class="text-end"><strong>{!! bn_number(number_format($totalIncome, 2)) !!} টাকা</strong></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="text-end"><strong>মোট {{ $subcategory->name }} বিনিয়োগ হতে ক্ষতি :</strong></td>
                        <td class="text-end"><strong>{!! bn_number(number_format($totalExpense, 2)) !!} টাকা</strong></td>
                    </tr>
          </tbody>
        </table>
      </div>
    </div>
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
                $formatted = $now->format('d F, Y') . ' ইং ' . $now->format('h:i A');

                // Translate English month and AM/PM to Bangla
                $formatted = str_replace(array_keys($banglaMonths), array_values($banglaMonths), $formatted);
                $formatted = str_replace(array_keys($banglaMeridiem), array_values($banglaMeridiem), $formatted);

                $banglaDateTime = bn_number($formatted);
            @endphp

            <p class="mt-4 text-center">রাসেল বুক দ্বারা প্রস্তুতকৃত - {!! $banglaDateTime !!} </p>
    </div>

  <div class="text-center no-print">
    <button onclick="window.print()" class="btn btn-success print-button">প্রিন্ট করুন</button>
  </div>
</div>
<div>
        <style>
            .go-top {
                position: fixed;
                bottom: 80px;
                right: 20px;
                background: #333;
                color: #fff;
                border: none;
                border-radius: 50%;
                font-size: 18px;
                cursor: pointer;
                display: none; /* Hidden by default */
                transition: opacity 0.3s ease;
                z-index: 999;
                width: 50px;
                height: 50px;
                padding: 0px;
                align-items: center;
                justify-content: center;
            }
            .go-top.back{
                bottom: 20px;
            }
            .go-top.show {
                display: flex;
                opacity: 0.8;
            }

            .go-top:hover {
                opacity: 1;
            }
            a{
                text-decoration: none;
            }
        </style>
        @if($categorysettings->report_up == 2)
        <button id="goTopBtn" class="go-top">⬆</button>
        @endif

        @if($categorysettings->report_back == 2)
        <a href="{{ url()->previous() }}" id="goBackBtn" class="go-top back">⬅</a>
        @endif
    </div>
    <script>
        const goTopBtn = document.getElementById('goTopBtn');
        const goBackBtn = document.getElementById('goBackBtn');
        // Show button when user scrolls down
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                goTopBtn.classList.add('show');
            } else {
                goTopBtn.classList.remove('show');
            }
        });
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                goBackBtn.classList.add('show');
            } else {
                goBackBtn.classList.remove('show');
            }
        });
        // Smooth scroll to top on click
        goTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    </script>
</body>
</html>
