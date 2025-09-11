<?php

namespace App\Console\Commands;

use App\Mail\OccasionMail;
use Illuminate\Console\Command;
use App\Models\Contact;
use App\Models\Notification;
use App\Models\Occasion;
use App\Models\SMSEMAILTEMPLATE;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'send:occasion-sms', description: 'Send SMS to contacts based on their occasion dates')]
class SendOccasionSms extends Command
{
    public function handle()
    {
        $currentDate = Carbon::now('Asia/Dhaka');
        $currentYear = $currentDate->year;
        $today = Carbon::today();
        $occasions = SMSEMAILTEMPLATE::with(['contacts.contact'])->get();

        foreach ($occasions as $occasion) {
            $occasionType = strtolower($occasion->occassion);
            $isCustomToday = false;

            // Custom occasion check
            // if (!in_array($occasionType, ['birthday', 'anniversary'])) {
            //     if ($occasion->custom_date && Carbon::parse($occasion->custom_date)->isSameDay($today)) {
            //         $isCustomToday = true;
            //     }
            // }
            if (!in_array($occasionType, ['birthday', 'anniversary'])) {
                if (!$occasion->custom_date) {
                    continue; // no date at all
                }

                $customDate = Carbon::parse($occasion->custom_date);

                // If custom date's year is NOT the current year → skip
                if ($customDate->year !== $currentYear) {
                    continue;
                }

                // Check if it's today
                if ($customDate->isSameDay($today)) {
                    $isCustomToday = true;
                }
            }

            // For custom occasion summary
            $customOccasionContacts = [];

            foreach ($occasion->contacts as $ocassionContact) {
                $contact = $ocassionContact->contact;
                if (!$contact) continue;

                // Skip if next_send doesn't match current year
                if ($ocassionContact->next_send && $ocassionContact->next_send != $currentYear) {
                    continue;
                }

                $shouldSend = false;
                $prefix = '';
                $mainMessage = '';
                $finalMessage = '';
                $emailSent = 0;
                $smsSent = 0;

                if (in_array($occasionType, ['birthday', 'anniversary'])) {
                    // Pick date field
                    $dateToCheck = ($occasionType === 'birthday') ? $contact->date_of_birth : $contact->marriage_date;

                    if ($dateToCheck) {
                        $date = Carbon::parse($dateToCheck);

                        // Match only day & month
                        $isToday = $date->day === $today->day && $date->month === $today->month;

                        if ($isToday) {
                            $shouldSend = true;

                            if ($occasion->english == 1) { // Bangla
                                if ($occasionType === 'birthday') {
                                    $age = $date->age;
                                    $prefix = "প্রিয় {$contact->name}, আপনাকে জানাই " . $this->toBanglaNumber($age) . " তম জন্মদিনের অনেক শুভেচ্ছা। ";
                                } else {
                                    $anniversaryYears = $date->diffInYears($today);
                                    $prefix = "প্রিয় {$contact->name}, শুভ বিবাহবার্ষিকী। আপনার " . $this->toBanglaNumber($anniversaryYears) . " তম বিবাহ বার্ষিকীতে জানাই হৃদয় নিংড়ানো ভালোবাসা ও শুভেচ্ছা । ";
                                }
                                $mainMessage = $occasion->message;
                            } else { // English
                                if ($occasionType === 'birthday') {
                                    $age = $date->age;
                                    $prefix = "Dear {$contact->name}, Wishing you a very Happy " . $this->toOrdinal($age) . " Birthday! ";
                                } else {
                                    $anniversaryYears = $date->diffInYears($today);
                                    $prefix = "Dear {$contact->name}, Happy Anniversary! Wishing you lots of love and happiness on your " . $this->toOrdinal($anniversaryYears) . " anniversary. ";
                                }
                                $mainMessage = $occasion->english_message;
                            }
                        }
                    }
                } elseif ($isCustomToday) {
                    $shouldSend = true;
                    if ($occasion->english == 1) { // Bangla
                        $prefix = "প্রিয় {$contact->name}, ";
                        $mainMessage = $occasion->message;
                    } else {
                        $prefix = "Dear {$contact->name}, ";
                        $mainMessage = $occasion->english_message;
                    }
                }

                if ($shouldSend) {
                    // Prepare final message once
                    $finalMessage = $prefix . $mainMessage;

                    // Send SMS
                    if ($contact->mobile_number && $contact->sms_option == 1) {
                        $mobileNumber = '88' . $contact->mobile_number;
                        $response = sendSMS($mobileNumber, $finalMessage);
                        $smsSent = 1;
                        Log::info("✅ SMS sent to {$mobileNumber} for {$occasion->occassion}. Response: {$response}");
                    }

                    // Send Email
                    if ($contact->send_email == 1 && $contact->email) {
                        try {
                            Mail::to($contact->email)->send(
                                new OccasionMail("{$occasion->occassion} Wishes", $finalMessage)
                            );
                            $emailSent = 1;
                            Log::info("📧 Email sent to {$contact->email} for {$occasion->occassion}.");
                        } catch (\Exception $e) {
                            Log::error("❌ Failed to send email to {$contact->email}. Error: " . $e->getMessage());
                        }
                    }

                    // Notify admin for birthday/anniversary
                    if (in_array($occasionType, ['birthday', 'anniversary'])) {
                        try {
                            $admin = User::where('role', 0)->first();
                            if ($admin && $admin->email) {
                                Mail::to($admin->email)->send(
                                    new OccasionMail(
                                        "Reminder: {$contact->name}'s " . ucfirst($occasionType),
                                        "{$contact->name}'s {$occasionType} is today. Message sent: {$finalMessage}"
                                    )
                                );
                                Log::info("📧 Admin {$admin->email} notified about {$contact->name}'s {$occasionType}.");
                            }
                        } catch (\Exception $e) {
                            Log::error("❌ Failed to notify admin about {$contact->name}'s {$occasionType}. Error: " . $e->getMessage());
                        }
                    }

                    // Update next_send
                    $ocassionContact->next_send = $currentYear + 1;
                    $ocassionContact->save();

                    // Notification creation
                    if (in_array($occasionType, ['birthday', 'anniversary'])) {
                        // Per-contact notification
                        Notification::create([
                            'sender_name' => $contact->name,
                            'message' => $finalMessage,
                            'sent_date' => now(),
                            'email_sent' => $emailSent,
                            'sms_sent' => $smsSent,
                            'occasion_name' => $occasion->occassion,
                            'contact_id' => $contact->id,
                        ]);
                    } else {
                        // For custom occasions, just collect contacts for summary
                        $customOccasionContacts[] = $contact->name;
                    }

                    // Update custom_date for next year
                    if (!in_array($occasionType, ['birthday', 'anniversary'])) {
                        $occasion->custom_date = Carbon::parse($occasion->custom_date)->addYear();
                        $occasion->next_send = $currentYear + 1; // Update next_send for custom occasions
                        $occasion->save();
                    }
                }
            }

            // After sending all for a custom occasion, store single notification
            if (!empty($customOccasionContacts) && !in_array($occasionType, ['birthday', 'anniversary'])) {
                Notification::create([
                    'sender_name' => 'All Contacts',
                    'message' => $mainMessage,
                    'sent_date' => now(),
                    'email_sent' => 1,
                    'sms_sent' => 1,
                    'occasion_name' => $occasion->occassion,
                    'contact_id' => null,
                ]);
            }
        }
    }




    private function toBanglaNumber($number)
    {
        return strtr($number, ['0'=>'০','1'=>'১','2'=>'২','3'=>'৩','4'=>'৪','5'=>'৫','6'=>'৬','7'=>'৭','8'=>'৮','9'=>'৯']);
    }

    private function toOrdinal($number)
    {
        $ends = ['th','st','nd','rd','th','th','th','th','th','th'];
        if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
            return $number . 'th';
        } else {
            return $number . $ends[$number % 10];
        }
    }

}
