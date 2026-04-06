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
        $this->url = config('services.paystack.url');
        $this->verify_url = config('services.paystack.verify_url');
        $this->secretKey = config('services.paystack.secret');
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
