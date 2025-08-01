<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="1024">
    <title>নগদ প্রবাহ বিবরণী</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom font for Inter, if not loaded by Tailwind's default */
        @import url('https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@300;400;500;600;700&display=swap');
        @import url('https://fonts.googleapis.com/css2?family=Tiro+Bangla:ital@0;1&display=swap');

        @media print {
            @page {
                size: A4;
            }

            body {
                font-size: 12px !important;
                line-height: 1.5;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .no-print {
                display: none;
            }

            .signature_img {
                width: 15% !important;
            }

            .report-header img {
                width: 15% !important;
            }

            .report-table th {
                background-color: #f2f2f2 !important;
                font-weight: 600;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .bg-gray-100 {
                background-color: #f2f2f2 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }

            

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
            padding: 7px 10px; /* Adjust padding to match visual spacing */
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
        .report-header{
            text-align: center;
            padding: 10px 0;
        }
        .report-header img {
            width: 5%;
        }
        .signature_img{
            width: 7%;
            height: auto;
            padding-bottom: 10px;
            border-bottom: 1px solid #000;
        }
        .bg-gray-100 {
            --tw-bg-opacity: 1;
            background-color: #f2f2f2 !important;
        }
        table td {
            max-width: 100%;
            word-break: break-word;     /* Break long words */
            white-space: normal;        /* Allow wrapping */
            overflow-wrap: break-word;  /* Support for older browsers */
        }
    </style>
</head>
<body class="p-8 md:p-12 lg:p-16 text-gray-800">
    <div class="mb-8 flex flex-col md:flex-row items-center justify-center gap-4 no-print">
        <form id="filterForm" method="GET" action="{{ url()->current() }}" class="flex flex-col md:flex-row items-center gap-2 no-print">
            <input type="date" name="startDate" id="startDate" class="form-control no-print" value="{{ request('startDate') }}">
            <span class="mx-1 no-print">-</span>
            <input type="date" name="endDate" id="endDate" class="form-control no-print" value="{{ request('endDate') }}">

            <button type="button" class="btn btn-outline-primary no-print" onclick="setCurrentMonth()">Current Month</button>
            <button type="button" class="btn btn-outline-secondary no-print" onclick="setCurrentYear()">Current Year</button>
            
            <button type="submit" class="btn btn-success no-print">Filter</button>
            
            <button type="button" class="btn btn-warning no-print" onclick="resetFilters()">Reset</button>

            <a href="{{ route('admin.dashboard') }}" class="btn btn-dark no-print">Go Back</a>
            <button onclick="window.print()" class="btn btn-primary">প্রিন্ট করুন</button>
        </form>
    </div>
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
        <div class="d-flex justify-content-center align-items-center flex-column report-header text-center">
            <img src="{{ asset($setting->site_logo) }}" height="100%" class="img" alt="">
            <h2 class="text-3xl font-bold">{{ $setting->site_name_bangla }}</h2>
        </div>
        <h1 class="text-2xl font-bold mb-2">নগদ প্রবাহ বিবরণী</h1>
        <p class="text-xl"> {!! bn_number(\Carbon\Carbon::now()->format('Y-m-d')) !!} তারিখে প্রস্তুতকৃত</p>
        @if($startDate && $endDate)
            <p class="text-lg"> {!! bn_number($startDate) !!} থেকে {!! bn_number($endDate) !!} পর্যন্ত</p>
        @endif
    </div>

    <!-- Summary Section -->
    
    <div class="d-flex flex-column">
        <!-- Combined Detailed Section -->
        <div class="mb-12 order-2">
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
                            <td>বিনিয়োগ উত্তোলন</td>
                            <td class="text-end">{!! bn_number(number_format($totalInvestWithdraw), 2 ) !!} </td>
                            <td>বিনিয়োগ জমা</td>
                            <td class="text-end"> {!! bn_number(number_format($totalInvestDeposit , 2) ) !!} </td>
                        </tr>
                        <tr>
                            <td>চলতি সম্পদ প্রাপ্তি</td>
                            <td class="text-end">{!! bn_number(number_format($totalCurrentAssettWithdraw , 2)) !!}</td>
                            <td>চলতি সম্পদ প্রদান</td>
                            <td class="text-end">{!! bn_number(number_format($totalCurrentAssetDeposit ,2 )) !!}</td>
                        </tr>
                        <tr>
                            <td>দায় সমূহ গ্রহন</td>
                            <td class="text-end">{!! bn_number(number_format($totalLiabilitytDeposit, 2)) !!}</td>
                            <td>দায় সমূহ পরিশোধ</td>
                            <td class="text-end">{!! bn_number(number_format($totalLiabilityWithdraw,2)) !!}</td>
                        </tr>
                        <tr>
                            <td>ব্যাংক উত্তোলন</td>
                            <td class="text-end">{!! bn_number(number_format($totalBankWithdraw,2)) !!}</td>
                            <td style="padding: 0px !important;">
                                <div class="w-full py-2 px-3">
                                    ব্যাংক জমা
                                </div>
                                <div class="w-100 border-t border-gray-300 py-2 px-3">
                                    স্থায়ী সম্পদ
                                </div>
                            </td>
                            <td style="padding: 0px !important;">
                                <div class="w-full  py-2 px-3 text-end">
                                    {!! bn_number(number_format($totalBankDeposit,2)) !!}
                                </div>
                                <div class="w-100 border-t border-gray-300 py-2 px-3 text-end">
                                    
                                    {!! bn_number(number_format($totalFixedAsset,2)) !!}
                                </div>
                            </td>
                        </tr>
                        
                        <tr>
                            <td colspan="2" class="bg-gray-100 font-semibold">আয় সমূহ:</td>
                            <td colspan="2" class="bg-gray-100 font-semibold">ব্যয় সমূহ:</td>
                        </tr>
                        <tr>
                            <td style="padding: 0px !important;">
                                @if($incomecategories)
                                {{-- @foreach ($incomecategories->where('id' , '!=', 13) as $incomecategory )
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
                                                        <td class="text-end">{!! bn_number(number_format($incomesubcategory->incomes->sum('amount') ,2 ))  !!}</td>
                                                    </tr>
                                                @endforeach
                                                @endif
                                            </tbody>
                                    </table> 
                                @endforeach --}}
                                
                                <table class="w-full table-fixed">
                                    <thead>
                                        {{-- <tr>
                                            <th class="w-3/4 bg-white">আয় ক্যাটেগরি</th>
                                            <th class="w-1/4 bg-white text-end">পরিমাণ</th>
                                        </tr> --}}
                                    </thead>
                                    <tbody>
                                        @foreach ($incomecategories->where('id', '!=', 13) as $incomecategory)
                                            {{-- Category row --}}
                                            <tr class="font-semibold">
                                                <th class="w-2/4 ">
                                                    <ul class="list-disc pl-5">
                                                        <li>{{ $incomecategory->name }}</li>
                                                    </ul>
                                                </th>
                                                <th class="w-2/4 "></th>
                                            </tr>

                                            {{-- Subcategory rows --}}
                                            @if($incomeSubCategories)
                                                @foreach ($incomeSubCategories->where('income_category_id', $incomecategory->id) as $incomesubcategory)
                                                    <tr>
                                                        <td class="pl-8">{{ $incomesubcategory->name }}</td>
                                                        <td class="text-end">{!! bn_number(number_format($incomesubcategory->incomes->sum('amount'), 2)) !!}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        @endforeach
                                    </tbody>
                                </table>
                                @endif
                            </td>
                            <td></td>
                            <td style="padding: 0px !important;">
                                @if($expenseCategories)
                                
                                <table class="w-full">
                                            <thead>
                                                {{-- <tr>
                                                    <th class="w-3/4 bg-white"><ul class="list-disc pl-5"><li>{{ $expenseCategory->name }}</li></ul></th>
                                                    <th class="w-1/4 bg-white"></th>
                                                </tr> --}}
                                            </thead>
                                            <tbody>
                                                @foreach ($expenseCategories->where('id' , '!=' , 7) as $expenseCategory)
                                                    {{-- Category row --}}
                                                    <tr class="font-semibold">
                                                        <th class="w-2/4 ">
                                                            <ul class="list-disc pl-5">
                                                                <li>{{ $expenseCategory->name }}</li>
                                                            </ul>
                                                        </th>
                                                        <th class="w-2/4 "></th>
                                                    </tr>

                                                    {{-- Subcategory rows --}}
                                                    @if($expenseSubCategories)
                                                    @foreach ($expenseSubCategories->where('expense_category_id' , $expenseCategory->id ) as $expenseSubCategory )
                                                        <tr>
                                                            <td>{{ $expenseSubCategory->name }}</td>
                                                            <td class="text-end">{!! bn_number(number_format($expenseSubCategory->expenses->sum('amount') , 2)) !!}</td>
                                                        </tr>
                                                    @endforeach
                                                    @endif
                                                @endforeach
                                                
                                            </tbody>
                                </table>
                                @endif
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="font-semibold text-right">মোট আয় =</td>
                            <td class="font-semibold text-end">{!! bn_number(number_format($totalIncomesExcludingCat13,2)) !!}</td>
                            <td class="font-semibold text-right">মোট ব্যয়  =</td>
                            <td class="font-semibold text-end">{!! bn_number(number_format($totalExpensesExcludingCat7,2)) !!}</td>
                        </tr>

                        <!-- Section for "দীর্ঘমেয়াদী বিনিয়োগ হতে প্রাপ্ত আয় সমূহ" (Investment Income) -->
                        <tr>
                            <td style="padding: 0px !important;">
                                {{-- <table class="w-full">
                                            <thead>
                                                <tr>
                                                    <th class="w-3/4 bg-white"><ul class="list-disc pl-5"><li>স্বল্পমেয়াদী বিনিয়োগ হতে প্রাপ্ত আয় সমূহ</li></ul></th>
                                                    <th class="w-1/4 bg-white"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $shortTermInvestmentIncomestotal = 0;
                                                @endphp
                                                @if($investmentIncomes->where('investment_category_id', 4)->count() > 0)
                                                @foreach ( $investmentIncomes as $investment )

                                                @php
                                                    
                                                    $shortTermInvestmentIncomestotal += $investment->investIncome->sum('amount');
                                                @endphp

                                                @if($investment->investIncome->count() > 0)
                                                <tr>
                                                    <td>{!! bn_number(number_format($loop->iteration)) !!}. {{ $investment->name }}</td>
                                                    <td class="text-end">{!! bn_number(number_format($investment->investIncome->sum('amount'),2)) !!}</td>
                                                </tr>
                                                @endif
                                                @endforeach
                                                @endif
                                                <tr>
                                                    <td>স্বল্পমেয়াদী বিনিয়োগ হতে মোট প্রাপ্ত আয় =</td>
                                                    <td class="text-end">{!! bn_number(number_format($shortTermInvestmentIncomestotal,2)) !!}</td>
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
                                                @php
                                                    $LongTermInvestmentIncomestotal = 0;
                                                @endphp
                                                @if($investmentIncomes->where('investment_category_id', 5)->count() > 0)
                                                @foreach ( $investmentIncomes as $investment )

                                                @php
                                                    
                                                    $LongTermInvestmentIncomestotal += $investment->investIncome->sum('amount');
                                                @endphp

                                                @if($investment->investIncome->count() > 0)
                                                <tr>
                                                    <td>{!! bn_number(number_format($loop->iteration)) !!}. {{ $investment->name }}</td>
                                                    <td class="text-end">{!! bn_number(number_format($investment->investIncome->sum('amount'),2)) !!}</td>
                                                </tr>
                                                @endif
                                                @endforeach
                                                @endif
                                                <tr>
                                                    <td>দীর্ঘমেয়াদী বিনিয়োগ হতে মোট প্রাপ্ত আয় =</td>
                                                    <td class="text-end">{!! bn_number(number_format($LongTermInvestmentIncomestotal,2)) !!}</td>
                                                </tr>
                                            </tbody>
                                </table> --}}
                                <table class="w-full table-fixed">
                                    <thead>
                                        {{-- <tr>
                                            <th class="w-3/4 bg-white">বিনিয়োগের ধরণ</th>
                                            <th class="w-1/4 bg-white text-end">পরিমাণ</th>
                                        </tr> --}}
                                    </thead>
                                    <tbody>
                                        {{-- স্বল্পমেয়াদী বিনিয়োগ --}}
                                        <tr class="bg-gray-100 font-semibold">
                                            <th class="w-2/4 ">
                                                <ul class="list-disc pl-5">
                                                    <li>স্বল্পমেয়াদী বিনিয়োগ হতে প্রাপ্ত আয় সমূহ</li>
                                                </ul>
                                            </th>
                                            <th class="w-2/4"></th>
                                        </tr>

                                        @php $shortTermInvestmentIncomestotal = 0; @endphp
                                        @foreach ($investmentIncomes->where('investment_category_id', 4) as $investment)
                                            @if($investment->investIncome->count() > 0)
                                                @php $shortTermInvestmentIncomestotal += $investment->investIncome->sum('amount'); @endphp
                                                <tr>
                                                    <td>{!! bn_number(number_format($loop->iteration)) !!}. {{ $investment->name }}</td>
                                                    <td class="text-end">{!! bn_number(number_format($investment->investIncome->sum('amount'), 2)) !!}</td>
                                                </tr>
                                            @endif
                                        @endforeach

                                        <tr class="font-semibold border-t">
                                            <td>স্বল্পমেয়াদী বিনিয়োগ হতে মোট প্রাপ্ত আয় =</td>
                                            <td class="text-end">{!! bn_number(number_format($shortTermInvestmentIncomestotal, 2)) !!}</td>
                                        </tr>

                                        {{-- দীর্ঘমেয়াদী বিনিয়োগ --}}
                                        <tr class="bg-gray-100 font-semibold">
                                            <th class="w-2/4 ">
                                                <ul class="list-disc pl-5">
                                                    <li>দীর্ঘমেয়াদী বিনিয়োগ হতে প্রাপ্ত আয় সমূহ</li>
                                                </ul>
                                            </th>
                                            <th class="w-2/4"></th>
                                        </tr>

                                        @php $LongTermInvestmentIncomestotal = 0; $longIndex = 1; @endphp
                                        @foreach ($investmentIncomes->where('investment_category_id', 5) as $investment)
                                            @if($investment->investIncome->count() > 0)
                                                @php $LongTermInvestmentIncomestotal += $investment->investIncome->sum('amount'); @endphp
                                                <tr>
                                                    <td>{!! bn_number(number_format($longIndex++)) !!}. {{ $investment->name }}</td>
                                                    <td class="text-end">{!! bn_number(number_format($investment->investIncome->sum('amount'), 2)) !!}</td>
                                                </tr>
                                            @endif
                                        @endforeach

                                        <tr class="font-semibold border-t">
                                            <td>দীর্ঘমেয়াদী বিনিয়োগ হতে মোট প্রাপ্ত আয় =</td>
                                            <td class="text-end">{!! bn_number(number_format($LongTermInvestmentIncomestotal, 2)) !!}</td>
                                        </tr>
                                    </tbody>
                                </table>

                            </td>
                            <td></td>
                            <td style="padding: 0px !important;">
                                {{-- <table class="w-full">
                                            <thead>
                                                <tr>
                                                    <th class="w-3/4 bg-white"><ul class="list-disc pl-5"><li>স্বল্পমেয়াদী বিনিয়োগ হতে প্রাপ্ত ক্ষতি  সমূহ</li></ul></th>
                                                    <th class="w-1/4 bg-white"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $shortTermInvestmentexpensestotal = 0;
                                                @endphp
                                                @if($investmentExpenses->where('investment_category_id', 4)->count() > 0)
                                                @foreach ( $investmentExpenses as $investment )

                                                @php
                                                    
                                                    $shortTermInvestmentexpensestotal += $investment->investExpense->sum('amount');
                                                @endphp

                                                @if($investment->investExpense->count() > 0)
                                                <tr>
                                                    <td>{!! bn_number(number_format($loop->iteration)) !!}. {{ $investment->name }}</td>
                                                    <td class="text-end">{!! bn_number(number_format($investment->investExpense->sum('amount'),2)) !!}</td>
                                                </tr>
                                                @endif
                                                @endforeach
                                                @endif
                                                <tr>
                                                    <td>স্বল্পমেয়াদী বিনিয়োগ হতে মোট ক্ষতি  =</td>
                                                    <td class="text-end"> {!! bn_number(number_format($shortTermInvestmentexpensestotal,2)) !!} </td>
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
                                                @php
                                                    $LongTermInvestmentexpensestotal = 0;
                                                @endphp
                                                @if($investmentExpenses->where('investment_category_id', 5)->count() > 0)
                                                @foreach ( $investmentExpenses as $investment )

                                                @php
                                                    
                                                    $LongTermInvestmentexpensestotal += $investment->investExpense->sum('amount');
                                                @endphp

                                                @if($investment->investExpense->count() > 0)
                                                <tr>
                                                    <td>{!! bn_number(number_format($loop->iteration)) !!}. {{ $investment->name }}</td>
                                                    <td class="text-end">{!! bn_number(number_format($investment->investExpense->sum('amount'),2)) !!}</td>
                                                </tr>
                                                @endif
                                                @endforeach
                                                @endif
                                                <tr>
                                                    <td>দীর্ঘমেয়াদী বিনিয়োগ হতে মোট ক্ষতি  =</td>
                                                    <td class="text-end"> {!! bn_number(number_format($LongTermInvestmentexpensestotal,2)) !!} </td>
                                                </tr>
                                            </tbody>
                                </table> --}}
                                <table class="w-full table-fixed">
                                    <thead>
                                        {{-- <tr>
                                            <th class="w-3/4 bg-white">বিনিয়োগের ক্ষতি</th>
                                            <th class="w-1/4 bg-white text-end">পরিমাণ</th>
                                        </tr> --}}
                                    </thead>
                                    <tbody>
                                        {{-- স্বল্পমেয়াদী বিনিয়োগ ক্ষতি --}}
                                        <tr class="bg-gray-100 font-semibold">
                                            <th class="w-2/4 ">
                                                <ul class="list-disc pl-5">
                                                    <li>স্বল্পমেয়াদী বিনিয়োগ হতে প্রাপ্ত ক্ষতি সমূহ</li>
                                                </ul>
                                            </th>
                                            <th class="w-2/4"></th>
                                        </tr>

                                        @php $shortTermInvestmentexpensestotal = 0; @endphp
                                        @foreach ($investmentExpenses->where('investment_category_id', 4) as $investment)
                                            @if($investment->investExpense->count() > 0)
                                                @php $shortTermInvestmentexpensestotal += $investment->investExpense->sum('amount'); @endphp
                                                <tr>
                                                    <td>{!! bn_number(number_format($loop->iteration)) !!}. {{ $investment->name }}</td>
                                                    <td class="text-end">{!! bn_number(number_format($investment->investExpense->sum('amount'), 2)) !!}</td>
                                                </tr>
                                            @endif
                                        @endforeach

                                        <tr class="font-semibold border-t">
                                            <td>স্বল্পমেয়াদী বিনিয়োগ হতে মোট ক্ষতি =</td>
                                            <td class="text-end">{!! bn_number(number_format($shortTermInvestmentexpensestotal, 2)) !!}</td>
                                        </tr>

                                        {{-- দীর্ঘমেয়াদী বিনিয়োগ ক্ষতি --}}
                                        <tr class="bg-gray-100 font-semibold">
                                            <th class="w-2/4 ">
                                                <ul class="list-disc pl-5">
                                                    <li>দীর্ঘমেয়াদী বিনিয়োগ হতে ক্ষতি সমূহ</li>
                                                </ul>
                                            </th>
                                            <th class="w-2/4"></th>
                                        </tr>

                                        @php $LongTermInvestmentexpensestotal = 0; $longIndex = 1; @endphp
                                        @foreach ($investmentExpenses->where('investment_category_id', 5) as $investment)
                                            @if($investment->investExpense->count() > 0)
                                                @php $LongTermInvestmentexpensestotal += $investment->investExpense->sum('amount'); @endphp
                                                <tr>
                                                    <td>{!! bn_number(number_format($longIndex++)) !!}. {{ $investment->name }}</td>
                                                    <td class="text-end">{!! bn_number(number_format($investment->investExpense->sum('amount'), 2)) !!}</td>
                                                </tr>
                                            @endif
                                        @endforeach

                                        <tr class="font-semibold border-t">
                                            <td>দীর্ঘমেয়াদী বিনিয়োগ হতে মোট ক্ষতি =</td>
                                            <td class="text-end">{!! bn_number(number_format($LongTermInvestmentexpensestotal, 2)) !!}</td>
                                        </tr>
                                    </tbody>
                                </table>

                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="font-semibold text-right">বিনিয়োগ হতে মোট প্রাপ্ত আয়</td>
                            
                            <td class="font-semibold text-end">{!! bn_number(number_format($totalIncomeCat13,2)) !!}</td>
                            <td class="font-semibold text-right">বিনিয়োগ হতে মোট ক্ষতি</td>
                            
                            <td class="font-semibold text-end">{!! bn_number(number_format($totalExpensesCat7,2)) !!}</td>
                        </tr>

                        <!-- Section for "দীর্ঘমেয়াদী বিনিয়োগ হতে মোট প্রাপ্ত আয়" and "মোট প্রদান" -->
                        <tr class="bg-gray-100">
                            <td class="font-semibold text-right">মোট প্রাপ্তি</td>
                            @php
                                $totalIncomes = $totalIncomesExcludingCat13 + $shortTermInvestmentIncomestotal + $LongTermInvestmentIncomestotal + $totalInvestWithdraw + $totalCurrentAssettWithdraw + $totalLiabilitytDeposit + $totalBankWithdraw;
                            @endphp
                            <td class="font-semibold text-end">{!! bn_number(number_format($totalIncomes,2)) !!}</td>
                            <td class="font-semibold text-right">মোট প্রদান</td>
                            @php
                                $totalExpenses = $totalExpensesExcludingCat7 + $shortTermInvestmentexpensestotal + $LongTermInvestmentexpensestotal + $totalInvestDeposit + $totalCurrentAssetDeposit + $totalLiabilityWithdraw + $totalBankDeposit + $totalFixedAsset;
                            @endphp
                            <td class="font-semibold text-end">{!! bn_number(number_format($totalExpenses,2)) !!}</td>
                        </tr>
                        <tr class="bg-gray-100">
                            <td class="font-semibold">@if($previousPeriod == 'lastMonth' ) গত মাসের  @endif @if($previousPeriod == 'lastYear' ) গত বছরের  @endif প্রারম্ভিক স্থিতি</td>
                            <td class="font-semibold text-end"> 
                            
                                {!! bn_number(number_format( $totalpreviousBalance ,2)) !!}
                            </td>
                            <td class="font-semibold">সমাপনী স্থিতি</td>
                            @php
                                // Calculate the closing balance
                                $closingBalance = ($totalIncomes - $totalExpenses) + $totalpreviousBalance;
                            @endphp
                            <td class="font-semibold text-end">{!! bn_number(number_format( $closingBalance  ,2)) !!}</td>
                        </tr>
                        <tr class="bg-gray-100">
                            <td class="font-semibold">মোট</td>
                            <td class="font-semibold text-end"> {!! bn_number(number_format( ($totalIncomes + $totalpreviousBalance ) ,2)) !!} </td>
                            <td class="font-semibold">মোট</td>
                            <td class="font-semibold text-end"> {!! bn_number(number_format( $closingBalance + $totalExpenses ,2)) !!} </td>
                        </tr>
                        
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mb-12 order-1">
            <h2 class="text-2xl font-semibold text-center mb-6">সংক্ষিপ্ত নগদ প্রবাহ বিবরণী</h2>
            <div class="overflow-x-auto">
                <table class="w-full bg-white shadow-lg rounded-lg report-table border-collapse">
                    <thead>
                        <tr>
                            <th class="w-2/3 md:w-3/4 rounded-tl-lg">বিবরণ</th>
                            <th class="w-1/3 md:w-1/4 rounded-tr-lg">টাকার পরিমাণ</th>
                        </tr>
                    </thead>

                    @php
                        // Correct bank balance logic
                        $bankBalance = $totalBankDeposit - $totalBankWithdraw;

                        
                        // $handcash = $closingBalance - $bankBalance;
                        $handcash = $closingBalance;

                        // Total cash (hand + bank)
                        $totalCash = $handcash + $bankBalance;
                    @endphp

                    <tbody>
                        <tr>
                            <td>মোট নগদ স্থিতি</td>
                            <td class="text-end">{!! bn_number(number_format($totalCash, 2)) !!}</td>
                        </tr>
                        <tr>
                            <td>হাতে নগদ স্থিতি</td>
                            <td class="text-end">{!! bn_number(number_format($handcash, 2)) !!}</td>
                        </tr>
                        <tr>
                            <td class="rounded-bl-lg">ব্যাংক জমা স্থিতি</td>
                            <td class="rounded-br-lg text-end">{!! bn_number(number_format($bankBalance, 2)) !!}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="report-footer mt-4">
            <div class="text-left">
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
                        e($setting->site_website ?? 'www.example.com'),
                    ) !!}
                </p>

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
                $formatted = $now->format('d F, Y h:i A'); // Example: 31 May, 2025 09:45 PM

                // Translate English month and AM/PM to Bangla
                $formatted = str_replace(array_keys($banglaMonths), array_values($banglaMonths), $formatted);
                $formatted = str_replace(array_keys($banglaMeridiem), array_values($banglaMeridiem), $formatted);

                $banglaDateTime = bn_number($formatted);
            @endphp

            <p class="mt-4 text-center">রাসেল বুক দ্বারা প্রস্তুতকৃত - {!! $banglaDateTime !!} </p>
            </div>
        </div>
    

    @php
        $currentMonthStart = \Carbon\Carbon::now()->startOfMonth()->format('Y-m-d');
        $currentMonthEnd = \Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');
        $currentYearStart = \Carbon\Carbon::now()->startOfYear()->format('Y-m-d');
        $currentYearEnd = \Carbon\Carbon::now()->endOfYear()->format('Y-m-d');
    @endphp

    <script>
        function setCurrentMonth() {
            document.getElementById('startDate').value = '{{ $currentMonthStart }}';
            document.getElementById('endDate').value = '{{ $currentMonthEnd }}';
            document.getElementById('filterForm').submit();
        }

        function setCurrentYear() {
            document.getElementById('startDate').value = '{{ $currentYearStart }}';
            document.getElementById('endDate').value = '{{ $currentYearEnd }}';
            document.getElementById('filterForm').submit();
        }

        function resetFilters() {
            document.getElementById('startDate').value = '';
            document.getElementById('endDate').value = '';
            document.getElementById('filterForm').submit();
        }
    </script>


    <!-- Print Section -->
    <div class="text-center no-print">
            
        </div>

</body>
</html>
