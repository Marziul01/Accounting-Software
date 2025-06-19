<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="1024">
    <title>বিশদ আর্থিক অবস্থা বিবরণী</title>
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
        </form>
        <button class="btn btn-primary no-print" onclick="window.print()">Print</button>        
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
        <p class="text-xl"> {!! bn_number(\Carbon\Carbon::now()->format('Y-m-d')) !!} তারিখে প্রস্তুতকৃত</p>
        @if($startDate && $endDate)
            <p class="text-lg"> {!! bn_number($startDate) !!} থেকে {!! bn_number($endDate) !!} পর্যন্ত</p>
        @endif
    </div>

    <!-- Summary Section -->
    
    <div class="d-flex flex-column">
        <div class="mb-12 order-2">
            
            <div class="overflow-x-auto">
                <table class="w-full bg-white shadow-lg rounded-lg report-table border-collapse">
                    <thead>
                        <tr>
                            <th class="w-2/6">বিবরণ</th>
                            <th class="w-1/6">টাকা</th>
                            <th class="w-2/6">বিবরণ</th>
                            <th class="w-1/6">টাকা</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Content from the first Detailed Section -->
                        <tr>
                            <td>হাতে নগদ</td>
                            <td class="text-end"> {!! bn_number(number_format($handCash , 2) ) !!} </td>
                            <td></td>
                            <td class="text-end"></td>
                        </tr>
                        <tr>
                            <td>ব্যাংক জমা </td>
                            <td class="text-end">{!! bn_number(number_format(($totalBankDeposit - $totalBankWithdraw) , 2)) !!}</td>
                            <td></td>
                            <td class="text-end"></td>
                        </tr>
                        <tr>
                            <td style="padding: 0px !important;">

                                <table class="w-full table-fixed">
                                    <thead>
                                        {{-- <tr>
                                            <th class="w-3/4 bg-white">আয় ক্যাটেগরি</th>
                                            <th class="w-1/4 bg-white text-end">পরিমাণ</th>
                                        </tr> --}}
                                    </thead>
                                    <tbody>
                                        <tr class="font-semibold">
                                                <th class="w-2/4 ">
                                                    <ul class="list-disc pl-5">
                                                        <li>চলতি সম্পদ সমুহঃ</li>
                                                    </ul>
                                                </th>
                                                <th class="w-2/4 ">টাকা</th>
                                        </tr>
                                        @if($assetSubCategories)
                                        @foreach ($assetSubCategories->where('asset_category_id', 4) as $assetSubCategory)
                                            {{-- Category row --}}
                                            <tr class="font-semibold">
                                                <th class="w-2/4 ">
                                                    <ul class="list-disc pl-5">
                                                        <li>{{ $assetSubCategory->name }}</li>
                                                    </ul>
                                                </th>
                                                <th class="w-2/4 "></th>
                                            </tr>

                                            {{-- Subcategory rows --}}
                                            @if($allCurrentAssets)
                                                @foreach ($allCurrentAssets->where('subcategory_id', $assetSubCategory->id) as $asset)
                                                @php
                                                    $showRow = true;

                                                    // Current transactions (from eager-loaded relationship)
                                                                $deposit  = $asset->transactions->where('transaction_type', 'Deposit')->sum('amount');
                                                                $withdraw = $asset->transactions->where('transaction_type', 'Withdraw')->sum('amount');

                                                                $currentAmount = $deposit - $withdraw;

                                                    // Check if no date filtering and asset amount is 0
                                                    if (empty($startDate) && empty($endDate) && $currentAmount == 0) {
                                                        $showRow = false;
                                                    }
                                                @endphp

                                                @if ($showRow)
                                                    <tr>
                                                        <td class="pl-8">{{ $asset->name }}</td>
                                                        
                                                        <td class="text-end">{!! bn_number(number_format($currentAmount, 2)) !!}</td>
                                                    </tr>
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endforeach
                                        @endif
                                        
                                    </tbody>
                                </table>
                                
                            </td>
                            <td></td>
                            <td style="padding: 0px !important;">
                               
                                
                                <table class="w-full">
                                            <thead>
                                                {{-- <tr>
                                                    <th class="w-3/4 bg-white"><ul class="list-disc pl-5"><li>{{ $expenseCategory->name }}</li></ul></th>
                                                    <th class="w-1/4 bg-white"></th>
                                                </tr> --}}
                                            </thead>
                                            <tbody>
                                                <tr class="font-semibold">
                                                <th class="w-2/4 ">
                                                    <ul class="list-disc pl-5">
                                                        <li>স্বল্পমেয়াদী দায় সমুহঃ</li>
                                                    </ul>
                                                </th>
                                                <th class="w-2/4 "></th>
                                        </tr>
                                        @if($liabilitySubCategories)
                                        @foreach ($liabilitySubCategories->where('liability_category_id', 3) as $liabilitySubCategory)
                                            {{-- Category row --}}
                                            <tr class="font-semibold">
                                                <th class="w-2/4 ">
                                                    <ul class="list-disc pl-5">
                                                        <li>{{ $liabilitySubCategory->name }}</li>
                                                    </ul>
                                                </th>
                                                <th class="w-2/4 "></th>
                                            </tr>

                                            {{-- Subcategory rows --}}
                                            @if($allShortLiabilities)
                                                @foreach ($allShortLiabilities->where('subcategory_id', $liabilitySubCategory->id) as $liability)
                                                @php
                                                    $showRow = true;

                                                    // Current transactions (from eager-loaded relationship)
                                                                $deposit  = $liability->transactions->where('transaction_type', 'Deposit')->sum('amount');
                                                                $withdraw = $liability->transactions->where('transaction_type', 'Withdraw')->sum('amount');
                                                                $currentAmount = $deposit - $withdraw;

                                                    // Check if no date filtering and asset amount is 0
                                                    if (empty($startDate) && empty($endDate) && $currentAmount == 0) {
                                                        $showRow = false;
                                                    }
                                                @endphp

                                                @if ($showRow)
                                                    <tr>
                                                        <td class="pl-8">{{ $liability->name }}</td>
                                                        
                                                        <td class="text-end">{!! bn_number(number_format($currentAmount, 2)) !!}</td>
                                                    </tr>
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endforeach
                                        @endif
                                                
                                            </tbody>
                                </table>
                                
                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="font-semibold text-right">মোট চলতি সম্পদ =</td>
                            <td class="font-semibold text-end">{!! bn_number(number_format(($totalCurrentAssetDeposit - $totalCurrentAssetWithdraw ),2)) !!}</td>
                            <td class="font-semibold text-right">মোট স্বল্পমেয়াদী দায়  =</td>
                            <td class="font-semibold text-end">{!! bn_number(number_format(($totalShortLiabilityDeposit - $totalShortLiabilityWithdraw),2)) !!}</td>
                        </tr>

                        <!-- Section for "দীর্ঘমেয়াদী বিনিয়োগ হতে প্রাপ্ত আয় সমূহ" (Investment Income) -->
                        <tr>
                            <td style="padding: 0px !important;">
                                <table class="w-full table-fixed">
                                    <thead>
                                        {{-- <tr>
                                            <th class="w-3/4 bg-white">বিনিয়োগের ধরণ</th>
                                            <th class="w-1/4 bg-white text-end">পরিমাণ</th>
                                        </tr> --}}
                                    </thead>
                                    <tbody>
                                        <tr class="font-semibold">
                                                <th class="w-2/4 ">
                                                    <ul class="list-disc pl-5">
                                                        <li>স্থায়ী সম্পদ সমুহঃ</li>
                                                    </ul>
                                                </th>
                                                <th class="w-2/4 "></th>
                                        </tr>
                                        @if($assetSubCategories)
                                        @foreach ($assetSubCategories->where('asset_category_id', 5) as $assetSubCategory)
                                            {{-- Category row --}}
                                            <tr class="font-semibold">
                                                <th class="w-2/4 ">
                                                    <ul class="list-disc pl-5">
                                                        <li>{{ $assetSubCategory->name }}</li>
                                                    </ul>
                                                </th>
                                                <th class="w-2/4 "></th>
                                            </tr>

                                            {{-- Subcategory rows --}}
                                            @if($allFixedAssets)
                                                @foreach ($allFixedAssets->where('subcategory_id', $assetSubCategory->id) as $asset)
                                                @php
                                                    $showRow = true;

                                                    // Current transactions (from eager-loaded relationship)
                                                                $deposit  = $asset->transactions->where('transaction_type', 'Deposit')->sum('amount');
                                                                $withdraw = $asset->transactions->where('transaction_type', 'Withdraw')->sum('amount');

                                                                $currentAmount = $deposit - $withdraw;

                                                    // Check if no date filtering and asset amount is 0
                                                    if (empty($startDate) && empty($endDate) && $currentAmount == 0) {
                                                        $showRow = false;
                                                    }
                                                @endphp

                                                @if ($showRow)
                                                    <tr>
                                                        <td class="pl-8">{{ $asset->name }}</td>
                                                       
                                                        <td class="text-end">{!! bn_number(number_format($currentAmount, 2)) !!}</td>
                                                    </tr>
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>

                            </td>
                            <td></td>
                            <td style="padding: 0px !important;">
                                
                                <table class="w-full table-fixed">
                                    <thead>
                                        {{-- <tr>
                                            <th class="w-3/4 bg-white">বিনিয়োগের ক্ষতি</th>
                                            <th class="w-1/4 bg-white text-end">পরিমাণ</th>
                                        </tr> --}}
                                    </thead>
                                    <tbody>
                                        <tr class="font-semibold">
                                                <th class="w-2/4 ">
                                                    <ul class="list-disc pl-5">
                                                        <li>দীর্ঘমেয়াদী দায় সমুহঃ</li>
                                                    </ul>
                                                </th>
                                                <th class="w-2/4 "></th>
                                        </tr>
                                        @if($liabilitySubCategories)
                                        @foreach ($liabilitySubCategories->where('liability_category_id', 4) as $liabilitySubCategory)
                                            {{-- Category row --}}
                                            <tr class="font-semibold">
                                                <th class="w-2/4 ">
                                                    <ul class="list-disc pl-5">
                                                        <li>{{ $liabilitySubCategory->name }}</li>
                                                    </ul>
                                                </th>
                                                <th class="w-2/4 "></th>
                                            </tr>

                                            {{-- Subcategory rows --}}
                                            @if($allLongLiabilities)
                                                @foreach ($allLongLiabilities->where('subcategory_id', $liabilitySubCategory->id) as $liability)
                                                @php
                                                    $showRow = true;

                                                    // Current transactions (from eager-loaded relationship)
                                                                $deposit  = $liability->transactions->where('transaction_type', 'Deposit')->sum('amount');
                                                                $withdraw = $liability->transactions->where('transaction_type', 'Withdraw')->sum('amount');

                                                                $currentAmount = $deposit - $withdraw;

                                                    // Check if no date filtering and asset amount is 0
                                                    if (empty($startDate) && empty($endDate) && $currentAmount == 0) {
                                                        $showRow = false;
                                                    }
                                                @endphp

                                                @if ($showRow)
                                                    <tr>
                                                        <td class="pl-8">{{ $liability->name }}</td>
                                                        
                                                        <td class="text-end">{!! bn_number(number_format($currentAmount, 2)) !!}</td>
                                                    </tr>
                                                    @endif
                                                @endforeach
                                            @endif
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>

                            </td>
                            <td></td>
                        </tr>
                        <tr>
                            <td class="font-semibold text-right">মোট স্থায়ী সম্পদঃ</td>
                            
                            <td class="font-semibold text-end">{!! bn_number(number_format($totalFixedAsset,2)) !!}</td>
                            <td class="font-semibold text-right">মোট দীর্ঘমেয়াদী দায়ঃ</td>
                            
                            <td class="font-semibold text-end">{!! bn_number(number_format(($totalLongLiabilityDeposit - $totalLongLiabilityWithdraw),2)) !!}</td>
                        </tr>
                        <tr>
                            <td style="padding: 0px !important;">
                                <table class="w-full table-fixed">
                                    <thead>
                                        {{-- <tr>
                                            <th class="w-3/4 bg-white">বিনিয়োগের ধরণ</th>
                                            <th class="w-1/4 bg-white text-end">পরিমাণ</th>
                                        </tr> --}}
                                    </thead>
                                    <tbody>
                                        <tr class="font-semibold">
                                                <th class="w-2/4 ">
                                                    <ul class="list-disc pl-5">
                                                        <li>স্বল্পমেয়াদী বিনিয়োগঃ</li>
                                                    </ul>
                                                </th>
                                                <th class="w-2/4 "></th>
                                        </tr>
                                        @php
                                            $shortTermInvestTotal = 0;
                                        @endphp
                                        @if($investmentSubCategories)
                                        @foreach ($investmentSubCategories->where('investment_category_id', 4) as $investmentSubCategory)
                                        @php
                                            $subshortTermInvestTotal = 0;
                                        @endphp
                                            {{-- Category row --}}
                                            <tr class="font-semibold">
                                                <th class="w-2/4 ">
                                                    <ul class="list-disc pl-5">
                                                        <li>{{ $investmentSubCategory->name }}</li>
                                                    </ul>
                                                </th>
                                                <th class="w-2/4 "></th>
                                            </tr>

                                            {{-- Subcategory rows --}}
                                            @if($allInvestments)
                                                @foreach ($allInvestments->where('investment_sub_category_id', $investmentSubCategory->id) as $investment)
                                                    <tr>
                                                        <td class="pl-8">{{ $investment->name }}</td>
                                                        @php
                                                            

                                                                // Current transactions (from eager-loaded relationship)
                                                                $deposit  = $investment->transactions->where('transaction_type', 'Deposit')->sum('amount');
                                                                $withdraw = $investment->transactions->where('transaction_type', 'Withdraw')->sum('amount');

                                                            
                                                                $nvestmentAmount = ($deposit - $withdraw);
                                                                $subshortTermInvestTotal += $nvestmentAmount;
                                                            
                                                        @endphp
                                                        <td class="text-end">{!! bn_number(number_format($nvestmentAmount, 2)) !!}</td>
                                                    </tr>
                                                @endforeach
                                                @php
                                                    $shortTermInvestTotal += $subshortTermInvestTotal;
                                                @endphp
                                            @endif
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>

                            </td>
                            <td></td>
                            <td>
                                
                                

                            </td>
                            <td class="text-end"></td>
                        </tr>
                        <tr>
                            <td class="font-semibold text-right">মোট স্বল্পমেয়াদী বিনিয়োগ</td>
                            
                            <td class="font-semibold text-end">{!! bn_number(number_format($shortTermInvestTotal,2)) !!}</td>
                            <td class="font-semibold text-right">নীট লাভ বা ক্ষতি</td>
                            
                            <td class="text-end">{!! bn_number(number_format($totalNetGainorLossBalance, 2)) !!}</td>
                        </tr>
                        <tr>
                            <td style="padding: 0px !important;">
                                <table class="w-full table-fixed">
                                    <thead>
                                        {{-- <tr>
                                            <th class="w-3/4 bg-white">বিনিয়োগের ধরণ</th>
                                            <th class="w-1/4 bg-white text-end">পরিমাণ</th>
                                        </tr> --}}
                                    </thead>
                                    <tbody>
                                        <tr class="font-semibold">
                                                <th class="w-2/4 ">
                                                    <ul class="list-disc pl-5">
                                                        <li>দীর্ঘমেয়াদী  বিনিয়োগঃ</li>
                                                    </ul>
                                                </th>
                                                <th class="w-2/4 "></th>
                                        </tr>
                                        @php
                                            $longTermInvestTotal = 0;
                                        @endphp
                                        @if($investmentSubCategories)
                                        @foreach ($investmentSubCategories->where('investment_category_id', 5) as $investmentSubCategory)
                                            {{-- Category row --}}
                                            <tr class="font-semibold">
                                                <th class="w-2/4 ">
                                                    <ul class="list-disc pl-5">
                                                        <li>{{ $investmentSubCategory->name }}</li>
                                                    </ul>
                                                </th>
                                                <th class="w-2/4 "></th>
                                            </tr>

                                            {{-- Subcategory rows --}}
                                            @if($allInvestments)
                                                @foreach ($allInvestments->where('investment_sub_category_id', $investmentSubCategory->id) as $investment)
                                                    <tr>
                                                        <td class="pl-8">{{ $investment->name }}</td>
                                                        @php
                                                            

                                                                // Current transactions (from eager-loaded relationship)
                                                                $deposit  = $investment->transactions->where('transaction_type', 'Deposit')->sum('amount');
                                                                $withdraw = $investment->transactions->where('transaction_type', 'Withdraw')->sum('amount');

                                                            
                                                                $nvestmentAmount = ($deposit - $withdraw);
                                                                $longTermInvestTotal += $nvestmentAmount;
                                                            
                                                        @endphp
                                                        <td class="text-end">{!! bn_number(number_format($nvestmentAmount, 2)) !!}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>

                            </td>
                            <td></td>
                            <td>
                                
                                

                            </td>
                            <td class="text-end"></td>
                        </tr>
                        <tr>
                            <td class="font-semibold text-right">মোট দীর্ঘমেয়াদী বিনিয়োগ</td>
                            
                            <td class="font-semibold text-end">{!! bn_number(number_format($longTermInvestTotal,2)) !!}</td>
                            <td class="font-semibold text-right">মূলধন বা মালিকানা সত্ত্ব</td>
                            @php
                                $totalEquity = (($totalBankDeposit - $totalBankWithdraw ) + $totalInvestAmount + $totalFixedAsset + $handCash + ($totalCurrentAssetDeposit - $totalCurrentAssetWithdraw)) - (($totalShortLiabilityDeposit - $totalShortLiabilityWithdraw) + $totalNetGainorLossBalance + ($totalLongLiabilityDeposit - $totalLongLiabilityWithdraw));
                            @endphp
                            <td class="font-semibold text-end">{!! bn_number(number_format($totalEquity,2)) !!}</td>
                        </tr>

                        <!-- Section for "দীর্ঘমেয়াদী বিনিয়োগ হতে মোট প্রাপ্ত আয়" and "মোট প্রদান" -->
                        <tr class="bg-gray-100">
                            <td class="font-semibold text-right">মোট বিনিয়োগ = </td>
                            
                            <td class="font-semibold text-end">{!! bn_number(number_format($totalInvestAmount,2)) !!}</td>
                            <td class="font-semibold text-right"></td>
                            
                            <td class="font-semibold text-end"></td>
                        </tr>
                        
                        <tr class="bg-gray-100">
                            <td class="font-semibold">মোট</td>
                            @php
                                $ttoalassets = $handCash + ($totalBankDeposit - $totalBankWithdraw) + ($totalCurrentAssetDeposit - $totalCurrentAssetWithdraw ) + $totalFixedAsset + $totalInvestAmount
                            @endphp
                            <td class="font-semibold text-end">{!! bn_number(number_format($ttoalassets,2)) !!} </td>
                            <td class="font-semibold">মোট</td>
                            @php
                                $ttoallia = $totalEquity + $totalNetGainorLossBalance + ($totalLongLiabilityDeposit - $totalLongLiabilityWithdraw) + ($totalShortLiabilityDeposit - $totalShortLiabilityWithdraw)
                            @endphp
                            <td class="font-semibold text-end"> {!! bn_number(number_format($ttoallia,2)) !!}</td>
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

</body>
</html>
