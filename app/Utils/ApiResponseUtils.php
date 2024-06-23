<?php
namespace App\Utils;

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

    public static function success($data)
    {
        return self::make('Permintaan berhasil!', $data);
    }

    public static function failed()
    {
        return self::make('Permintaan gagal!', null, 500);
    }

    public static function notFound()
    {
        return self::make('Data tidak ditemukan!', null, 404);
    }

    public static function registerSuccess()
    {
        return self::make('Registrasi berhasil, silahkan login!', null, 201);
    }

    public static function registerFailed()
    {
        return self::make('Registrasi gagal, coba lagi!', null, 500);
    }

    public static function duplicateUserEntry(){
        return self::make('Username atau email sudah terdaftar!', null, 401);
    }

    public static function loginSuccess($customer)
    {
        return self::make('Login berhasil!', $customer);
    }

    public static function loginFailed()
    {
        return self::make('Username atau password salah!', null, 401);
    }

    public static function validateFail()
    {
        return self::make('Mohon isi field dengan benar!', null, 401);
    }
}
?>