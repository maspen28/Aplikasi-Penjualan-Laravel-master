<?php

namespace App\Http\Controllers;

use App\Models\Citie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use App\Models\Customer;
use App\Models\Province;
use App\Models\District;

class CostumerRegistriController extends Controller
{
    use RegistersUsers;
    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegisterForm()
    {
        if (Auth::guard('costumer')->check()) {
        return redirect('/costumer/home');
        } else {
        return view('costumerAuth.register');
        }
    }

    public function getCity()
    {
        $cities = Citie::where('province_id', request()->province_id)->get();
        return response()->json(['status' => 'success', 'data' => $cities]);
    }

    public function getDistrict()
    {
        $districts = District::where('city_id', request()->city_id)->get();
        return response()->json(['status' => 'success', 'data' => $districts]);
    }

    public function createCustomer(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers,email',
            'password' => 'required|string|min:8|confirmed',
            'username' => 'required|string|max:15|unique:customers,username',
            'address' => 'required|string|max:255',
        ]);

        $customer = new Customer();
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->password = bcrypt($request->password);
        $customer->username = $request->username;
        $customer->address = $request->address;
        $customer->save();

        return redirect('/costumer/login')->with('alert-success', 'You have successfully registered');
    }


    

    // protected function updateFormCostumer(Request $request){

    // }

    // protected function updateCostumer(Request $request){

    // }

    // // public function createCostumer(Array $input){
    // //     Validator::make($input, [
    // //         'name' => ['required', 'string', 'max:255'],
    // //         'email' => ['required', 'string', 'email', 'max:255', 'unique:customers'],
    // //         'password' => $this->passwordRules(),
    // //     ])->validate();

    // //     return Customer::create([
    // //         'name' => $input['name'],
    // //         'email' => $input['email'],
    // //         'password' => Hash::make($input['password']),
    // //     ]);
    // // }
}
