<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class CostumersController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/costumer/home';

    public function __construct()
    {
      $this->middleware('guest')->except('logout');
    }

    public function guard()
    {
     return Auth::guard('costumer');
    }

    public function showLoginForm(){
        if (Auth::guard('costumer')->check()) {
            return redirect('/costumer/home');
        }
        else {
            return view('costumerAuth.login');
        }
    }

    private function loginFailed(){
        return redirect()
            ->back()
            ->withInput()
            ->with('error','Login failed, please try again!');
    }
    public function show($id)
{
    $customer = Customer::find($id);

    if ($customer) {
        return view('costumer.show', compact('customer'));
    } else {
        // Handle jika customer tidak ditemukan
        return redirect()->route('customers.index')->withErrors('Customer not found');
    }
}


}
