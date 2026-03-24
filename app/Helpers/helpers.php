<?php

use Symfony\Component\HttpFoundation\Response;

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