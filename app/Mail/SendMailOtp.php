<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class SendMailOtp extends Mailable
{
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.email_otp')
        ->from('divya.mallick@typof.in', 'Divya from Typof')
        ->subject('Verify Otp')
        ->with('otp', $this->otp);
    }
}