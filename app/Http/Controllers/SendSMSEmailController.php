<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Notification;

class SendSMSEmailController extends Controller
{
    public function sendSMSEmail()
    {
        return view('admin.send_sms_email',[
            'contacts' => Contact::all(),

        ]);
    }

    public function sendSms(Request $request)
    {
        $request->validate([
            'contact_ids' => 'required|array',
            'message' => 'required|string|max:500',
        ]);

        $contacts = Contact::whereIn('id', $request->contact_ids)->get();

        foreach ($contacts as $contact) {
            $message = $request->message;
            $number = '88'.$contact->mobile_number ;
            $response = sendSMS($number, $message);

            \Log::info("SMS sent to {$contact->mobile_number}: {$request->message}");
        }

        Notification::create([
                'sent_date' => now(),
                'sms_sent' => 1,
                'message' => Auth()->user()->name . ' sendted Message : "' . $message .' " to '. count($contacts) .' contacts.',
            ]);

        return response()->json([
            'status' => 'success',
            'message' => 'SMS sent successfully!'
        ]);
    }

    // ✅ Send Email
    public function sendEmail(Request $request)
    {
        $request->validate([
            'contact_ids' => 'required|array',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $contacts = Contact::whereIn('id', $request->contact_ids)->get();

        foreach ($contacts as $contact) {
            Mail::raw($request->message, function ($mail) use ($contact, $request) {
                $mail->to($contact->email)
                    ->subject($request->subject);
            });
        }

        Notification::create([
                'sent_date' => now(),
                'email_sent' => 1,
                'message' => Auth()->user()->name . ' sendted Email : "' . $request->subject .' " to '. count($contacts) .' contacts.',
            ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Emails sent successfully!'
        ]);
    }
}
