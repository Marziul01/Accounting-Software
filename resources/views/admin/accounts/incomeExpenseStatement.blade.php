<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="1024">
    <title>আয় ও ব্যয় বিবরণী</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
            line-height: normal !important;
        }

        .no-print .btn {
            min-width: 90px;
            text-align: center;
            font-size: 12px;
            padding: 5px;
        }

        .no-print .form-control {
            font-size: 12px;
        }

        .no-print .form-label {
            font-size: 12px;
            font-weight: 500;
        }

        /* Specific styles for table cells to match the screenshot */
        .report-table th,
        .report-table td {
            border: 1px solid #e0e0e0;
            padding: 7px 10px;
            /* Adjust padding to match visual spacing */
            text-align: left;
            vertical-align: top;
            /* Align text to top for multi-line content */
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
            border-top: 1px solid #e0e0e0;
            /* Ensure top border if it's a new "section" in the table */
            border-bottom: 1px solid #e0e0e0;
            text-align: left;
        }

        .report-header {
            text-align: center;
            padding: 10px 0;
        }

        .report-header img {
            width: 5%;
        }

        .signature_img {
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
            word-break: break-word;
            /* Break long words */
            white-space: normal;
            /* Allow wrapping */
            overflow-wrap: break-word;
            /* Support for older browsers */
        }
    </style>
</head>

