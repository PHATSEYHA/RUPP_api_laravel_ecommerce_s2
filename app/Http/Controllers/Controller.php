<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function res_success($message = '', $data = null)
    {
        return response()->json([
            'result' => true, 
            'message' => $message, 
            'data' => $data
        ]);
    }

    public function Respone_paginate($page, $message = '', $data = null)
    {
        return response()->json([
            'result' => true,
            'message' => $message,
            'data' => $data,
            'paginate' => [
                'totle' => $page->total(),
                'total_page' => $page->lastPage(),
                'curent_page' => $page->currentPage(),
                'has_more_page' => $page->hasMorePages(),
                'has_page' => $page->hasPages(),
            ]
        ]);
    }
}
