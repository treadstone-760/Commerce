<?php

namespace App\Services;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Http;

class SmsService implements ShouldQueue
{
    /**
     * Create a new class instance.
     */
    protected $message ;
    protected $phone;
    protected $url;
    protected $token;
    public function __construct($message , $phone)
    {
        $this->message = $message;
        $this->phone = $phone;
        $this->url = env('mNotify_url' , 'https://api.mnotify.net/sms/send');
        $this->token = env('mNotify_token' , 'YOUR_API_TOKEN');
    }


    public function send()
    {
        $response = Http::post($this->url , [
            "key" => $this->token,
            "receipient" => $this->phone,
            "sender" => "COMMERCE" , 
            "message" => $this->message
        ]);
    }

}
