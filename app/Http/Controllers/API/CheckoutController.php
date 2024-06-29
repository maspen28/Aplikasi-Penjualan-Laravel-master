<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use Exception;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;

class CheckoutController extends Controller {
  public function __construct() {
    Config::$serverKey = config('services.midtrans.serverKey');
    Config::$isProduction = config('services.midtrans.isProduction');
    Config::$isSanitized = config('services.midtrans.is_sanitized');
    Config::$is3ds = config('services.midtrans.is_3ds');
  }

  public function checkout(Request $request) {
    $customer_id = $request->input('customer_id');
    $cartItems = Cart::where('customer_id', $customer_id)->get();

    if ($cartItems->isEmpty()) {
      return response()->json(['status' => 'failed', 'message' => 'Cart is empty or invalid customer_id']);
    }

    // Ambil data customer
    $customer = Customer::find($customer_id);
    if (!$customer) {
      return response()->json(['status' => 'failed', 'message' => 'Customer not found']);
    }

    try {
      \DB::beginTransaction();

      $subtotal = 0;
      foreach ($cartItems as $cartItem) {
        $subtotal += ($cartItem->product->price * $cartItem->qty);
      }

      $order = Order::create([
        'invoice' => uniqid('INV-'),
        'customer_id' => $customer_id,
        'customer_name' => $customer->name, // Mengambil data dari tabel customers
        'customer_address' => $customer->address, // Mengambil data dari tabel customers
        'customer_phone' => $customer->phone, // Mengambil data dari tabel customers
        'cost' => $subtotal,
        'shipping' => 0,
        'status' => 1,
        'subtotal' => $subtotal,
      ]);

      $orderDetails = [];
      foreach ($cartItems as $cartItem) {
        $orderDetails[] = [
          'order_id' => $order->id,
          'product_id' => $cartItem->product_id,
          'price' => $cartItem->product->price,
          'qty' => $cartItem->qty,
          'weight' => $cartItem->product->weight,
          'created_at' => now(),
          'updated_at' => now(),
        ];
      }

      OrderDetail::insert($orderDetails);
      $order->save();

      $cartItems->each->delete();

      \DB::commit();

      $params = [
        'transaction_details' => [
          'order_id' => $order->invoice, // pastikan ini adalah 'invoice' dan bukan 'id'
          'gross_amount' => $order->cost,
        ],
        'customer_details' => [
          'first_name' => $order->customer_name,
          'email' => $customer->email, // Mengambil data dari tabel customers
          'phone' => $order->customer_phone,
        ],
      ];

      $snapToken = Snap::getSnapToken($params);

      return response()->json(['status' => 'success', 'snap_token' => $snapToken]);
    } catch (Exception $e) {
      \DB::rollBack();
      return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
    }
  }
}
