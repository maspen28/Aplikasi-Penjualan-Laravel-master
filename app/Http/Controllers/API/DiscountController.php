<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller {
    public function applyDiscount(Request $request) {
        $validated = $request->validate([
            'cart_total' => 'required|numeric',
            'discount_id' => 'nullable|string',
        ]);

        $cartTotal = $validated['cart_total'];
        $discountId = $validated['discount_id'];

        $discountAmount = 0;

        if ($discountId) {
            // Find discount by ID
            $discount = Discount::where('id', $discountId)->first();

            if ($discount) {
                $discountAmount = $discount->besar_diskon;
            } else {
                return response()->json(['message' => 'Invalid discount ID'], 400);
            }
        }

        // Calculate total after discount
        $totalAfterDiscount = $cartTotal - $discountAmount;

        return response()->json([
            'cart_total' => $cartTotal,
            'discount_amount' => $discountAmount,
            'total_after_discount' => $totalAfterDiscount,
        ]);
    }
}
