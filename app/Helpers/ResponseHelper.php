<?php

namespace App\Helpers;

class ResponseHelper
{
    public static function success($data = null, $message = null, $page = null)
    {
        return response()->json([
            'code' => 200,
            'data' => $data,
            'message' => $message,
            'errors' => null,
            'page' => $page,
        ]);
    }

    public static function error($message = 'Terjadi kesalahan.', $errors = [], $code = 400)
    {
        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => null,
            'errors' => $errors,
            'page' => null,
        ], $code);
    }
}
