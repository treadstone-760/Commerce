<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class SendSmsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    protected $message;

    protected $phone;

    protected $url;

    protected $token;

    public function __construct($message, $phone)
    {
        $this->message = $message;
        $this->phone = $phone;
        $this->url = env('mNotify_url').'?key='.env('mNotify_token');
        // $this->token = env('mNotify_token' , 'YOUR_API_TOKEN');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Http::post($this->url, [
            'recipient' => [$this->phone],
            'sender' => 'EllasShop',
            'message' => $this->message,
        ]);
    }

   
}
