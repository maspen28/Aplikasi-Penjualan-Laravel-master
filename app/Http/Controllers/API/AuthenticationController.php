<?php

namespace App\Http\Controllers\API;

use App\Utils\ApiResponseUtils;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Customer;

class AuthenticationController extends Controller
{
    public function register(Request $request)
    {
        $fields = $request->only('email', 'password', 'username', 'name', 'address');
        $fieldsValidate = ['required|email', 'required|min:8', 'required', 'required', 'required'];
        $data = $fields;
        for ($i = 0; $i < count($fields); $i++) {
            $data[array_keys($fields)[$i]] = $fieldsValidate[$i];
        }

        $validator = Validator::make($fields, $data);

        if ($validator->fails()) return ApiResponseUtils::validateFail();
        
        $fields['password'] = Hash::make($fields['password']);

        try{
            if (Customer::insert($fields)) {
                return ApiResponseUtils::registerSuccess();
            } else {
                return ApiResponseUtils::registerFailed();
            }
        }catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if($errorCode == 1062) return ApiResponseUtils::duplicateUserEntry();
            else return ApiResponseUtils::registerFailed();
        }
    }

    public function login(Request $request){
        $fields = $request->only('username', 'password');
        $fieldsValidate = ['required', 'required|min:8'];
        $data = $fields;
        for ($i = 0; $i < count($fields); $i++) {
            $data[array_keys($fields)[$i]] = $fieldsValidate[$i];
        }

        $validator = Validator::make($fields, $data);
        if ($validator->fails()) return ApiResponseUtils::validateFail();

        $customer = Customer::where('username', $request->username)->first();

        if (!$customer || !Hash::check($request->password, $customer->password)) {
            return ApiResponseUtils::loginFailed();
        }

        return ApiResponseUtils::loginSuccess($customer);
    }
}
?>