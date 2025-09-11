<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'SolaimanLipi', sans-serif; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td, th { border: 1px solid #000; padding: 6px; }
        .tiro-font {
            font-family: 'SolaimanLipi', sans-serif;
        }
        .report-header{
            text-align: center;
        }
        .signature_img {
            width: 15%;
            height: auto;
            padding-bottom: 3px;
            border-bottom: #000 solid 1px;
        }
    </style>
</head>
<body>
    @php
  function bn_number($number)
        {
            $eng = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
            $bang = ['০', '১', '২', '৩', '৪', '৫', '৬', '৭', '৮', '৯'];
            return '<span class="tiro-font">' . str_replace($eng, $bang, $number) . '</span>';
        }
@endphp
    <div class="report-header">
        <img src="{{ asset($setting->site_logo) }}" height="auto" width="15%" class="img" alt="">
        {{-- <p><strong>হিসাব নাম্বার:</strong> #{{ $liability->slug }}{{ $liability->id }}</p> --}}
        <p><strong>প্রকাশের তারিখ:</strong> {!! bn_number(now()->format('d-m-Y')) !!}</p>
    </div>

    <table>
        <tr>
            <td>
                <strong>প্রেরকঃ</strong><br>
                {{ $setting->site_owner }}<br>
                ঠিকানাঃ {{ $setting->site_address }}
            </td>
            <td>
                <strong>প্রাপকঃ</strong><br>
                নামঃ {{ $liability->name }}<br>
                ঠিকানাঃ {{ $liability->present_address }}
            </td>
        </tr>
    </table>

    <h4>লেনদেনের রিসিট</h4>
    <table>
        <tr>
            <td>লেনদেনের তারিখ:</td>
            <td>{!! bn_number(\Carbon\Carbon::parse($request->entry_date)->format('d-m-Y')) !!} ইং</td>
        </tr>
        <tr>
            <td>গৃহীত ঋণের পরিমাণ:</td>
            <td>{{ $requestASmount}} টাকা</td>
        </tr>
        <tr>
            <td>মোট অপরিশোধিত ঋণের স্থিতির পরিমাণ:</td>
            <td>{{ $totalAmount }} টাকা</td>
        </tr>
    </table>

    <p style="margin-top:20px;"><strong>বিশেষ দ্রষ্টব্যঃ</strong> লেনদেন এ কোন রকম অসংগতি পাওয়া গেলে সেটা আলোচনা করে ঠিক করে নেয়ার অনুরোধ করা হলো। ধন্যবাদ।</p>

    <br><br>
    <p>
        <div class="w-100">
            <img src="{{ asset($setting->signature) }}" height="100%" class="signature_img" alt="">
        </div>
        {{ $setting->site_owner }}<br>
        ঠিকানা: {{ $setting->site_address }}<br>
        ইমেইল: {{ $setting->site_email }}<br>
        ওয়েবসাইট : {{ $setting->site_link }}
    </p>
</body>
</html>
