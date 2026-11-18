<?php

namespace App\Traits;

trait SetReponses
{
    protected static function success($data, $ifPaginate = false)
    {
        $per_page = $ifPaginate ? $data->perPage() : count($data);
        $current_page = $ifPaginate ? $data->currentPage() : 1;
        $total = $ifPaginate ? $data->total() : count($data);
        $next = $ifPaginate ? $data->hasMorePages() : false;
        $row = $ifPaginate ? $data->items() : $data;
        $last_page = $ifPaginate ? $data->lastPage() : 1;
        $prev = $ifPaginate ? ($data->previousPageUrl() !== null) : false;
        $from = $ifPaginate ? $data->firstItem() : 0;
        $to = $ifPaginate ? $data->lastItem() : 0;

        $meta = [
            'per_page'     => $per_page,
            'current_page' => $current_page,
            'from'         => $from,
            'to'           => $to,
            'prev'         => $prev,
            'next'         => $next,
            'last'         => $last_page,
            'total'        => $total,
            'timestamp'    => date('Y-m-d H:i:s'),
        ];

        return response()->json([
            'status'  => true,
            'message' => 'Data retrieved successfully',
            'data'    => $row,
            'meta'    => $meta,
        ], 200);
    }

    protected static function error($message, $code = 400)
    {
        return response()->json([
            'status'  => false,
            'message' => $message,
            'data'    => [],
        ], $code);
    }

    protected static function crudSuccess($data, $message = 'Success')
    {
        return response()->json([
            'status'  => true,
            'message' => $message,
            'data'    => $data,
        ], 200);
    }
}
