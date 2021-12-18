<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMobileOtp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $mobile;
    protected $message;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($mobile, $message)
    {
        $this->mobile = $mobile;
        $this->msg = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ch = curl_init('https://www.txtguru.in/imobile/api.php?');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "username=typof.in&password=94596895&source=TYPOFT&dmobile=91".$this->mobile."&dltentityid=1501558460000027046&dltheaderid=1505161985265418850&dlttempid=".env('SMS_OTP_ID')."&message=".$this->msg);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        $data = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);
    }
}
