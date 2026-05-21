<?php

use App\Models\Order;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

if (! function_exists('Res')) {
    function Res(string $message, int $status = 200, array $data = []): Response
    {
        return Response()->json([
            'status' => $status,
            'message' => $message,
            'data' => $data,

        ], $status);
    }
}

if (! function_exists('invoiceNumber')) {
    function invoiceNumber($length = 8)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        if (Order::where('invoice_number', $randomString)->exists()) {
            return invoiceNumber($length);
        }

        return $randomString;
    }
}

if (! function_exists('paginationDetails')) {
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

if (! function_exists('convertFile')) {
    function convertFile($data)
    {
        try {
            // Check if the input is a valid base64 file string
            if (! is_string($data) || ! Str::contains($data, 'base64,')) {
                return $data;
            }
            $exploded_file = explode(',', $data);
            // \Log::info('exploded_file' ,$exploded_file );
            $file = $exploded_file[1];
            $file_extension = fileExtension($exploded_file[0]);
            $file_name = Str::random(27).'.'.$file_extension;
            $file = base64_decode($file);

            return [
                'file_name' => $file_name,
                'file' => $file,
            ];

        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw $e;
        }
    }
}



if(! function_exists('fileExtension')){
    function fileExtension($data)
    {
        try {
            return match (true) {
                Str::contains($data, ['pdf', 'PDF']) => 'pdf',
                Str::contains($data, ['doc', 'DOC']) => 'doc',
                Str::contains($data, ['docx', 'DOCX']) => 'docx',
                Str::contains($data, ['jpg', 'JPG']) => 'jpg',
                Str::contains($data, ['jpeg', 'JPEG']) => 'jpeg',
                Str::contains($data, ['png', 'PNG']) => 'png',
                default => 'jpg',
            };
        } catch (Exception $e) {
            Log::error($e->getMessage());
            throw $e;
        }
    }
}
  
