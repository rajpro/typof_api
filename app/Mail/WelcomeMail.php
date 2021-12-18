<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $name;
    protected $link;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $link)
    {
        $this->name = $name;
        $this->link = $link;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.welcome-mail')
        ->from('divya.mallick@typof.in', 'Divya from Typof')
        ->cc('sales@typof.in', 'Typof')
        ->bcc('trilochan@typof.in', 'Trilochan Parida')
        ->subject('Congratulations💐, your e-commerce website is ready!')
        ->with(['name' => $this->name, 'link' => $this->link]);
    }
}
