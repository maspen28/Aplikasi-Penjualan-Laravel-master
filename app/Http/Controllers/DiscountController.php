<?php
// app/Http/Controllers/DiscountController.php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Product;
use Illuminate\Http\Request;

class DiscountController extends Controller {
  public function index() {
    $discounts = Discount::with('product')->paginate(10);
    $products = Product::all();
    return view('discount.index', compact('discounts', 'products'));
  }

  public function store(Request $request) {
    $request->validate([
      'discount_name' => 'required|string|max:255',
      'product_id' => 'required|exists:products,id',
      'besar_diskon' => 'required|numeric',
    ]);

    Discount::create($request->all());

    return redirect()->route('discount.index')->with('success', 'Diskon berhasil ditambahkan');
  }

  public function update(Request $request, Discount $discount) {
    $request->validate([
      'discount_name' => 'required|string|max:255',
      'product_id' => 'required|exists:products,id',
      'besar_diskon' => 'required|numeric',
    ]);

    $discount->update($request->all());

    return redirect()->route('discount.index')->with('success', 'Diskon berhasil diupdate');
  }

  public function destroy(Discount $discount) {
    $discount->delete();

    return redirect()->route('discount.index')->with('success', 'Diskon berhasil dihapus');
  }
}

?>