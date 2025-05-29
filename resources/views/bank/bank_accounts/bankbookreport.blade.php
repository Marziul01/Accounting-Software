<!DOCTYPE html>
<html lang="bn">
<head>
  <meta charset="UTF-8">
  <title>{{ $bankAccount->bank_name }} ব্যাংক রিপোর্ট</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&family=Tiro+Bangla&display=swap');
    body { font-family: "Hind Siliguri", sans-serif; background-color: #f8f9fa; }
    .tiro { font-family: "Tiro Bangla", serif; }
    @media print { .no-print { display: none !important; } body { -webkit-print-color-adjust: exact !important; } }
    .report-header, .report-footer { text-align: center; padding: 10px 0; }
    .report-header { border-bottom: 2px solid #000; margin-bottom: 20px; }
    .card-header { background-color: #343a40; color: white; }
    .table-light th { background-color: #f1f1f1 !important; font-size: 12px; }
    .category-total, .grand-total, .subcategory-total { background-color: #d4edda; font-weight: bold; }
    .summary-box { background: #fff3cd; padding: 15px; }
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
      return str_replace($eng, $bang, $number);
  }
@endphp

<div class="container-fluid my-4">
  <div class="report-header">
    <h2>রাসেল বুক</h2>
    <h4>{{ $bankAccount->bank_name }} ব্যাংক হিসাব রিপোর্ট</h4>
    <p class="tiro">তারিখ: {{ bn_number($startDate->format('d M, Y')) }} থেকে {{ bn_number($endDate->format('d M, Y')) }}</p>
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
                <th>তারিখ</th>
                <th>ধরন</th>
                <th>বিবরণ</th>
                <th class="text-end">পরিমাণ</th>
              </tr>
            </thead>
            <tbody>
              @foreach($transactions as $tx)
                <tr>
                  <td>{{ bn_number(\Carbon\Carbon::parse($tx->transaction_date)->format('Y-m-d')) }}</td>
                  <td>{{ $tx->transaction_type == 'credit' ? 'জমা' : 'উত্তোলন' }}</td>
                  <td>{{ $tx->description ?? 'N/A' }}</td>
                  <td class="text-end tiro">{{ bn_number(number_format($tx->amount, 2)) }} টাকা</td>
                </tr>
              @endforeach
              <tr class="table-info">
                <td colspan="3" class="text-end"><strong>মোট জমা</strong></td>
                <td class="text-end tiro">{{ bn_number(number_format($totalDeposit, 2)) }} টাকা</td>
              </tr>
              <tr class="table-warning">
                <td colspan="3" class="text-end"><strong>মোট উত্তোলন</strong></td>
                <td class="text-end tiro">{{ bn_number(number_format($totalWithdraw, 2)) }} টাকা</td>
              </tr>
              <tr class="table-primary">
                <td colspan="3" class="text-end"><strong>বর্তমান ব্যালেন্স</strong></td>
                <td class="text-end tiro"><strong>{{ bn_number(number_format($currentBalance, 2)) }} টাকা</strong></td>
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
          <td><strong class="tiro">{{ bn_number(number_format($currentBalance, 2)) }} টাকা</strong></td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="report-footer mt-4">
    <p class="tiro">রাসেল বুক দ্বারা প্রস্তুতকৃত - {{ bn_number(now()->format('d M, Y h:i A')) }}</p>
  </div>

  <div class="text-center no-print">
    <button onclick="window.print()" class="btn btn-primary mt-3">প্রিন্ট করুন</button>
  </div>
</div>
</body>
</html>
