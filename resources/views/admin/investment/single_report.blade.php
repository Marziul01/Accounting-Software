<!DOCTYPE html>
<html lang="bn">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $investment->name }} বিনিয়োগ রিপোর্ট</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap');
    @import url('https://fonts.googleapis.com/css2?family=Tiro+Bangla:ital@0;1&display=swap');

    body { font-family: "Hind Siliguri", sans-serif; background-color: #f8f9fa; }
    @media print { .no-print { display: none !important; } body { -webkit-print-color-adjust: exact !important; } }

    .report-header, .report-footer { text-align: center; padding: 10px 0; }
    .report-header { border-bottom: 2px solid #000; margin-bottom: 20px; }
    .card-header { background-color: #343a40; color: white; }
    .table-light th { background-color: #f1f1f1 !important; font-size: 12px; }
    .summary-box { background: #fff3cd; padding: 15px; }
    .tiro-font { font-family: 'Tiro Bangla', serif; }
    table tbody tr td { font-size: 12px; }
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
        return '<span class="tiro-font">'.str_replace($eng, $bang, $number).'</span>';
    }

    $deposits = $investment->transactions->where('transaction_type', 'Deposit')->sum('amount');
    $withdrawals = $investment->transactions->where('transaction_type', 'Withdraw')->sum('amount');
    $initialAmount = $investment->amount - $deposits + $withdrawals;
@endphp

<div class="container my-4">
  <div class="report-header">
    <h2>রাসেল বুক</h2>
    <h4>{{ $investment->name }} বিনিয়োগ রিপোর্ট</h4>
    <p><strong>নাম:</strong> {{ $investment->name }}</p>
    <p><strong>বিনিয়োগ তারিখ:</strong> {!! bn_number($investment->date) !!}</p>
    <p><strong>বিনিয়োগ বিস্তারিত:</strong> {{ $investment->description ?? 'N/A' }}</p>
    <p><strong>ক্যাটেগরি:</strong> {{ $investment->investmentSubCategory->investmentCategory->name ?? 'N/A' }} | <strong>সাবক্যাটেগরি:</strong> {{ $investment->investmentSubCategory->name ?? 'N/A' }}</p>
  </div>

  <div class="card">
    <div class="card-header">লেনদেন বিবরণী</div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-bordered m-0">
          <thead class="table-light">
            <tr>
              <th>তারিখ</th>
              <th>ধরণ</th>
              <th class="text-end">পরিমাণ</th>
            </tr>
          </thead>
          <tbody>
            @forelse($investment->transactions->sortBy('date') as $txn)
              <tr>
                <td>{!! bn_number($txn->date) !!}</td>
                <td>{{ $txn->transaction_type == 'Deposit' ? 'জমা' : 'উত্তোলন' }}</td>
                <td class="text-end">{!! bn_number(number_format($txn->amount, 2)) !!} টাকা</td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="text-center">কোনো লেনদেন পাওয়া যায়নি</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="d-flex justify-content-center mt-4">
    <table class="table table-bordered w-auto summary-box mb-0" style="min-width: 350px;">
      <thead>
        <tr>
          <th colspan="2" class="text-center bg-warning">সারাংশ</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><strong>শুরুর পরিমাণ</strong></td>
          <td>{!! bn_number(number_format(abs($initialAmount), 2)) !!} টাকা</td>
        </tr>
        <tr>
          <td><strong>মোট জমা</strong></td>
          <td>{!! bn_number(number_format(abs($deposits), 2)) !!} টাকা</td>
        </tr>
        <tr>
          <td><strong>মোট উত্তোলন</strong></td>
          <td>{!! bn_number(number_format(abs($withdrawals), 2)) !!} টাকা</td>
        </tr>
        <tr>
          <td><strong>চূড়ান্ত ব্যালেন্স</strong></td>
          <td>{!! bn_number(number_format(abs($investment->amount), 2)) !!} টাকা</td>
        </tr>
        <tr>
          <td><strong>লাভ / ক্ষতি</strong></td>
          <td>
            @if($investment->amount < 0)
              <span class="text-success">লাভ: {!! bn_number(number_format(abs($investment->amount), 2)) !!} টাকা</span>
            @elseif($investment->amount > 0)
              <span class="text-danger">ক্ষতি: {!! bn_number(number_format(abs($investment->amount), 2)) !!} টাকা</span>
            @else
              <span class="text-muted">ব্যালেন্স সমান</span>
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
    <button onclick="window.print()" class="btn btn-primary mt-3">প্রিন্ট করুন</button>
  </div>
</div>

</body>
</html>
