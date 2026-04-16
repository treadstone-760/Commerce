<?php

use Symfony\Component\HttpFoundation\Response;
use App\Models\Order;

if(!function_exists('Res')) {
    function Res(string $message, int $status = 200 , array $data = []): Response
    {
        return Response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data

        ] , $status);
    }
}


if(!function_exists('invoiceNumber')) {
    function invoiceNumber($length = 8)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        if(Order::where('invoice_number' , $randomString)->exists()) {
            return invoiceNumber($length);
        }
        return $randomString;
    }
}

if(!function_exists('paginationDetails')) {
    function paginationDetails($data)
    {
        return [
            'current_page' => $data->currentPage(),
            'total' => $data->total(),
            'data_size' => $data->count(),
            'first_page' => 1,
            'last_page' => $data->lastPage(),
            'prev' => $data->currentPage() - 1 < 1 ? null : $data->currentPage() - 1,
            'next' => $data->currentPage() + 1 > $data->lastPage() ? null : $data->currentPage() + 1,
            'page_size' => $data->perPage(),
        ];
    }
}