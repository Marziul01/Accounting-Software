<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'SolaimanLipi', sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td, th { border: 1px solid #000; padding: 6px; }
        .tiro-font {
            font-family: 'SolaimanLipi', sans-serif;
        }
    </style>
</head>
<body>
    
    প্রিয় {{ $asset->name }},
    <br><br>
    {{ $templateText }}
    
    <br><br>

    <table>
        <tr>
            <td>লেনদেনের তারিখ:</td>
            <td>{{ $transDate }} ইং</td>
        </tr>
        <tr>
            <td>প্রদত্ত ঋণের পরিমাণ:</td>
            <td>{{ $requestASmount }} টাকা</td>
        </tr>
        <tr>
            <td>পূর্বের প্রদানকৃত ঋণের পরিমাণ:</td>
            <td>{{ $previousAmountBn }} টাকা</td>
        </tr>
        <tr>
            <td>মোট প্রদত্ত ঋণের পরিমাণ:</td>
            <td>{{ $totalAmountBn }} টাকা</td>
        </tr>
    </table>

    <br><br>
    উক্ত মেইলটি আপনার নিকট লেনদেনের তথ্য বিবরণী হিসাবে সয়ংক্রিয় ভাবে সিস্টেম থেকে পাঠানো হয়েছে। তাই উক্ত মেইলের বিপরীতে কোন প্রতিউত্তর না দেয়ার জন্য অনুরোধ রইলো।

    <br><br>
    <strong>ধন্যবাদান্তে,</strong><br>
    {{ $setting->site_owner }}<br>
    ঠিকানা: {{ $setting->site_address }}<br>
    ইমেইল: {{ $setting->site_email }}<br>
    ওয়েবসাইট : <a href="https://{{ $setting->site_link }}">{{ $setting->site_link }}</a>
</body>
</html>
