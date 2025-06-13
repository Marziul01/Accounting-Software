<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>নগদ প্রবাহ বিবরণী</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom font for Inter, if not loaded by Tailwind's default */
        @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Tiro+Bangla:ital@0;1&display=swap');

        body {
            font-family: "Hind Siliguri", sans-serif;
            background-color: #f8f9fa;
        }
        .tiro-font {
            font-family: 'Tiro Bangla', serif;
        }
        /* Specific styles for table cells to match the screenshot */
        .report-table th, .report-table td {
            border: 1px solid #e0e0e0;
            padding: 0.75rem 1rem; /* Adjust padding to match visual spacing */
            text-align: left;
            vertical-align: top; /* Align text to top for multi-line content */
        }
        .report-table th {
            background-color: #f2f2f2;
            font-weight: 600;
        }
        /* Ensure table content aligns to the left as per screenshot */
        .report-table td.text-right-aligned {
            text-align: right;
        }
        .report-table td.text-center-aligned {
            text-align: center;
        }
        /* Specific styling for table headers within the combined table */
        .sub-table-header {
            background-color: #f2f2f2;
            padding: 0.5rem 0.75rem;
            font-weight: 600;
            border-top: 1px solid #e0e0e0; /* Ensure top border if it's a new "section" in the table */
            border-bottom: 1px solid #e0e0e0;
            text-align: left;
        }
    </style>
