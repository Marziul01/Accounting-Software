<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>সম্পদ </title>
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
 
প্রিয় {{ $asset->name }}, আপনার নিকট আজ পর্যন্ত রাসেল এর থেকে প্রাপ্ত মোট অপরিশোধিত ঋণের পরিমান {{ $totalAmountBn }} টাকা ৷
    <br><br>
    {{ $templateText }}   


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
