<!DOCTYPE html>
<html lang="bn">
<head>
  <meta charset="UTF-8">
  <title>উপ-বিভাগ আয়ের রিপোর্ট - {{ $subcategory->name ?? 'প্রযোজ্য নয়' }}</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap');
    @import url('https://fonts.googleapis.com/css2?family=Tiro+Bangla:ital@0;1&display=swap');
    body {
      font-family: "Hind Siliguri", sans-serif;
      background-color: #f1f3f6;
    }

    @media print {
      .no-print { display: none !important; }
      body { -webkit-print-color-adjust: exact !important; }
    }

    .report-header {
      
      padding: 20px;
      border-radius: 6px 6px 0 0;
      text-align: center;
      margin-bottom: 15px;
    }

    .report-header h1 {
      margin: 0;
      font-size: 26px;
    }

    .report-header p {
      margin: 0;
      font-size: 15px;
    }

    .report-meta {
      
      background: #fff3cd;
      padding: 15px;
      border-left: 5px solid #ffc107;
    
    }

    .report-meta p {
      margin: 0;
      font-size: 16px;
    }

    .table thead th {
      background-color: #343a40;
      color: white;
      font-size: 15px;
    }

    .subtotal-row {
      background-color: #d4edda;
      font-weight: bold;
    }

    .footer-note {
      font-size: 13px;
      color: #6c757d;
      margin-top: 40px;
      text-align: center;
    }

    .container {
      max-width: 1000px;
    }
    .report-header, .report-footer {
      text-align: center;
      padding: 10px 0;
    }
    .report-header {
      border-bottom: 2px solid #000;
      margin-bottom: 20px;
    }
    .tiro-font {
      font-family: 'Tiro Bangla', serif;
    }
    table tbody tr td{
      background-color: transparent !important;
    }
    table.table tbody tr:nth-of-type(odd) {
      background-color: #d4edda !important;
    }

    table.table tbody tr:nth-of-type(even) {
      background-color: #fff3cd !important;
    }
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

    $subTotal = $incomes->sum('amount');
    $entryCount = $incomes->count();
    $dateFrom = bn_number(optional($incomes->min('date'))->format('d-m-Y'));
    $dateTo = bn_number(optional($incomes->max('date'))->format('d-m-Y'));
  @endphp
<div class="container-fluid my-4">
    <div class="report-header">
    <h2>রাসেল বুক</h2>
    <h4>আয় বিবরণী - ({{ $subcategory->name ?? 'প্রযোজ্য নয়' }})</h4>
    <p>{!! bn_number($startDate) !!} থেকে {!! bn_number($endDate) !!} পর্যন্ত</p>
  </div>
  


  <!-- Report Metadata -->
  

  <!-- Income Table -->
  <div class="card shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-bordered m-0">
          <thead class="table-primary">
            <tr>
              <th colspan="4" class="text-center bg-dark text-white">উপ-বিভাগ: {{ $subcategory->name }}</th>
            <tr>
              <th>তারিখ</th>
              <th>নাম</th>
              <th>বিবরণ</th>
              <th class="text-end">পরিমাণ (৳)</th>
            </tr>
          </thead>
          <tbody>
            @forelse($incomes->sortBy('date') as $income)
              <tr>
                <td>{!! bn_number($income->date) !!}</td>
                <td>{{ $income->name }}</td>
                <td>{{ $income->description }}</td>
                <td class="text-end">{!! bn_number(number_format($income->amount, 2)) !!} টাকা</td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center text-danger">এই উপ-বিভাগে কোন আয় নেই।</td>
              </tr>
            @endforelse
            
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="card-footer text-center bg-success text-white fw-bold p-3 mt-2">
      {{ $subcategory->name ?? 'প্রযোজ্য নয়' }} মোট: {!! bn_number(number_format($subTotal, 2)) !!} টাকা
    </div>

  <div class="d-flex justify-content-center mt-4">
    <table class="table table-bordered w-auto text-center align-middle" style="background: #fff3cd; border-left: 5px solid #ffc107;">
      <tbody>
        <tr>
          <th class="bg-warning-subtle">মোট এন্ট্রি</th>
          <td>{!! bn_number($entryCount) !!} টি</td>
        </tr>
        <tr>
          <th class="bg-warning-subtle">মোট আয়</th>
          <td>{!! bn_number(number_format($subTotal, 2)) !!} টাকা</td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Print Button -->
  <div class="text-center no-print">
    <button onclick="window.print()" class="btn btn-primary mt-4">প্রিন্ট করুন</button>
  </div>

</div>

</body>
</html>