</head>
<body class="p-8 md:p-12 lg:p-16 text-gray-800">
    @php
        function bn_number($number)
        {
            $eng = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            $bang = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
            return '<span class="tiro-font">' . str_replace($eng, $bang, $number) . '</span>';
        }  
    @endphp
    <!-- Header Section -->
    <div class="text-center mb-10">
        <h1 class="text-3xl font-bold mb-2">নগদ প্রবাহ বিবরণী</h1>
        <p class="text-xl">সাল ***** তারিখে প্রস্তুতকৃত</p>
    </div>

    <!-- Summary Section -->
    <div class="mb-12">
        <h2 class="text-2xl font-semibold text-center mb-6">সংক্ষিপ্ত নগদ প্রবাহ বিবরণী</h2>
        <div class="overflow-x-auto">
            <table class="w-full bg-white shadow-lg rounded-lg report-table border-collapse">
                <thead>
                    <tr>
                        <th class="w-2/3 md:w-3/4 rounded-tl-lg">বিবরণ</th>
                        <th class="w-1/3 md:w-1/4 rounded-tr-lg">টাকার পরিমাণ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>মোট নগদ স্থিতি ( হাতে নগদ + ব্যাংক জমা)</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>হাতে নগদ স্থিতি ( যদি ব্যাংক জমা না থাকে তাহলে সমাপনি স্থিতিই হবে হাতে নগদ)</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="rounded-bl-lg">ব্যাংক জমা স্থিতি ( ব্যাংক জমা - ব্যাংক উত্তোলন)</td>
                        <td class="rounded-br-lg"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Combined Detailed Section -->
    <div class="mb-12">
        <h2 class="text-2xl font-semibold text-center mb-6">বিস্তারিত নগদ প্রবাহ বিবরণী</h2>
        <div class="overflow-x-auto">
            <table class="w-full bg-white shadow-lg rounded-lg report-table border-collapse">
                <thead>
                    <tr>
                        <th class="w-2/6">বিবরণ</th>
                        <th class="w-1/6">ক্রেডিট</th>
                        <th class="w-2/6">বিবরণ</th>
                        <th class="w-1/6">ডেবিট</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Content from the first Detailed Section -->
                    <tr>
                        <td>বিনিয়োগ</td>
                        <td> {!! bn_number($totalInvestDeposit) !!} </td>
                        <td>বিনিয়োগ</td>
                        <td>{!! bn_number($totalInvestWithdraw) !!}</td>
                    </tr>
                    <tr>
                        <td>চলতি সম্পদ প্রাপ্তি</td>
                        <td>{!! bn_number($totalCurrentAssettWithdraw) !!}</td>
                        <td>চলতি সম্পদ প্রদান</td>
                        <td>{!! bn_number($totalCurrentAssetDeposit) !!}</td>
                    </tr>
                    <tr>
                        <td>দায় সমূহ গ্রহন</td>
                        <td>{!! bn_number($totalLiabilitytDeposit) !!}</td>
                        <td>দায় সমূহ পরিশোধ</td>
                        <td>{!! bn_number($totalLiabilityWithdraw) !!}</td>
                    </tr>
                    <tr>
                        <td>ব্যাংক উত্তোলন</td>
                        <td>{!! bn_number($totalBankWithdraw) !!}</td>
                        <td style="padding: 0px !important;">
                            <div class="w-full py-2 px-3">
                                ব্যাংক জমা
                            </div>
                            <div class="w-100 border-t border-gray-300 py-2 px-3">
                                স্থায়ী সম্পদ
                            </div>
                        </td>
                        <td style="padding: 0px !important;">
                            <div class="w-full  py-2 px-3">
                                {!! bn_number($totalBankDeposit) !!}
                            </div>
                            <div class="w-100 border-t border-gray-300 py-2 px-3">
                                {!! bn_number($totalFixedAsset) !!}
                            </div>
                        </td>
                    </tr>
                    
                    <tr>
                        <td colspan="2" class="font-semibold">আয় সমূহ:</td>
                        <td colspan="2" class="font-semibold">ব্যয় সমূহ:</td>
                    </tr>
                    <tr>
                        <td style="padding: 0px !important;">
                            @if($incomecategories)
                            @foreach ($incomecategories->where('id' , '!=', 13) as $incomecategory )
                               <table class="w-full">
                                        <thead>
                                            <tr>
                                                <th class="w-3/4 bg-white"><ul class="list-disc pl-5"><li>{{ $incomecategory->name }}</li></ul></th>
                                                <th class="w-1/4 bg-white font-normal"> </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($incomeSubCategories)
                                            @foreach ($incomeSubCategories->where('income_category_id' , $incomecategory->id ) as $incomesubcategory )
                                                <tr>
                                                    <td>{{ $incomesubcategory->name }}</td>
                                                    <td>{{ $incomesubcategory->incomes->sum('amount') }}</td>
                                                </tr>
                                            @endforeach
                                            @endif
                                        </tbody>
                                </table> 
                            @endforeach
                            @endif
                            
                        </td>
                        <td></td>
                        <td style="padding: 0px !important;">
                            @if($expenseCategories)
                            @foreach ($expenseCategories->where('id' , '!=' , 7) as $expenseCategory )
                            <table class="w-full">
                                        <thead>
                                            <tr>
                                                <th class="w-3/4 bg-white"><ul class="list-disc pl-5"><li>{{ $expenseCategory->name }}</li></ul></th>
                                                <th class="w-1/4 bg-white"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($expenseSubCategories)
                                            @foreach ($expenseSubCategories->where('expense_category_id' , $expenseCategory->id ) as $expenseSubCategory )
                                                <tr>
                                                    <td>{{ $expenseSubCategory->name }}</td>
                                                    <td>{{ $expenseSubCategory->expenses->sum('amount') }}</td>
                                                </tr>
                                            @endforeach
                                            @endif
                                        </tbody>
                            </table>
                            @endforeach
                            @endif
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="font-semibold text-right">মোট আয় =</td>
                        <td>{{ $totalIncomesExcludingCat13 }}</td>
                        <td class="font-semibold text-right">মোট ব্যয়  =</td>
                        <td>{{ $totalExpensesExcludingCat7 }}</td>
                    </tr>

                    <!-- Section for "দীর্ঘমেয়াদী বিনিয়োগ হতে প্রাপ্ত আয় সমূহ" (Investment Income) -->
                    <tr>
                        <td style="padding: 0px !important;">
                            <table class="w-full">
                                        <thead>
                                            <tr>
                                                <th class="w-3/4 bg-white"><ul class="list-disc pl-5"><li>স্বল্পমেয়াদী বিনিয়োগ হতে প্রাপ্ত আয় সমূহ</li></ul></th>
                                                <th class="w-1/4 bg-white"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($incomeSubCategories)
                                            @php
                                                $shortTermInvestmentIncomes = $incomeSubCategories->where('id', 8)->first();
                                                $shortTermInvestmentIncomestotal = $shortTermInvestmentIncomes ? $shortTermInvestmentIncomes->incomes->sum('amount') : 0;
                                            @endphp
                                            @if($shortTermInvestmentIncomes)
                                            @foreach ( $shortTermInvestmentIncomes->incomes as $income )
                                            <tr>
                                                <td>{{ $income->name }}</td>
                                                <td>{{ $income->amount }}</td>
                                            </tr>
                                            @endforeach
                                            @endif
                                            @endif
                                            <tr>
                                                <td>স্বল্পমেয়াদী বিনিয়োগ হতে মোট প্রাপ্ত আয় =</td>
                                                <td>{{ $shortTermInvestmentIncomestotal }}</td>
                                            </tr>
                                        </tbody>
                            </table>
                            <table class="w-full">
                                        <thead>
                                            <tr>
                                                <th class="w-3/4 bg-white"><ul class="list-disc pl-5"><li>দীর্ঘমেয়াদী বিনিয়োগ হতে প্রাপ্ত আয় সমূহ</li></ul></th>
                                                <th class="w-1/4 bg-white"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($incomeSubCategories)
                                            @php
                                                $LongTermInvestmentIncomes = $incomeSubCategories->where('id', 9)->first();
                                                $LongTermInvestmentIncomestotal = $LongTermInvestmentIncomes ? $LongTermInvestmentIncomes->incomes->sum('amount') : 0;
                                            @endphp
                                            @if($LongTermInvestmentIncomes)
                                            @foreach ( $LongTermInvestmentIncomes->incomes as $income )
                                            <tr>
                                                <td>{{ $income->name }}</td>
                                                <td>{{ $income->amount }}</td>
                                            </tr>
                                            @endforeach
                                            @endif
                                            @endif
                                            <tr>
                                                <td>দীর্ঘমেয়াদী বিনিয়োগ হতে মোট প্রাপ্ত আয় =</td>
                                                <td>{{ $LongTermInvestmentIncomestotal }}</td>
                                            </tr>
                                        </tbody>
                            </table>
                        </td>
                        <td></td>
                        <td style="padding: 0px !important;">
                            <table class="w-full">
                                        <thead>
                                            <tr>
                                                <th class="w-3/4 bg-white"><ul class="list-disc pl-5"><li>স্বল্পমেয়াদী বিনিয়োগ হতে প্রাপ্ত ক্ষতি  সমূহ</li></ul></th>
                                                <th class="w-1/4 bg-white"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($expenseSubCategories)
                                            @php
                                                $shortTermInvestmentexpenses = $expenseSubCategories->where('id', 14)->first();
                                                $shortTermInvestmentexpensestotal = $shortTermInvestmentexpenses ? $shortTermInvestmentexpenses->expenses->sum('amount') : 0;
                                            @endphp
                                            @if($shortTermInvestmentexpenses)
                                            @foreach ( $shortTermInvestmentexpenses->expenses as $expense )
                                            <tr>
                                                <td>{{ $expense->name }}</td>
                                                <td>{{ $expense->amount }}</td>
                                            </tr>
                                            @endforeach
                                            @endif
                                            @endif
                                            <tr>
                                                <td>স্বল্পমেয়াদী বিনিয়োগ হতে মোট ক্ষতি  =</td>
                                                <td> {{ $shortTermInvestmentexpensestotal }} </td>
                                            </tr>
                                        </tbody>
                            </table>
                            <table class="w-full">
                                        <thead>
                                            <tr>
                                                <th class="w-3/4 bg-white"><ul class="list-disc pl-5"><li>দীর্ঘমেয়াদী বিনিয়োগ হতে ক্ষতি  সমূহ</li></ul></th>
                                                <th class="w-1/4 bg-white"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($expenseSubCategories)
                                            @php
                                                $LongTermInvestmentexpenses = $expenseSubCategories->where('id', 15)->first();
                                                $LongTermInvestmentexpensestotal = $LongTermInvestmentexpenses ? $LongTermInvestmentexpenses->expenses->sum('amount') : 0;
                                            @endphp
                                            @if($LongTermInvestmentexpenses)
                                            @foreach ( $LongTermInvestmentexpenses->expenses as $expense )
                                            <tr>
                                                <td>{{ $expense->name }}</td>
                                                <td>{{ $expense->amount }}</td>
                                            </tr>
                                            @endforeach
                                            @endif
                                            @endif
                                            <tr>
                                                <td>দীর্ঘমেয়াদী বিনিয়োগ হতে মোট ক্ষতি  =</td>
                                                <td> {{ $LongTermInvestmentexpensestotal }} </td>
                                            </tr>
                                        </tbody>
                            </table>
                        </td>
                        <td></td>
                    </tr>

                    <!-- Section for "দীর্ঘমেয়াদী বিনিয়োগ হতে মোট প্রাপ্ত আয়" and "মোট প্রদান" -->
                    <tr>
                        <td class="font-semibold text-right">মোট প্রাপ্তি</td>
                        <td></td>
                        <td class="font-semibold text-right">মোট প্রদান</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>প্রারম্ভিক স্থিতি</td>
                        <td></td>
                        <td>সমাপনী স্থিতি</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>মোট</td>
                        <td></td>
                        <td>মোট</td>
                        <td></td>
                    </tr>
                    
                </tbody>
            </table>
        </div>
    </div>

    <!-- Print Section -->
    <div class="text-center mt-12 mb-8">
        <p class="text-2xl font-bold">প্রিন্ট</p>
    </div>

</body>
</html>
