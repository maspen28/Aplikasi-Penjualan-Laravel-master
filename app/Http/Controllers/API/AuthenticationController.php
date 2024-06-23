<?php

namespace App\Http\Controllers\Api;

use App\Utils\ApiResponseUtils;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller
{
    public function login(Request $request){
        $validatedData = $request->validate([
            'username' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);
        if (Auth::attempt($validatedData)) {
            $token = Auth::user()->createToken('remember_token')->plainTextToken;
            return ApiResponseUtils::loginSuccess($token);
        }
        return ApiResponseUtils::loginFailed();
    }
}
?>