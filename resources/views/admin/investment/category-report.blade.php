<!DOCTYPE html>
<html lang="bn">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>বিনিয়োগ রিপোর্ট - {{ $category->name }}</title>
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
  </style>
</head>
<body>

@php
    function bn_number($number) {
        $eng = ['0','1','2','3','4','5','6','7','8','9'];
        $bang = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
        $converted = str_replace($eng, $bang, $number);
        return '<span class="tiro-font">'.$converted.'</span>';
    }

    $allInvestments = collect(); // Flattened investment list

    foreach ($investments as $group) {
        $allInvestments = $allInvestments->merge($group);
    }

    $categoryTotal = $allInvestments->sum('amount');
    $totalSources = $allInvestments->count();
    $average = $totalSources > 0 ? $categoryTotal / $totalSources : 0;
    $max = $allInvestments->max('amount');
    $min = $allInvestments->min('amount');
    $grandTotal = 0;
    $totalLoss = 0;
    $totalGain = 0;
@endphp


<div class="container-fluid my-4">
  <div class="report-header">
    <h2>রাসেল বুক</h2>
    <h4>বিনিয়োগ রিপোর্ট - ({{ $category->name }})</h4>
    <p> {!! bn_number($startDate ?? 'সর্বপ্রথম') !!} থেকে {!! bn_number($endDate ?? now()->format('Y-m-d')) !!} পর্যন্ত </p>
  </div>

 @foreach($investments as $subCatId => $group)
    @php
        $subcategory = \App\Models\InvestmentSubCategory::find($subCatId);
        $subLossTotal = $group->where('amount', '>', 0)->sum('amount'); // negative
        $subGainTotal = $group->where('amount', '<', 0)->sum('amount'); // positive
        $subNetTotal = $group->sum('amount'); // final amount for the subcategory
        $grandTotal += $subNetTotal;

        $totalLoss += $subLossTotal;
        $totalGain += $subGainTotal;

    @endphp

    <div class="card mb-4">
        <div class="card-header">
            <strong>{{ $subcategory->name ?? 'বিনা সাবক্যাটেগরি' }}</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered m-0">
                    <thead class="table-light">
                        <tr>
                            <th>তারিখ</th>
                            <th>নাম</th>
                            <th class="text-end">শুরুর পরিমাণ</th>
                            <th class="text-end">জমা</th>
                            <th class="text-end">উত্তোলন</th>
                            <th class="text-end">লাভ/ক্ষতি</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($group->sortBy('date') as $investment)
                            @php
                                $currentAmount = $investment->amount;
                                $transactions = $investmentTransactions->where('investment_id', $investment->id);

                                $totalDeposits = $transactions->where('transaction_type', 'Deposit')->sum('amount');
                                $totalWithdrawals = $transactions->where('transaction_type', 'Withdraw')->sum('amount');

                                $initialAmount = $currentAmount - $totalDeposits + $totalWithdrawals;
                            @endphp
                            <tr>
                                <td>{!! bn_number($investment->date) !!}</td>
                                <td>{{ $investment->name }}</td>
                                <td class="text-end">{!! bn_number(number_format($initialAmount, 2)) !!} টাকা</td>
                                <td class="text-end">{!! bn_number(number_format($totalDeposits, 2)) !!} টাকা</td>
                                <td class="text-end">{!! bn_number(number_format($totalWithdrawals, 2)) !!} টাকা</td>
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
                            <td colspan="5" class="text-end"><strong>মোট ক্ষতি:</strong></td>
                            <td class="text-end text-danger"><strong>{!! bn_number(number_format(abs($subLossTotal), 2)) !!} টাকা</strong></td>
                        </tr>
                        <tr class="category-total bg-light">
                            <td colspan="5" class="text-end"><strong>মোট লাভ:</strong></td>
                            <td class="text-end text-success"><strong>{!! bn_number(number_format(abs($subGainTotal), 2)) !!} টাকা</strong></td>
                        </tr>
                        <tr class="category-total bg-warning">
                            <td colspan="5" class="text-end"><strong>{{ $subcategory->name ?? 'বিনা সাবক্যাটেগরি' }} নেট ফলাফল:</strong></td>
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
@endforeach


  <!-- Summary Section -->
  <div class="d-flex justify-content-center mt-4">
    <table class="table table-bordered w-auto summary-box mb-0" style="min-width: 350px;">
      <thead>
        <tr>
          <th colspan="2" class="text-center bg-warning">সারাংশ</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><strong>সর্বমোট ক্ষতি</strong></td>
          <td>{!! bn_number(number_format(abs($totalLoss), 2)) !!} টাকা</td>
        </tr>
        <tr>
          <td><strong>সর্বমোট লাভ</strong></td>
          <td>{!! bn_number(number_format(abs($totalGain), 2)) !!} টাকা</td>
        </tr>
        <tr>
          <td>
            <strong>
                @if ($grandTotal < 0)
                    নেট লাভ
                @elseif ($grandTotal > 0)
                    নেট ক্ষতি
                @else
                    ব্যালেন্স সমান
                @endif
            </strong>
           </td>
          <td>
            @if ($grandTotal < 0)
                    {!! bn_number(number_format(abs($grandTotal), 2)) !!} টাকা
                @elseif ($grandTotal > 0)
                    {!! bn_number(number_format(abs($grandTotal), 2)) !!} টাকা
                @else
                    {!! bn_number('0.00') !!} টাকা
                @endif
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="report-footer mt-4">
    <p>রাসেল বুক দ্বারা প্রস্তুতকৃত - {!! bn_number(now()->format('d M, Y H:i A')) !!}</p>
  </div>

  <div class="text-center no-print">
    <button onclick="window.print()" class="btn btn-primary print-button">প্রিন্ট করুন</button>
  </div>
</div>

</body>
</html>
