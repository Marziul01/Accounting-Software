<!DOCTYPE html>
<html lang="bn">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="1024">
    <title>বিশদ আর্থিক অবস্থা বিবরণী</title>
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
    <div class="mb-8 flex flex-col md:flex-row items-center justify-center gap-4 no-print">
        <form id="filterForm" method="GET" action="{{ url()->current() }}"
            class="flex flex-col md:flex-row items-end gap-2 no-print">
            {{-- <input type="date" name="startDate" id="startDate" class="form-control no-print"
                value="{{ request('startDate') }}">
            <span class="mx-1 no-print">-</span>
            <input type="date" name="endDate" id="endDate" class="form-control no-print"
                value="{{ request('endDate') }}"> --}}

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
        <h1 class="text-2xl font-bold mb-2">বিশদ আর্থিক অবস্থা বিবরণী</h1>
        <p class="text-xl"> {!! bn_number(\Carbon\Carbon::now()->format('Y-m-d')) !!} ইং তারিখে প্রস্তুতকৃত</p>
        @if ($startDate && $endDate)
            <p class="text-lg"> {!! bn_number($startDate) !!} ইং থেকে {!! bn_number($endDate) !!} ইং পর্যন্ত</p>
        @endif
    </div>

    <!-- Summary Section -->

    <div class="d-flex flex-column">
        <div class="mb-12 order-2">

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
                        <!-- Content from the first Detailed Section -->
                        <tr>
                            <td>হাতে নগদ</td>
                            <td class="text-end"> {!! bn_number(number_format($handCash, 2)) !!} </td>
                            <td></td>
                            <td class="text-end"></td>
                        </tr>
                        <tr>
                            <td>ব্যাংক জমা </td>
                            <td class="text-end">{!! bn_number(number_format($totalBankDeposit - $totalBankWithdraw, 2)) !!}</td>
                            <td></td>
                            <td class="text-end"></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding: 0px !important;">
                                <table class="w-full table-fixed">
                                    <thead></thead>
                                    <tbody>
                                        <tr class="font-semibold">
                                            <th class="w-2/3 text-center">চলতি সম্পদ সমুহঃ</th>
                                            <th class="w-1/3"></th>
                                        </tr>

                                        @if ($assetSubCategories)
                                            @foreach ($assetSubCategories->where('asset_category_id', 4) as $assetSubCategory)
                                                {{-- Calculate subcategory total --}}
                                                @php
                                                    $subcategoryTotal = 0;

                                                    if ($allCurrentAssets) {
                                                        foreach ($allCurrentAssets->where('subcategory_id', $assetSubCategory->id) as $asset) {
                                                            $deposit = $asset->transactions->where('transaction_type', 'Deposit')->sum('amount');
                                                            $withdraw = $asset->transactions->where('transaction_type', 'Withdraw')->sum('amount');
                                                            $currentAmount = $deposit - $withdraw;

                                                            if (!(empty($startDate) && empty($endDate) && $currentAmount == 0)) {
                                                                $subcategoryTotal += $currentAmount;
                                                            }
                                                        }
                                                    }
                                                @endphp

                                                {{-- Subcategory row with total --}}
                                                <tr class="font-semibold">
                                                    <th class="w-2/3">
                                                        <ul class="list-disc pl-5">
                                                            <li>{{ $assetSubCategory->name }}</li>
                                                        </ul>
                                                    </th>
                                                    <th class="w-1/3 text-end">
                                                        {!! bn_number(number_format($subcategoryTotal, 2)) !!}
                                                    </th>
                                                </tr>

                                                {{-- Asset rows --}}
                                                @if ($allCurrentAssets)
                                                    @foreach ($allCurrentAssets->where('subcategory_id', $assetSubCategory->id) as $asset)
                                                        @php
                                                            $showRow = true;

                                                            $deposit = $asset->transactions->where('transaction_type', 'Deposit')->sum('amount');
                                                            $withdraw = $asset->transactions->where('transaction_type', 'Withdraw')->sum('amount');
                                                            $currentAmount = $deposit - $withdraw;

                                                            if (empty($startDate) && empty($endDate) && $currentAmount == 0) {
                                                                $showRow = false;
                                                            }
                                                        @endphp

                                                        @if ($showRow)
                                                            <tr>
                                                                <td class="p-0">
                                                                    <table class="w-full table-fixed">
                                                                        <tr>
                                                                            <td class="w-1/2" style="border-top: 0px; border-bottom: 0px; border-left: 0px;">{{ $asset->name }}</td>
                                                                            <td class="w-1/2 text-end" style="border-top: 0px; border-bottom: 0px; border-left: 0px;">{!! bn_number(number_format($currentAmount, 2)) !!}</td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                                <td></td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </td>
                            
                            <td colspan="2" style="padding: 0px !important;">
                                <table class="w-full">
                                    <thead></thead>
                                    <tbody>
                                        <tr class="font-semibold">
                                            <th class="w-2/3 text-center">
                                                স্বল্পমেয়াদী দায় সমুহঃ
                                            </th>
                                            <th class="w-1/3"></th>
                                        </tr>

                                        @if ($liabilitySubCategories)
                                            @foreach ($liabilitySubCategories->where('liability_category_id', 3) as $liabilitySubCategory)
                                                {{-- Calculate subcategory total --}}
                                                @php
                                                    $subcategoryTotal = 0;

                                                    if ($allShortLiabilities) {
                                                        foreach ($allShortLiabilities->where('subcategory_id', $liabilitySubCategory->id) as $liability) {
                                                            $deposit = $liability->transactions->where('transaction_type', 'Deposit')->sum('amount');
                                                            $withdraw = $liability->transactions->where('transaction_type', 'Withdraw')->sum('amount');
                                                            $currentAmount = $deposit - $withdraw;

                                                            if (!(empty($startDate) && empty($endDate) && $currentAmount == 0)) {
                                                                $subcategoryTotal += $currentAmount;
                                                            }
                                                        }
                                                    }
                                                @endphp

                                                {{-- Subcategory row with total --}}
                                                <tr class="font-semibold">
                                                    <th class="w-2/3">
                                                        <ul class="list-disc pl-5">
                                                            <li>{{ $liabilitySubCategory->name }}</li>
                                                        </ul>
                                                    </th>
                                                    <th class="w-1/3 text-end">
                                                        {!! bn_number(number_format($subcategoryTotal, 2)) !!}
                                                    </th>
                                                </tr>

                                                {{-- Individual liabilities rows --}}
                                                @if ($allShortLiabilities)
                                                    @foreach ($allShortLiabilities->where('subcategory_id', $liabilitySubCategory->id) as $liability)
                                                        @php
                                                            $showRow = true;

                                                            $deposit = $liability->transactions->where('transaction_type', 'Deposit')->sum('amount');
                                                            $withdraw = $liability->transactions->where('transaction_type', 'Withdraw')->sum('amount');
                                                            $currentAmount = $deposit - $withdraw;

                                                            if (empty($startDate) && empty($endDate) && $currentAmount == 0) {
                                                                $showRow = false;
                                                            }
                                                        @endphp

                                                        @if ($showRow)
                                                            <tr>
                                                                <td class="p-0">
                                                                    <table class="w-full table-fixed">
                                                                        <tr>
                                                                            <td class="w-1/2" style="border-top: 0px; border-bottom: 0px; border-left: 0px;">{{ $liability->name }}</td>
                                                                            <td class="w-1/2 text-end" style="border-top: 0px; border-bottom: 0px; border-left: 0px;">{!! bn_number(number_format($currentAmount, 2)) !!}</td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                                
                                                                <td class="text-end"></td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </td>
                            
                        </tr>
                        <tr>
                            <td class="font-semibold text-right">মোট চলতি সম্পদ =</td>
                            <td class="font-semibold text-end">{!! bn_number(number_format($totalCurrentAssetDeposit - $totalCurrentAssetWithdraw, 2)) !!}</td>
                            <td class="font-semibold text-right">মোট স্বল্পমেয়াদী দায় =</td>
                            <td class="font-semibold text-end">{!! bn_number(number_format($totalShortLiabilityDeposit - $totalShortLiabilityWithdraw, 2)) !!}</td>
                        </tr>

                        <!-- Section for "দীর্ঘমেয়াদী বিনিয়োগ হতে প্রাপ্ত আয় সমূহ" (Investment Income) -->
                        <tr>
                            <td colspan="2" style="padding: 0px !important;">
                                <table class="w-full table-fixed">
                                    <thead></thead>
                                    <tbody>
                                        <tr class="font-semibold">
                                            <th class="w-2/3 text-center">
                                                স্থায়ী সম্পদ সমুহঃ
                                            </th>
                                            <th class="w-1/3"></th>
                                        </tr>

                                        @if ($assetSubCategories)
                                            @foreach ($assetSubCategories->where('asset_category_id', 5) as $assetSubCategory)
                                                {{-- Calculate subcategory total --}}
                                                @php
                                                    $subcategoryTotal = 0;

                                                    if ($allFixedAssets) {
                                                        foreach ($allFixedAssets->where('subcategory_id', $assetSubCategory->id) as $asset) {
                                                            $deposit = $asset->transactions->where('transaction_type', 'Deposit')->sum('amount');
                                                            $withdraw = $asset->transactions->where('transaction_type', 'Withdraw')->sum('amount');
                                                            $currentAmount = $deposit - $withdraw;

                                                            if (!(empty($startDate) && empty($endDate) && $currentAmount == 0)) {
                                                                $subcategoryTotal += $currentAmount;
                                                            }
                                                        }
                                                    }
                                                @endphp

                                                {{-- Subcategory row with total --}}
                                                <tr class="font-semibold">
                                                    <th class="w-2/3">
                                                        <ul class="list-disc pl-5">
                                                            <li>{{ $assetSubCategory->name }}</li>
                                                        </ul>
                                                    </th>
                                                    <th class="w-1/3 text-end">
                                                        {!! bn_number(number_format($subcategoryTotal, 2)) !!}
                                                    </th>
                                                </tr>

                                                {{-- Individual asset rows --}}
                                                @if ($allFixedAssets)
                                                    @foreach ($allFixedAssets->where('subcategory_id', $assetSubCategory->id) as $asset)
                                                        @php
                                                            $showRow = true;

                                                            $deposit = $asset->transactions->where('transaction_type', 'Deposit')->sum('amount');
                                                            $withdraw = $asset->transactions->where('transaction_type', 'Withdraw')->sum('amount');
                                                            $currentAmount = $deposit - $withdraw;

                                                            if (empty($startDate) && empty($endDate) && $currentAmount == 0) {
                                                                $showRow = false;
                                                            }
                                                        @endphp

                                                        @if ($showRow)
                                                            <tr>
                                                                <td class="p-0">
                                                                    <table class="w-full table-fixed">
                                                                        <tr>
                                                                            <td class="w-1/2" style="border-top: 0px; border-bottom: 0px; border-left: 0px;">{{ $asset->name }}</td>
                                                                            <td class="w-1/2 text-end" style="border-top: 0px; border-bottom: 0px; border-left: 0px;">{!! bn_number(number_format($currentAmount, 2)) !!}</td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                                
                                                                <td class="text-end"></td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </td>
                            
                            <td colspan="2" style="padding: 0px !important;">
                                <table class="w-full table-fixed">
                                    <thead></thead>
                                    <tbody>
                                        <tr class="font-semibold">
                                            <th class="w-2/3 text-center">
                                                দীর্ঘমেয়াদী দায় সমুহঃ
                                            </th>
                                            <th class="w-1/3"></th>
                                        </tr>

                                        @if ($liabilitySubCategories)
                                            @foreach ($liabilitySubCategories->where('liability_category_id', 4) as $liabilitySubCategory)
                                                {{-- Calculate subcategory total --}}
                                                @php
                                                    $subcategoryTotal = 0;

                                                    if ($allLongLiabilities) {
                                                        foreach ($allLongLiabilities->where('subcategory_id', $liabilitySubCategory->id) as $liability) {
                                                            $deposit = $liability->transactions->where('transaction_type', 'Deposit')->sum('amount');
                                                            $withdraw = $liability->transactions->where('transaction_type', 'Withdraw')->sum('amount');
                                                            $currentAmount = $deposit - $withdraw;

                                                            if (!(empty($startDate) && empty($endDate) && $currentAmount == 0)) {
                                                                $subcategoryTotal += $currentAmount;
                                                            }
                                                        }
                                                    }
                                                @endphp

                                                {{-- Subcategory row with total --}}
                                                <tr class="font-semibold">
                                                    <th class="w-2/3">
                                                        <ul class="list-disc pl-5">
                                                            <li>{{ $liabilitySubCategory->name }}</li>
                                                        </ul>
                                                    </th>
                                                    <th class="w-1/3 text-end">
                                                        {!! bn_number(number_format($subcategoryTotal, 2)) !!}
                                                    </th>
                                                </tr>

                                                {{-- Individual liability rows --}}
                                                @if ($allLongLiabilities)
                                                    @foreach ($allLongLiabilities->where('subcategory_id', $liabilitySubCategory->id) as $liability)
                                                        @php
                                                            $showRow = true;

                                                            $deposit = $liability->transactions->where('transaction_type', 'Deposit')->sum('amount');
                                                            $withdraw = $liability->transactions->where('transaction_type', 'Withdraw')->sum('amount');
                                                            $currentAmount = $deposit - $withdraw;

                                                            if (empty($startDate) && empty($endDate) && $currentAmount == 0) {
                                                                $showRow = false;
                                                            }
                                                        @endphp

                                                        @if ($showRow)
                                                            <tr>
                                                                <td class="p-0">
                                                                    <table class="w-full table-fixed">
                                                                        <tr>
                                                                            <td class="w-1/2" style="border-top: 0px; border-bottom: 0px; border-left: 0px;">{{ $liability->name }}</td>
                                                                            <td class="w-1/2 text-end" style="border-top: 0px; border-bottom: 0px; border-left: 0px;">{!! bn_number(number_format($currentAmount, 2)) !!}</td>
                                                                        </tr>
                                                                    </table>
                                                                </td>
                                                                
                                                                <td class="text-end"></td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>


                            </td>
                            
                        </tr>
                        <tr>
                            <td class="font-semibold text-right">মোট স্থায়ী সম্পদঃ</td>

                            <td class="font-semibold text-end">{!! bn_number(number_format($totalFixedAsset, 2)) !!}</td>
                            <td class="font-semibold text-right">মোট দীর্ঘমেয়াদী দায়ঃ</td>

                            <td class="font-semibold text-end">{!! bn_number(number_format($totalLongLiabilityDeposit - $totalLongLiabilityWithdraw, 2)) !!}</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding: 0px !important;">
                                <table class="w-full table-fixed">
                                    <thead></thead>
                                    <tbody>
                                        <tr class="font-semibold">
                                            <th class="w-2/3 text-center">
                                                স্বল্পমেয়াদী বিনিয়োগঃ
                                            </th>
                                            <th class="w-1/3 text-end"></th>
                                        </tr>
                                        @php
                                            $shortTermInvestTotal = 0;
                                        @endphp
                                        @if ($investmentSubCategories)
                                            @foreach ($investmentSubCategories->where('investment_category_id', 4) as $investmentSubCategory)
                                                @php
                                                    $subshortTermInvestTotal = 0;
                                                @endphp
                                                @if ($allInvestments)
                                                    @foreach ($allInvestments->where('investment_sub_category_id', $investmentSubCategory->id) as $investment)
                                                        @php
                                                            $deposit = $investment->transactions->where('transaction_type', 'Deposit')->sum('amount');
                                                            $withdraw = $investment->transactions->where('transaction_type', 'Withdraw')->sum('amount');
                                                            $expense = $investment->investExpense->sum('amount');

                                                            $investmentAmount = $deposit - $withdraw - $expense;
                                                            $subshortTermInvestTotal += $investmentAmount;
                                                        @endphp
                                                    @endforeach
                                                @endif

                                                @php
                                                    $shortTermInvestTotal += $subshortTermInvestTotal;
                                                @endphp

                                                {{-- Subcategory Row (with total) --}}
                                                <tr class="font-semibold">
                                                    <th class="w-2/3">
                                                        <ul class="list-disc pl-5 flex items-center gap-2">
                                                            <li>{{ $investmentSubCategory->name }}</li>
                                                        </ul>
                                                    </th>
                                                    <th class="w-1/3 text-end">
                                                        {!! bn_number(number_format($subshortTermInvestTotal, 2)) !!}
                                                    </th>
                                                </tr>

                                                {{-- Subcategory investments --}}
                                                @if ($allInvestments)
                                                    @foreach ($allInvestments->where('investment_sub_category_id', $investmentSubCategory->id) as $investment)
                                                        @php
                                                            $deposit = $investment->transactions->where('transaction_type', 'Deposit')->sum('amount');
                                                            $withdraw = $investment->transactions->where('transaction_type', 'Withdraw')->sum('amount');
                                                            $expense = $investment->investExpense->sum('amount');

                                                            $investmentAmount = $deposit - $withdraw - $expense;
                                                        @endphp
                                                        <tr>
                                                            <td class="p-0">
                                                                <table class="w-full table-fixed">
                                                                    <tr>
                                                                        <td class="w-1/2" style="border-top: 0px; border-bottom: 0px; border-left: 0px;">{{ $investment->name }}</td>
                                                                        <td class="w-1/2 text-end" style="border-top: 0px; border-bottom: 0px; border-left: 0px;">{!! bn_number(number_format($investmentAmount, 2)) !!}</td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                            <td class="text-end"></td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </td>
                            <td colspan="2"></td>
                        </tr>
                        <tr>
                            <td class="font-semibold text-right">মোট স্বল্পমেয়াদী বিনিয়োগ</td>
                            <td class="font-semibold text-end">{!! bn_number(number_format($shortTermInvestTotal, 2)) !!}</td>
                            <td class="font-semibold text-right">মূলধন বা মালিকানা সত্ত্ব <span
                                    class="font-xs text-warning"> ( নীট লাভ বা ক্ষতি এর সাথে যুক্ত )</span> </td>
                            @php
                                $ttoalassets =
                                    ( $totalCurrentAssetDeposit -
                                    $totalCurrentAssetWithdraw ) +
                                    $totalFixedAsset +
                                    $totalInvestAmount +
                                    $handCash +
                                    ($totalBankDeposit - $totalBankWithdraw);
                            @endphp
                            @php
                                $totalLiabilities =
                                    ( $totalShortLiabilityDeposit -
                                    $totalShortLiabilityWithdraw ) +
                                    ($totalLongLiabilityDeposit - $totalLongLiabilityWithdraw);
                            @endphp
                            @php
                                $totalEquity = $ttoalassets - $totalLiabilities;
                            @endphp
                            <td class="font-semibold text-end">
                                {!! bn_number(number_format($totalEquity, 2)) !!}
                            </td>

                        </tr>
                        <tr>
                            <td colspan="2" style="padding: 0px !important;">
                                <table class="w-full table-fixed">
                                    <thead></thead>
                                    <tbody>
                                        <tr class="font-semibold">
                                            <th class="w-2/3 text-center">
                                                দীর্ঘমেয়াদী বিনিয়োগঃ
                                            </th>
                                            <th class="w-1/3 text-end"></th>
                                        </tr>
                                        @php
                                            $longTermInvestTotal = 0;
                                        @endphp
                                        @if ($investmentSubCategories)
                                            @foreach ($investmentSubCategories->where('investment_category_id', 5) as $investmentSubCategory)
                                                @php
                                                    $subLongTermInvestTotal = 0;
                                                @endphp

                                                {{-- Calculate subcategory total --}}
                                                @if ($allInvestments)
                                                    @foreach ($allInvestments->where('investment_sub_category_id', $investmentSubCategory->id) as $investment)
                                                        @php
                                                            $deposit = $investment->transactions->where('transaction_type', 'Deposit')->sum('amount');
                                                            $withdraw = $investment->transactions->where('transaction_type', 'Withdraw')->sum('amount');
                                                            $expense = $investment->investExpense->sum('amount');

                                                            $investmentAmount = $deposit - $withdraw - $expense;
                                                            $subLongTermInvestTotal += $investmentAmount;
                                                        @endphp
                                                    @endforeach
                                                @endif

                                                @php
                                                    $longTermInvestTotal += $subLongTermInvestTotal;
                                                @endphp

                                                {{-- Subcategory Row (with total) --}}
                                                <tr class="font-semibold">
                                                    <th class="w-2/3">
                                                        <ul class="list-disc pl-5 flex items-center gap-2">
                                                            <li>{{ $investmentSubCategory->name }}</li>
                                                        </ul>
                                                    </th>
                                                    <th class="w-1/3 text-end">
                                                        {!! bn_number(number_format($subLongTermInvestTotal, 2)) !!}
                                                    </th>
                                                </tr>

                                                {{-- Subcategory investments --}}
                                                @if ($allInvestments)
                                                    @foreach ($allInvestments->where('investment_sub_category_id', $investmentSubCategory->id) as $investment)
                                                        @php
                                                            $deposit = $investment->transactions->where('transaction_type', 'Deposit')->sum('amount');
                                                            $withdraw = $investment->transactions->where('transaction_type', 'Withdraw')->sum('amount');
                                                            $expense = $investment->investExpense->sum('amount');

                                                            $investmentAmount = $deposit - $withdraw - $expense;
                                                        @endphp
                                                        <tr>
                                                            <td class="p-0">
                                                                <table class="w-full table-fixed">
                                                                    <tr>
                                                                        <td class="w-1/2" style="border-top: 0px; border-bottom: 0px; border-left: 0px;">{{ $investment->name }}</td>
                                                                        <td class="w-1/2 text-end" style="border-top: 0px; border-bottom: 0px; border-left: 0px;">{!! bn_number(number_format($investmentAmount, 2)) !!}</td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                            <td class="text-end"></td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </td>
                           
                            <td colspan="2">



                            </td>
                            
                        </tr>
                        <tr>
                            <td class="font-semibold text-right">মোট দীর্ঘমেয়াদী বিনিয়োগ</td>

                            <td class="font-semibold text-end">{!! bn_number(number_format($longTermInvestTotal, 2)) !!}</td>
                            <td class="font-semibold text-right">
                                
                            </td>
                            
                            <td class="font-semibold text-end">
                               
                            </td>
                        </tr>

                       
                        <tr class="bg-gray-100">
                            <td class="font-semibold text-right">মোট বিনিয়োগ = </td>

                            <td class="font-semibold text-end">{!! bn_number(number_format($totalInvestAmount, 2)) !!}</td>
                            <td class="font-semibold text-right"></td>

                            <td class="font-semibold text-end"></td>
                        </tr>

                        <tr class="bg-gray-100">
                            <td class="font-semibold">মোট</td>
                            
                            <td class="font-semibold text-end">{!! bn_number(number_format($ttoalassets, 2)) !!} </td>
                            <td class="font-semibold">মোট</td>
                            @php
                                $totalLiabilitiesAndEquity = $totalLiabilities + $totalEquity;
                            @endphp
                            <td class="font-semibold text-end"> {!! bn_number(number_format($totalLiabilitiesAndEquity, 2)) !!}</td>
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
                disableMobile: true
            });
        });
    </script>
    <script>
        const goTopBtn = document.getElementById('goTopBtn');
        const goBackBtn = document.getElementById('goBackBtn');
        // Show button when user scrolls down
        window.addEventListener('scroll', function() {
            if (window.scrollY > 100) {
                goTopBtn.classList.add('show');
            } else {
                goTopBtn.classList.remove('show');
            }
        });
        window.addEventListener('scroll', function() {
            if (window.scrollY > 100) {
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
