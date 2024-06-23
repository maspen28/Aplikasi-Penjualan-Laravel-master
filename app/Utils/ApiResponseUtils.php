<?php
namespace App\Utils\ApiResponseUtils;

class ApiResponseUtils
{
    public static function make($message, $data = null, $status = 200)
    {
        return response()->json([
            'data' => $data,
            'message' => $message,
            'status' => $status
        ]);
    }

    public static function loginSuccess($token)
    {
        return self::make('Login berhasil!', $token);
    }

    public static function loginFailed()
    {
        return self::make('Login gagal!', null, 401);
    }
}
?>