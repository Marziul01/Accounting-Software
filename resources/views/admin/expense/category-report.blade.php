<!DOCTYPE html>
<html lang="bn">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ব্যয় রিপোর্ট - {{ $category->name }}</title>
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

    $categoryTotal = 0;
    $grandTotal = 0;
    $categoryexpenses = $expenses->where('expense_category_id', $category->id);
    $subcategories = $categoryexpenses->groupBy('expense_sub_category_id');
  @endphp

<div class="container-fluid my-4">
  <div class="report-header">
    <h2>রাসেল বুক</h2>
    <h4>ব্যয় বিবরণী - ({{ $category->name }})</h4>
    <p>{!! bn_number($startDate) !!} থেকে {!! bn_number($endDate) !!} পর্যন্ত</p>
  </div>
  @php
  
  $categoryexpenses = $expenses->where('expense_category_id', $category->id);
  $categoryTotal = $categoryexpenses->sum('amount');
  $totalSources = $categoryexpenses->count();
  $averageexpense = $totalSources > 0 ? $categoryTotal / $totalSources : 0;
  $maxexpense = $categoryexpenses->max('amount');
  $minexpense = $categoryexpenses->min('amount');

  $groupedBySubcategory = $categoryexpenses->groupBy('expense_sub_category_id');
@endphp
  

  <!-- Category Header -->
  <div class="card mb-4">

    <!-- Subcategory Tables -->
    <div class="card-body p-0">
      @foreach($subcategories as $subCatId => $subexpenses)
        @php
          $subcategory = \App\Models\expenseSubCategory::find($subCatId);
          $subTotal = $subexpenses->sum('amount');
          $categoryTotal += $subTotal;
          $grandTotal += $subTotal;
        @endphp

        <div class="table-responsive">
          <table class="table table-bordered m-0">
            <thead class="table-primary">
              <tr>
                <th colspan="4">উপ-বিভাগ: {{ $subcategory->name ?? 'প্রযোজ্য নয়' }}</th>
              </tr>
              <tr class="table-light">
                <th>তারিখ</th>
                <th>নাম</th>
                <th>বিবরণ</th>
                <th class="text-end">পরিমাণ</th>
              </tr>
            </thead>
            <tbody>
              @foreach($subexpenses->sortBy('date') as $expense)
                <tr>
                  <td>{!! bn_number($expense->date) !!}</td>
                  <td>{{ $expense->name }}</td>
                  <td>{{ $expense->description }}</td>
                  <td class="text-end">{!! bn_number(number_format($expense->amount, 2)) !!} টাকা</td>
                </tr>
              @endforeach
              <tr class="category-total">
                <td colspan="3" class="text-end">{{ $subcategory->name ?? 'প্রযোজ্য নয়' }} মোট:</td>
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
    <table class="table table-bordered w-auto summary-box mb-0" style="min-width: 350px;">
      <thead>
        <tr>
          <th colspan="2" class="text-center bg-warning">সারাংশ</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><strong>মোট ব্যয়ের উৎস</strong></td>
          <td>{!! bn_number($totalSources) !!}</td>
        </tr>
        <tr>
          <td><strong>প্রতি উৎসে গড় ব্যয়</strong></td>
          <td>{!! bn_number(number_format($averageexpense, 2)) !!} টাকা</td>
        </tr>
        <tr>
          <td><strong>সর্বোচ্চ একক ব্যয়</strong></td>
          <td>{!! bn_number(number_format($maxexpense, 2)) !!} টাকা</td>
        </tr>
        <tr>
          <td><strong>সর্বনিম্ন একক ব্যয়</strong></td>
          <td>{!! bn_number(number_format($minexpense, 2)) !!} টাকা</td>
        </tr>
        <tr>
          <td><strong>সর্বমোট ব্যয়</strong></td>
          <td>{!! bn_number(number_format($categoryTotal, 2)) !!} টাকা</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div class="report-footer mt-4">
    <p>রাসেল বুক দ্বারা প্রস্তুতকৃত - {!! bn_number(now()->format('d M, Y H:i A')) !!}</p>
  </div>
  <!-- Print Button -->
  <div class="text-center no-print">
    <button onclick="window.print()" class="btn btn-primary print-button">প্রিন্ট করুন</button>
  </div>
</div>

</body>
</html>
