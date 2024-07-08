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
    $province_id = $request->input('province_id');
    $city_id = $request->input('city_id');
    $district_id = $request->input('district_id');
    $ongkos_kirim = $request->input('ongkos_kirim');
    $customer_phone = $request->input('customer_phone');
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
        'customer_phone' => $customer_phone,
        'shipping' => 0,
        'status' => 1,
        'subtotal' => $subtotal,
        'ongkos_kirim' => $ongkos_kirim,
        'cost' => $subtotal + $ongkos_kirim,
        'city_id' => $city_id,
        'district_id' => $district_id,
        'province_id' => $province_id,

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
        // Mengurangi stok produk
          $product = $cartItem->product;
          $product->stock -= $cartItem->qty;
          $product->save();
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
          'phone' => $customer_phone,
        ],
      ];

      $snapToken = Snap::getSnapToken($params);

      return response()->json([
        'status' => 'success',
        'id_order' => $order->id, // tambahkan id_order
        'invoice' => $order->invoice, // tambahkan invoice
        'customer_name' => $order->customer_name, // tambahkan customer_name
        'customer_address' => $order->customer_address, // tambahkan customer_address
        'customer_phone' => $order->customer_phone, // tambahkan customer_phone
        'cost' => $order->cost, // tambahkan cost
        'ongkos_kirim' => $order->ongkos_kirim, // tambahkan ongkos_kirim
        'snap_token' => $snapToken,
      ]);
    } catch (Exception $e) {
      \DB::rollBack();
      return response()->json(['status' => 'failed', 'message' => $e->getMessage()]);
    }
  }
}
