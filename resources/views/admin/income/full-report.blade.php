<!DOCTYPE html>
<html lang="bn">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>আয় বিবরণী</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap');
    @import url('https://fonts.googleapis.com/css2?family=Tiro+Bangla:ital@0;1&display=swap');
    @media print {
      .no-print { display: none !important; }
      .page-break { page-break-after: always; }
      body { -webkit-print-color-adjust: exact !important; }
    }
    body {
      font-family: "Hind Siliguri", sans-serif;
      
    }
    .report-header, .report-footer {
      text-align: center;
      padding: 10px 0;
    }
    .report-header {
      border-bottom: 2px solid #000;
      margin-bottom: 20px;
    }
    .report-footer {
      border-top: 2px solid #000;
      font-size: 14px;
    }
    table th, table td {
      white-space: nowrap;
    }
    .category-total {
      font-weight: bold;
      background-color: #f2f2f2;
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

    $grandTotal = 0;
  @endphp
<div class="container-fluid my-4">
  <div class="report-header">
    <h2>রাসেল বুক</h2>
    <h4>আয় বিবরণী</h4>
    <p>{!! bn_number($startDate) !!} থেকে {!! bn_number($endDate) !!} পর্যন্ত</p>
  </div>

  

  @foreach($incomeCategories as $category)
    @if($category->status == 1)
    @php
      $categoryTotal = 0;
      $categoryIncomes = $incomes->where('income_category_id', $category->id);
      $subcategories = $categoryIncomes->groupBy('income_sub_category_id');
    @endphp

    <div class="card mb-4">
      <div class="card-header bg-dark text-white">
        <h5 class="mb-0">বিভাগ: {{ $category->name }}</h5>
      </div>
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
                  <th colspan="4">
                    উপ-বিভাগ: {{ $subcategory->name ?? 'প্রযোজ্য নয়' }}
                  </th>
                </tr>
                <tr class="table-light">
                  <th>তারিখ</th>
                  <th>নাম</th>
                  <th>বিবরণ</th>
                  <th>পরিমাণ</th>
                </tr>
              </thead>
              <tbody>
                @foreach($subIncomes->sortBy('date') as $income)
                <tr>
                  <td>{!! bn_number($income->date) !!}</td>
                  <td>{!! $income->name !!}</td>
                  <td>{!! $income->description !!}</td>
                  <td>{!! bn_number(number_format($income->amount, 2)) !!} টাকা</td>
                </tr>
                @endforeach
                <tr class="category-total">
                  <td colspan="3" class="text-end">{{ $subcategory->name ?? 'প্রযোজ্য নয়' }} মোট:</td>
                  <td>{!! bn_number(number_format($subTotal, 2)) !!} টাকা</td>
                </tr>
              </tbody>
            </table>
          </div>
        @endforeach

        <div class="text-end p-3 bg-light fw-bold">
          {{ $category->name }} মোট: {!! bn_number(number_format($categoryTotal, 2)) !!} টাকা
        </div>
      </div>
    </div>
    @endif
  @endforeach

  <div class="card mb-4">
    <div class="card-body bg-success text-white">
      <h5 class="mb-0 text-center">সর্বমোট আয়: {!! bn_number(number_format($grandTotal, 2)) !!} টাকা</h5>
    </div>
  </div>

  <div class="report-footer">
    <p>রাসেল বুক দ্বারা প্রস্তুতকৃত - {!! bn_number(now()->format('d M, Y H:i A')) !!}</p>
  </div>

  <div class="text-center mt-3 no-print">
    <button onclick="window.print()" class="btn btn-primary">প্রিন্ট করুন</button>
  </div>
</div>


</body>
</html>