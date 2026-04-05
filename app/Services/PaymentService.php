<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;



class PaymentService
{
    /**
     * Create a new class instance.
     */
    protected $url ;
    protected $verify_url ;
    protected $secretKey ;
    public function __construct()
    {
        $this->url = env('Paystack_Url');
        $this->verify_url = env('Paystack_verify_url' , 'https://api.paystack.co/transaction/verify/');
        $this->secretKey = env('Paystack_secret');
    }

    public function makePayment($body){
        $response = Http::withHeaders(['Authorization' => 'Bearer '.$this->secretKey])->post($this->url , $body);
        return $response;
    }

    public function verifyPayment($reference){
        try{
            $response = Http::withHeaders(['Authorization' => 'Bearer '.$this->secretKey])->get($this->verify_url  . $reference );
            return $response;
        }catch(\Exception $e){
            Log::error([
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);
            return Res('Something went wrong', 500);
        }
       
    }
}
