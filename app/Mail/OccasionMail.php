<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OccasionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subjectLine;
    public $body;

    public function __construct($subjectLine, $body)
    {
        $this->subjectLine = $subjectLine;
        $this->body = $body;
    }

    public function build()
    {
        return $this->subject($this->subjectLine)
                    ->view('emails.occasion')
                    ->with(['body' => $this->body]);
    }
}
