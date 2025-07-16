<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contact;
use App\Models\Occasion;
use App\Models\SMSEMAILTEMPLATE;
use Carbon\Carbon;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'send:occasion-sms', description: 'Send SMS to contacts based on their occasion dates')]
class SendOccasionSms extends Command
{
    public function handle()
    {
        $currentDate = Carbon::now('Asia/Dhaka');
        $currentYear = $currentDate->year;
        $today = Carbon::today();
        $occasions = SMSEMAILTEMPLATE::all();

        foreach ($occasions as $occasion) {

            if ($occasion->next_send !== null && $occasion->next_send != $currentYear) {
                continue;
            }

            $contactIds = explode(',', $occasion->contact_ids);
            $contacts = Contact::whereIn('id', $contactIds)->get();

            $sentAnyMessage = false;

            foreach ($contacts as $contact) {
                $shouldSend = false;
                $dateToCheck = null;
                $prefix = '';
                $occasionType = strtolower($occasion->occassion);

                switch ($occasionType) {
                    case 'birthday':
                        $dateToCheck = $contact->date_of_birth;
                        break;
                    case 'anniversary':
                        $dateToCheck = $contact->marriage_date;
                        break;
                    default:
                        $dateToCheck = $occasion->custom_date;
                        break;
                }

                if ($dateToCheck) {
                    $date = Carbon::parse($dateToCheck);
                    $isToday = (
                        ($occasionType === 'birthday' || $occasionType === 'anniversary')
                            ? $date->isBirthday($today)
                            : $date->isSameDay($today)
                    );

                    if ($isToday) {
                        $shouldSend = true;

                        switch ($occasionType) {
                            case 'birthday':
                                $age = $date->age;
                                $prefix = "প্রিয় {$contact->name}, আপনাকে জানাই " . $this->toBanglaNumber($age) . " তম জন্মদিনের অনেক শুভেচ্ছা। ";
                                break;
                            case 'anniversary':
                                $anniversaryYears = $date->diffInYears($today);
                                $prefix = "প্রিয় {$contact->name}, শুভ বিবাহবার্ষিকী। আপনার " . $this->toBanglaNumber($anniversaryYears) . " তম বিবাহ বার্ষিকীতে জানাই হৃদয় নিংড়ানো ভালোবাসা ও শুভেচ্ছা । ";
                                break;
                            default:
                                $prefix = "প্রিয় {$contact->name}, ";
                                break;
                        }
                    }
                }

                if ($shouldSend && $contact->mobile_number && $contact->sms_option == 1) {
                    $mainMessage = $occasion->message;
                    $finalMessage = $prefix . $mainMessage;

                    $mobileNumber = '88' . $contact->mobile_number;

                    $response = sendSMS($mobileNumber, $finalMessage);

                    \Log::info("✅ SMS sent to {$mobileNumber} for {$occasion->occassion}. Response: {$response}");

                    $sentAnyMessage = true;

                    if ($occasionType !== 'birthday' && $occasionType !== 'anniversary') {
                        $customDate = Carbon::parse($occasion->custom_date);
                        $customDate->year = $currentYear + 1;
                        $occasion->custom_date = $customDate->toDateString();
                    }
                }
            }

            if ($sentAnyMessage) {
                $occasion->next_send = $currentYear + 1;
                $occasion->save();
            }
        }
    }

    private function toBanglaNumber($number)
    {
        return strtr($number, ['0'=>'০','1'=>'১','2'=>'২','3'=>'৩','4'=>'৪','5'=>'৫','6'=>'৬','7'=>'৭','8'=>'৮','9'=>'৯']);
    }
}
