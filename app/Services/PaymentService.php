<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;



class PaymentService
{
    /**
     * Create a new class instance.
     */
    protected $url ;
    protected $secretKey ;
    public function __construct()
    {
        $this->url = env('Paystack_Url' , 'https://api.paystack.co/transaction/verify/');
        $this->secretKey = env('Paystack_secret');
    }

    public function makePayment($body){
        $response = Http::withHeaders(['Authorization' => 'Bearer '.$this->secretKey])->post($this->url , $body);
        return $response;
    }
}
