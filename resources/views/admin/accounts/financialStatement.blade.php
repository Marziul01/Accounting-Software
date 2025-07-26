<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="1024">
    <title>আর্থিক বিবরণী</title>
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
        <h1 class="text-2xl font-bold mb-2">আর্থিক বিবরণী</h1>
        <p class="text-xl"> {!! bn_number(\Carbon\Carbon::now()->format('Y-m-d')) !!} তারিখে প্রস্তুতকৃত</p>
        @if($startDate && $endDate)
            <p class="text-lg"> {!! bn_number($startDate) !!} থেকে {!! bn_number($endDate) !!} পর্যন্ত</p>
        @endif
    </div>

    <!-- Summary Section -->
    
    <div class="d-flex flex-column">
        <!-- Combined Detailed Section -->
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
                        <tr>
                            <td colspan="2" class="bg-gray-100 font-semibold">সম্পদ সমূহ:</td>
                            <td colspan="2" class="bg-gray-100 font-semibold">দায় সমূহ ও মালিকানা সত্ত্ব:</td>
                        </tr>
                        <tr>
                            <td>
                               চলতি সম্পদ
                            </td>
                            <td class="text-end"> {!! bn_number(number_format(($totalCurrentAssetDeposit - $totalCurrentAssetWithdraw ), 2)) !!} </td>
                            <td>
                              স্বল্প মেয়াদী দায়  
                            </td>
                            <td class="text-end">{!! bn_number(number_format(($totalShortLiabilityDeposit - $totalShortLiabilityWithdraw ), 2)) !!}</td>
                        </tr>
                        <tr>
                            <td>
                               স্থায়ী সম্পদ
                            </td>
                            <td class="text-end">{!! bn_number(number_format($totalFixedAsset, 2)) !!}</td>
                            <td>
                              দীর্ঘ মেয়াদী দায়  
                            </td>
                            <td class="text-end">{!! bn_number(number_format(($totalLongLiabilityDeposit - $totalLongLiabilityWithdraw ), 2)) !!}</td>
                        </tr>
                        <tr>
                            <td>
                               বিনিয়োগ
                            </td>
                            <td class="text-end">{!! bn_number(number_format( $totalInvestAmount , 2)) !!}</td>
                            {{-- <td>
                              নীট লাভ বা ক্ষতি 
                            </td>
                            <td class="text-end">{!! bn_number(number_format($totalNetGainorLossBalance, 2)) !!}</td> --}}

                            <td>
                              মালিকানা সত্ত্ব  <span class="font-xs text-warning"> ( নীট লাভ বা ক্ষতি  এর সাথে যুক্ত )</span>
                            </td>
                            @php
                               $totalEquity = (
                                    ($totalBankDeposit - $totalBankWithdraw)
                                + $totalInvestAmount
                                + $totalFixedAsset
                                + $handCash
                                + ($totalCurrentAssetDeposit - $totalCurrentAssetWithdraw)
                                ) - (
                                    ($totalShortLiabilityDeposit - $totalShortLiabilityWithdraw)
                                + ($totalLongLiabilityDeposit - $totalLongLiabilityWithdraw)
                                );
                            @endphp
                            <td class="text-end">{!! bn_number(number_format($totalEquity, 2)) !!}</td>

                        </tr>
                        <tr>
                            <td>
                               হাতে নগদ
                            </td>
                            <td class="text-end">{!! bn_number(number_format($handCash, 2)) !!}</td>
                            <td>
                              {{-- মালিকানা সত্ত্ব   --}}
                            </td>
                            {{-- @php
                               $totalEquity = (
                                    ($totalBankDeposit - $totalBankWithdraw)
                                + $totalInvestAmount
                                + $totalFixedAsset
                                + $handCash
                                + ($totalCurrentAssetDeposit - $totalCurrentAssetWithdraw)
                                ) - (
                                    ($totalShortLiabilityDeposit - $totalShortLiabilityWithdraw)
                                + ($totalLongLiabilityDeposit - $totalLongLiabilityWithdraw)
                                );
                            @endphp --}}
                            <td class="text-end">
                                {{-- {!! bn_number(number_format($totalEquity, 2)) !!} --}}

                            </td>
                        </tr>
                        <tr>
                            <td>
                               ব্যাংক জমা
                            </td>
                            <td class="text-end">{!! bn_number(number_format(($totalBankDeposit - $totalBankWithdraw ), 2)) !!}</td>
                            <td>
                               
                            </td>
                            <td></td>
                        </tr>
                        <tr class="bg-gray-100">
                            <td class="font-semibold text-right">মোট সম্পদ সমূহ =</td>
                            @php
                                $totalAssets = ($totalCurrentAssetDeposit - $totalCurrentAssetWithdraw) + $totalFixedAsset + $totalInvestAmount + $handCash + ($totalBankDeposit - $totalBankWithdraw);
                            @endphp
                            <td class="font-semibold text-end"> {!! bn_number(number_format($totalAssets, 2)) !!} </td>
                            <td class="font-semibold text-right">মোট দায় সমূহ ও মালিকানা সত্ত্ব =</td>
                            @php
                                $totalLiabilitiesAndEquity = ($totalShortLiabilityDeposit - $totalShortLiabilityWithdraw) + ($totalLongLiabilityDeposit - $totalLongLiabilityWithdraw)  + $totalEquity;
                            @endphp
                                <td class="font-semibold text-end">{!! bn_number(number_format($totalLiabilitiesAndEquity, 2)) !!}</td>
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


    

</body>
</html>
