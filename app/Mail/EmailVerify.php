<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class EmailVerify extends Mailable
{
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.email_verify')
        ->from('divya.mallick@typof.in', 'Divya from Typof')
        ->subject('Congratulations💐, your e-commerce website is ready!');
    }
}