<?php
namespace App\Http\Controllers;

use App\Models\CustomerAccount;
use Illuminate\Http\Request;

class CustomerAccountController extends Controller {
  public function index() {
    $customers = CustomerAccount::paginate(10);
    return view('akun.akun', compact('customers'));
  }

//   public function create() {
//     return view('auth.akun.create');
//   }

//   public function store(Request $request) {
//     $this->validate($request, [
//       // Define validation rules for storing data
//     ]);

//     CustomerAccount::create([
//       // Assign request data to model fields
//     ]);

//     return redirect()->route('customer.index')->with('success', 'Customer account created successfully');
//   }

//   public function edit($id) {
//     $customer = CustomerAccount::findOrFail($id);
//     return view('auth.akun.edit', compact('customer'));
//   }

//   public function update(Request $request, $id) {
//     $this->validate($request, [
//       // Define validation rules for updating data
//     ]);

//     $customer = CustomerAccount::findOrFail($id);
//     $customer->update([
//       // Assign request data to model fields
//     ]);

//     return redirect()->route('customer.index')->with('success', 'Customer account updated successfully');
//   }

  public function destroy($id) {
    $customer = CustomerAccount::findOrFail($id);
    $customer->delete();

    return redirect()->route('customers.index')->with('success', 'Customer account deleted successfully');
  }
}
