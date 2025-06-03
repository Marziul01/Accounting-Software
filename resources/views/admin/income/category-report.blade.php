<!DOCTYPE html>
<html lang="bn">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $category->name }} এর আয় রিপোর্ট </title>
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

    .category-total, .grand-total {
      background-color: #d4edda;
      font-weight: bold;
    }

    .print-button {
      margin: 20px 0;
    }
    .summary-box {
      background: #fff3cd;
      padding: 15px;
      
    }
    .tiro-font {
      font-family: 'Tiro Bangla', serif;
    }
    table tbody tr td{
      background-color: transparent !important;
      font-size: 12px;
      
    }
    table.table tbody tr:nth-of-type(odd) {
      background-color: #d4edda !important;
    }

    table.table tbody tr:nth-of-type(even) {
      background-color: #fff3cd !important;
    }
    .last-row td{
      border-bottom: 2px solid #00a652 !important;
    }
    .report-footer .text-center p{
      margin-bottom: 5px;
      font-weight: 500;
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
        $converted = str_replace($eng, $bang, $number);
        return '<span class="tiro-font">'.$converted.'</span>';
    }

    $categoryTotal = 0;
    $grandTotal = 0;
    $categoryIncomes = $incomes->where('income_category_id', $category->id);
    $subcategories = $categoryIncomes->groupBy('income_sub_category_id');
  @endphp
<div class="container-fluid my-4">
  <div class="report-header">
    <img src="{{ asset($setting->site_logo) }}"  height="100%" class="img"  alt="">
    <h2>{{ $setting->site_name_bangla }}</h2>
    <h4>{{ $category->name }} এর আয় বিবরণী </h4>
    <p>{!! bn_number(\Carbon\Carbon::parse($startDate)->format('d-m-y')) !!} থেকে {!! bn_number(\Carbon\Carbon::parse($endDate)->format('d-m-y')) !!} পর্যন্ত</p>
  </div>
  @php
  
  $categoryIncomes = $incomes->where('income_category_id', $category->id);
  $categoryTotal = $categoryIncomes->sum('amount');
  $totalSources = $categoryIncomes->count();
  $averageIncome = $totalSources > 0 ? $categoryTotal / $totalSources : 0;
  $maxIncome = $categoryIncomes->max('amount');
  $minIncome = $categoryIncomes->min('amount');

  $groupedBySubcategory = $categoryIncomes->groupBy('income_sub_category_id');
@endphp
  

  <!-- Category Header -->
  <div class="card mb-4">
    <div class="card-header bg-dark text-white">
      <h5 class="mb-0">বিভাগ: {{ $category->name }}</h5>
    </div>

    <!-- Subcategory Tables -->
    <div class="card-body p-0">
      @foreach($subcategories as $subCatId => $subIncomes)
        @php
          $subcategory = \App\Models\IncomeSubCategory::find($subCatId);
          $subTotal = $subIncomes->sum('amount');
          $categoryTotal += $subTotal;
          $grandTotal += $subTotal;
        @endphp

        <div class="table-responsive">
          <table class="table table-bordered m-0">
            <thead class="table-primary">
              <tr>
                <th colspan="5">উপ-বিভাগ: {{ $subcategory->name ?? 'প্রযোজ্য নয়' }}</th>
              </tr>
              <tr class="table-light">
                <th>ক্রমিক নম্বর</th>
                <th>তারিখ</th>
                <th>নাম</th>
                <th>বিবরণ</th>
                <th class="text-end">পরিমাণ</th>
              </tr>
            </thead>
            <tbody>
              @foreach($subIncomes->sortBy('date') as $income)
              @php $isLast = $loop->last; @endphp
                <tr class="{{ $isLast ? 'last-row' : '' }}">
                  <td>{!! bn_number($loop->iteration) !!}</td>
                  <td>{!! bn_number(\Carbon\Carbon::parse($income->date)->format('d-m-y')) !!}</td>
                  <td>{{ $income->name }}</td>
                  <td>{{ $income->description }}</td>
                  <td class="text-end">{!! bn_number(number_format($income->amount, 2)) !!} টাকা</td>
                </tr>
              @endforeach
              <tr class="category-total">
                <td colspan="4" class="text-end">{{ $subcategory->name ?? 'প্রযোজ্য নয়' }} মোট:</td>
                <td class="text-end">{!! bn_number(number_format($subTotal, 2)) !!} টাকা</td>
              </tr>
            </tbody>
          </table>
        </div>
      @endforeach
    </div>

    <!-- Category Total -->
    <div class="card-footer text-center bg-success text-white fw-bold">
      {{ $category->name }} মোট: {!! bn_number(number_format($categoryTotal, 2)) !!} টাকা
    </div>
  </div>

  <div class="d-flex justify-content-center mt-4">
    <div class="summary-box p-0" style="width: 400px;">
      <table class="table table-bordered mb-0 text-center align-middle">
        <thead class="table-warning">
          <tr>
            <th colspan="2" class="h5 mb-0">সারাংশ</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><strong>মোট আয়ের উৎস</strong></td>
            <td>{!! bn_number($totalSources) !!}</td>
          </tr>
          <tr>
            <td><strong>প্রতি উৎসে গড় আয়</strong></td>
            <td>{!! bn_number(number_format($averageIncome, 2)) !!} টাকা</td>
          </tr>
          <tr>
            <td><strong>সর্বোচ্চ একক আয়</strong></td>
            <td>{!! bn_number(number_format($maxIncome, 2)) !!} টাকা</td>
          </tr>
          <tr>
            <td><strong>সর্বনিম্ন একক আয়</strong></td>
            <td>{!! bn_number(number_format($minIncome, 2)) !!} টাকা</td>
          </tr>
          <tr>
            <td><strong>সর্বমোট আয়</strong></td>
            <td>{!! bn_number(number_format($categoryTotal, 2)) !!} টাকা</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="report-footer mt-4">
    <div class="text-center">
      <p>{{ $setting->site_name_bangla }}</p>
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

    <p class="mt-4">রাসেল বুক দ্বারা প্রস্তুতকৃত - {!! $banglaDateTime !!}</p>
  </div>
  <!-- Print Button -->
  <div class="text-center no-print">
    <button onclick="window.print()" class="btn btn-primary print-button">প্রিন্ট করুন</button>
  </div>
</div>

</body>
</html>
