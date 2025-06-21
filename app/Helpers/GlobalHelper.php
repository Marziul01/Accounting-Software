<?php

if (!function_exists('sendSMS')) {
    function sendSMS($phone, $message)
    {
        $url = "http://45.120.38.242/api/sendsms";

        $data = [
            'api_key'  => '01601989118.bN2a9kvNidsVjVXn1K',         // <-- Replace with real key
            'type'     => 'unicode',
            'phone'    => $phone,                 // Can be comma-separated
            'senderid' => 'Rashel_Book',       // <-- Replace with real sender ID
            'message'  => $message,
        ];

        \Log::info("📤 Sending SMS payload:", $data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);

        if ($response === false) {
            $curlError = curl_error($ch);
            \Log::error("❌ CURL ERROR: {$curlError}");
        } else {
            \Log::info("📩 SMS API Response: {$response}");
        }

        curl_close($ch);

        return $response;
    }
}
