<?php

use Illuminate\Console\Command;
use App\Models\Contact;
use App\Models\Occasion;
use App\Models\SMSEMAILTEMPLATE;
use Carbon\Carbon;

class SendOccasionSms extends Command
{
    protected $signature = 'send:occasion-sms';
    protected $description = 'Send SMS to contacts based on their occasion dates';

    public function handle()
    {
        $today = Carbon::today();

        $occasions = SMSEMAILTEMPLATE::all();

        foreach ($occasions as $occasion) {
            $contactIds = explode(',', $occasion->contact_ids);
            $contacts = Contact::whereIn('id', $contactIds)->get();

            foreach ($contacts as $contact) {
                $shouldSend = false;
                $dateToCheck = null;
                $prefix = '';
                $occasionType = strtolower($occasion->occassion); // e.g. 'birthday'

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
                }
            }
        }
    }

    private function toBanglaNumber($number)
    {
        return strtr($number, ['0'=>'০','1'=>'১','2'=>'২','3'=>'৩','4'=>'৪','5'=>'৫','6'=>'৬','7'=>'৭','8'=>'৮','9'=>'৯']);
    }


}
