<?php


namespace App\Helpers;

class PaginatedHelpers
{
    public static function paginationDetails($data)
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
