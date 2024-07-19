<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller {
  public function applyDiscount(Request $request) {
    $validated = $request->validate([
      'cart_total' => 'required|numeric',
      'discount_name' => 'nullable|string',
    ]);

    $cartTotal = $validated['cart_total'];
    $discountName = $validated['discount_name'];

    $discountAmount = 0;

    if ($discountName) {
      // Cari diskon berdasarkan nama
      $discount = Discount::where('discount_name', $discountName)->first();

      if ($discount) {
        $discountAmount = $discount->besar_diskon;
      } else {
        return response()->json(['message' => 'Invalid discount name'], 400);
      }
    }

    // Hitung total setelah diskon
    $totalAfterDiscount = $cartTotal - $discountAmount;

    return response()->json([
      'cart_total' => $cartTotal,
      'discount_amount' => $discountAmount,
      'total_after_discount' => $totalAfterDiscount,
    ]);
  }
}
