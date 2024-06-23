<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function show($id)
{
    $customer = Customer::find($id);
    if ($customer) {
        return view('costumer.show', compact('customer'));
    } else {
        // Handle if customer not found
        return redirect()->route('customers.index')->withErrors('Customer not found');
    }
}

}