<body class="p-8 md:p-12 lg:p-16 text-gray-800">
    <div class="mb-8 flex flex-col md:flex-row items-end justify-center gap-4 no-print">
        <form id="filterForm" method="GET" action="{{ url()->current() }}"
            class="flex flex-col md:flex-row items-end gap-2 no-print">
            {{-- <input type="date" name="startDate" id="startDate" class="form-control no-print" value="{{ request('startDate') }}">
            <span class="mx-1 no-print">-</span>
            <input type="date" name="endDate" id="endDate" class="form-control no-print" value="{{ request('endDate') }}"> --}}

            <div>
                <label for="startDate" class="form-label no-print mb-0">Start Date:</label>
                <input type="date" name="startDate" id="startDate" class="form-control no-print myDate"
                    value="{{ request('startDate') }}">
            </div>

            <span class="mx-1 no-print mb-2">-</span>
            <div>
                <label for="endDate" class="form-label no-print mb-0">End Date:</label>
                <input type="date" name="endDate" id="endDate" class="form-control no-print myDate"
                    value="{{ request('endDate') }}">
            </div>

            <button type="button" class="btn btn-outline-primary no-print" onclick="setCurrentMonth()">Current
                Month</button>
            <button type="button" class="btn btn-outline-secondary no-print" onclick="setCurrentYear()">Current
                Year</button>

            <button type="submit" class="btn btn-success no-print">Filter</button>

            <button type="button" class="btn btn-warning no-print" onclick="resetFilters()">Reset</button>

            <a href="{{ url()->previous() }}" class="btn btn-dark no-print">Go Back</a>
            <button class="btn btn-primary no-print" onclick="window.print()">Print</button>
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
        <h1 class="text-2xl font-bold mb-2">আয় ও ব্যয় বিবরণী</h1>
        <p class="text-xl"> {!! bn_number(\Carbon\Carbon::now()->format('Y-m-d')) !!} ইং তারিখে প্রস্তুতকৃত</p>
        @if ($startDate && $endDate)
            <p class="text-lg"> {!! bn_number($startDate) !!} ইং থেকে {!! bn_number($endDate) !!} ইং পর্যন্ত</p>
        @endif
    </div>

    <!-- Summary Section -->

    <div class="d-flex flex-column">
        <!-- Combined Detailed Section -->
        <div class="mb-12 order-2">
            <h2 class="text-2xl font-semibold text-center mb-6">বিস্তারিত আয় ও ব্যয় বিবরণী</h2>
            <div class="overflow-x-auto">
                <table class="w-full bg-white shadow-lg rounded-lg report-table border-collapse">
                    <thead>
                        <tr>
                            <th class="w-2/6 text-center">বিবরণ</th>
                            <th class="w-1/6 text-center">টাকা</th>
                            <th class="w-2/6 text-center">বিবরণ</th>
                            <th class="w-1/6 text-center">টাকা</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="2" style="padding: 0px !important;">
                                @if ($incomecategories)
                                    <table class="w-full table-fixed">
                                        <thead>
                                        </thead>
                                        <tbody>
                                            @foreach ($incomecategories->where('id', '!=', 13) as $incomecategory)
                                                @php
                                                    // Calculate total amount for this category
                                                    $categoryTotal = $incomeSubCategories
                                                        ->where('income_category_id', $incomecategory->id)
                                                        ->sum(function ($subcategory) {
                                                            return $subcategory->incomes->sum('amount');
                                                        });
                                                @endphp
                                                <tr class="font-semibold">
                                                    <th class="w-2/3 ">
                                                        <ul class="list-disc pl-5">
                                                            <li>{{ $incomecategory->name }}</li>
                                                        </ul>
                                                    </th>
                                                    <th class="w-1/3 text-end">{!! bn_number(number_format($categoryTotal, 2)) !!}</th>
                                                </tr>
                                                {{-- Subcategory rows --}}
                                                @if ($incomeSubCategories)
                                                @foreach ($incomeSubCategories->where('income_category_id', $incomecategory->id) as $incomesubcategory)
                                                    <tr class="p-0">
                                                        <td class="p-0">
                                                            <table class="w-full table-fixed border-0">
                                                                <thead></thead>
                                                                <tbody class="">
                                                                    <tr class="">
                                                                        <td class="w-2/4" style="border-top: none; border-left: 0px; border-bottom: 0px;">{{ $incomesubcategory->name }}</td>
                                                                        <td class="w-2/4 text-end" style="border-top: none; border-left: 0px; border-bottom: 0px;">{!! bn_number(number_format($incomesubcategory->incomes->sum('amount'), 2)) !!}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                            
                                                        </td>
                                                        <td class="text-end"></td>
                                                    </tr>
                                                @endforeach
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </td>
                            
                            <td colspan="2" style="padding: 0px !important;">
                                @if ($expenseCategories)
                                    <table class="w-full">
                                        <thead>
                                        </thead>
                                        <tbody>
                                            @foreach ($expenseCategories->where('id', '!=', 7) as $expenseCategory)
                                                @php
                                                    // Calculate total amount for this category
                                                    $categoryTotal = $expenseSubCategories
                                                        ->where('expense_category_id', $expenseCategory->id)
                                                        ->sum(function ($subcategory) {
                                                            return $subcategory->expenses->sum('amount');
                                                        });
                                                @endphp
                                                <tr class="font-semibold">
                                                    <th class="w-2/3 ">
                                                        <ul class="list-disc pl-5">
                                                            <li>{{ $expenseCategory->name }}</li>
                                                        </ul>
                                                    </th>
                                                    <th class="w-1/3 text-end">{!! bn_number(number_format($categoryTotal, 2)) !!}</th>
                                                </tr>
                                                        {{-- Subcategory rows --}}
                                                @if ($expenseSubCategories)
                                                    @foreach ($expenseSubCategories->where('expense_category_id', $expenseCategory->id) as $expenseSubCategory)
                                                        <tr>
                                                            <td class="p-0">
                                                                <table class="w-full table-fixed border-0">
                                                                    <thead></thead>
                                                                    <tbody class="">
                                                                        <tr class="">
                                                                            <td class="w-2/4" style="border-top: none; border-left: 0px; border-bottom: 0px;">{{ $expenseSubCategory->name }}</td>
                                                                            <td class="w-2/4 text-end" style="border-top: none; border-left: 0px; border-bottom: 0px;">{!! bn_number(number_format($expenseSubCategory->expenses->sum('amount'), 2)) !!}</td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                                
                                                            </td>
                                                            <td class="text-end"></td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </td>
                            
                        </tr>
                        <tr>
                            <td class="font-semibold text-right">মোট আয় =</td>
                            <td class="font-semibold text-end">{!! bn_number(number_format($totalIncomesExcludingCat13, 2)) !!}</td>
                            <td class="font-semibold text-right">মোট ব্যয় =</td>
                            <td class="font-semibold text-end">{!! bn_number(number_format($totalExpensesExcludingCat7, 2)) !!}</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding: 0px !important;">

                                <table class="w-full table-fixed">
                                    <thead>

                                    </thead>
                                    <tbody>
                                        {{-- স্বল্পমেয়াদী বিনিয়োগ --}}
                                        <tr class=" font-semibold">
                                            <th class="w-2/3 ">
                                                <ul class="list-disc pl-5">
                                                    <li>স্বল্পমেয়াদী বিনিয়োগ হতে প্রাপ্ত আয় সমূহ</li>
                                                </ul>
                                            </th>
                                            <th class="w-1/3"></th>
                                        </tr>

                                        @php $shortTermInvestmentIncomestotal = 0; @endphp
                                        @foreach ($investmentIncomes->where('investment_category_id', 4) as $investment)
                                            @if ($investment->investIncome->count() > 0)
                                                @php $shortTermInvestmentIncomestotal += $investment->investIncome->sum('amount'); @endphp
                                                <tr>
                                                    <td class="p-0">
                                                        <table class="w-full table-fixed border-0">
                                                            <thead></thead>
                                                            <tbody class="">
                                                                <tr class="">
                                                                    <td class="w-2/4" style="border-top: none; border-left: 0px; border-bottom: 0px;">{!! bn_number(number_format($loop->iteration)) !!}. {{ $investment->name }}</td>
                                                                    <td class="w-2/4 text-end" style="border-top: none; border-left: 0px; border-bottom: 0px;">{!! bn_number(number_format($investment->investIncome->sum('amount'), 2)) !!}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                    <td class="text-end"></td>
                                                </tr>
                                            @endif
                                        @endforeach

                                        <tr class="font-semibold border-t">
                                            <td>স্বল্পমেয়াদী বিনিয়োগ হতে মোট প্রাপ্ত আয় =</td>
                                            <td class="text-end">{!! bn_number(number_format($shortTermInvestmentIncomestotal, 2)) !!}</td>
                                        </tr>

                                        {{-- দীর্ঘমেয়াদী বিনিয়োগ --}}
                                        <tr class="bg-gray-100 font-semibold">
                                            <th class="w-2/3 ">
                                                <ul class="list-disc pl-5">
                                                    <li>দীর্ঘমেয়াদী বিনিয়োগ হতে প্রাপ্ত আয় সমূহ</li>
                                                </ul>
                                            </th>
                                            <th class="w-1/3"></th>
                                        </tr>

                                        @php
                                            $LongTermInvestmentIncomestotal = 0;
                                            $longIndex = 1;
                                        @endphp
                                        @foreach ($investmentIncomes->where('investment_category_id', 5) as $investment)
                                            @if ($investment->investIncome->count() > 0)
                                                @php $LongTermInvestmentIncomestotal += $investment->investIncome->sum('amount'); @endphp
                                                <tr>
                                                    <td>
                                                        <table class="w-full table-fixed border-0">
                                                            <thead></thead>
                                                            <tbody class="">
                                                                <tr class="">
                                                                    <td class="w-2/4" style="border-top: none; border-left: 0px; border-bottom: 0px;">{!! bn_number(number_format($longIndex++)) !!}. {{ $investment->name }}</td>
                                                                    <td class="w-2/4 text-end" style="border-top: none; border-left: 0px; border-bottom: 0px;">{!! bn_number(number_format($investment->investIncome->sum('amount'), 2)) !!}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                    <td class="text-end"></td>
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
                            
                            <td colspan="2" style="padding: 0px !important;">
                                <table class="w-full table-fixed">
                                    <thead>

                                    </thead>
                                    <tbody>
                                        {{-- স্বল্পমেয়াদী বিনিয়োগ ক্ষতি --}}
                                        <tr class="bg-gray-100 font-semibold">
                                            <th class="w-2/3 ">
                                                <ul class="list-disc pl-5">
                                                    <li>স্বল্পমেয়াদী বিনিয়োগ হতে প্রাপ্ত ক্ষতি সমূহ</li>
                                                </ul>
                                            </th>
                                            <th class="w-1/3"></th>
                                        </tr>

                                        @php $shortTermInvestmentexpensestotal = 0; @endphp
                                        @foreach ($investmentExpenses->where('investment_category_id', 4) as $investment)
                                            @if ($investment->investExpense->count() > 0)
                                                @php $shortTermInvestmentexpensestotal += $investment->investExpense->sum('amount'); @endphp
                                                <tr>
                                                    <td class="p-0">
                                                        <table class="w-full table-fixed border-0">
                                                            <thead></thead>
                                                            <tbody class="">
                                                                <tr class="">
                                                                    <td class="w-2/4" style="border-top: none; border-left: 0px; border-bottom: 0px;">{!! bn_number(number_format($loop->iteration)) !!}. {{ $investment->name }}</td>
                                                                    <td class="w-2/4 text-end" style="border-top: none; border-left: 0px; border-bottom: 0px;">{!! bn_number(number_format($investment->investExpense->sum('amount'), 2)) !!}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                        
                                                    </td>
                                                    <td class="text-end"></td>
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

                                        @php
                                            $LongTermInvestmentexpensestotal = 0;
                                            $longIndex = 1;
                                        @endphp
                                        @foreach ($investmentExpenses->where('investment_category_id', 5) as $investment)
                                            @if ($investment->investExpense->count() > 0)
                                                @php $LongTermInvestmentexpensestotal += $investment->investExpense->sum('amount'); @endphp
                                                <tr>
                                                    <td class="p-0"> 
                                                        <table class="w-full table-fixed border-0">
                                                            <thead></thead>
                                                            <tbody class="">
                                                                <tr class="">
                                                                    <td class="w-2/4" style="border-top: none; border-left: 0px; border-bottom: 0px;">{!! bn_number(number_format($longIndex++)) !!}. {{ $investment->name }}</td>
                                                                    <td class="w-2/4 text-end" style="border-top: none; border-left: 0px; border-bottom: 0px;">{!! bn_number(number_format($investment->investExpense->sum('amount'), 2)) !!}</td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                        
                                                    </td>
                                                    <td class="text-end"></td>
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
                        </tr>
                        <tr>
                            <td class="font-semibold text-right">বিনিয়োগ হতে মোট প্রাপ্ত আয়</td>

                            <td class="font-semibold text-end">{!! bn_number(number_format($totalIncomeCat13, 2)) !!}</td>
                            <td class="font-semibold text-right">বিনিয়োগ হতে মোট ক্ষতি</td>

                            <td class="font-semibold text-end">{!! bn_number(number_format($totalExpensesCat7, 2)) !!}</td>
                        </tr>
                        <tr class="bg-gray-100">
                            <td class="font-semibold text-right">মোট </td>
                            @php
                                $totalIncomes =
                                    $totalIncomesExcludingCat13 +
                                    $shortTermInvestmentIncomestotal +
                                    $LongTermInvestmentIncomestotal;
                            @endphp
                            <td class="font-semibold text-end">{!! bn_number(number_format($totalIncomes, 2)) !!}</td>
                            <td class="font-semibold text-right">মোট</td>
                            @php
                                $totalExpenses =
                                    $totalExpensesExcludingCat7 +
                                    $shortTermInvestmentexpensestotal +
                                    $LongTermInvestmentexpensestotal;
                            @endphp
                            <td class="font-semibold text-end">{!! bn_number(number_format($totalExpenses, 2)) !!}</td>
                        </tr>
                        <tr class="bg-gray-100">
                            <td colspan="2" class="font-semibold">নীট লাভ বা ক্ষতি ( আয় - ব্যয়)</td>
                            <td colspan="2" class="font-semibold text-end"> {!! bn_number(number_format($totalIncomes - $totalExpenses, 2)) !!} </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mb-12 order-1">
            <h2 class="text-2xl font-semibold text-center mb-6">সংক্ষিপ্ত আয় ও ব্যয় বিবরণী</h2>
            <div class="overflow-x-auto">
                <table class="w-full bg-white shadow-lg rounded-lg report-table border-collapse">
                    <thead>
                        <tr>
                            <th class="w-2/3 md:w-3/4 rounded-tl-lg text-center">বিবরণ</th>
                            <th class="w-1/3 md:w-1/4 rounded-tr-lg text-center">টাকা</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>আয় সমূহ </td>
                            <td class="text-end">{!! bn_number(number_format($totalIncomes, 2)) !!}</td>
                        </tr>
                        <tr>
                            <td>ব্যয় সমূহ</td>
                            <td class="text-end">{!! bn_number(number_format($totalExpenses, 2)) !!}</td>
                        </tr>
                        <tr>
                            <td class="rounded-bl-lg">নীট লাভ বা ক্ষতি</td>
                            <td class="rounded-br-lg text-end">{!! bn_number(number_format($totalIncomes - $totalExpenses, 2)) !!}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="report-footer mt-4">
        <div class="text-">
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
        </style>
        @if($categorysettings->report_up == 2)
        <button id="goTopBtn" class="go-top">⬆</button>
        @endif

        @if($categorysettings->report_back == 2)
        <a href="{{ url()->previous() }}" id="goBackBtn" class="go-top back">⬅</a>
        @endif
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
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
        document.querySelectorAll('.myDate').forEach(function(el) {
            // If no value, set placeholder
            if (!el.value) {
                el.setAttribute('placeholder', 'YY-MM-DD');
            }

            flatpickr(el, {
                dateFormat: "Y-m-d",
                defaultDate: el.value || null, // Do not use placeholder as default date
            });
        });
    </script>
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
