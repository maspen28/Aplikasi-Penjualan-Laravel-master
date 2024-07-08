<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\City;
use App\Models\District;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kavist\RajaOngkir\Facades\RajaOngkir;
use Midtrans\Config;
use Midtrans\Snap;

class OrderController extends Controller {
  public function checkout() {
    $cart = Cart::where('customer_id', Auth::guard('costumer')->user()->id)->get();

    if ($cart->isEmpty()) {
      return redirect()->route('home.list_cart')->with('error', 'Keranjang belanja Anda kosong.');
    }

    $subtotal = collect($cart)->sum(function ($q) {
      return $q->qty * $q->cart_price;
    });

    $weight = collect($cart)->sum(function ($q) {
      return $q->qty * $q->cart_weight;
    });

    $provinces = Province::all();
    $provinceId = $provinces->first()->id; // Get the ID of the first province

    // Fetch cities and districts based on the first province
    $cities = City::where('province_id', $provinceId)->get();
    $districts = District::where('city_id', $cities->first()->id)->get();
    $cost = null;

    return view('costumer.checkout', compact('cart', 'subtotal', 'weight', 'cost', 'provinces', 'cities', 'districts'));
  }

  public function getCities($province_id) {
    $cities = City::where('province_id', $province_id)->pluck('name', 'id');
    return response()->json($cities);
  }

  public function calculateShippingCost(Request $request) {
    $origin = 257; // Ganti dengan ID kota asal yang sesuai
    $destination = $request->district_id; // pastikan request berisi district_id
    $weight = $request->weight; // Berat dalam gram
    $courier = 'jne'; // Kode kurir (jne, pos, tiki, dll)

    $cost = RajaOngkir::ongkosKirim([
      'origin' => $origin,
      'destination' => $destination,
      'weight' => $weight,
      'courier' => $courier,
    ])->get();

    return response()->json($cost);
  }

  public function processCheckout(Request $request) {
    $cart = Cart::where('customer_id', Auth::guard('costumer')->user()->id)->get();

    $order = Order::create([
      'invoice' => $request->invoice,
      'customer_id' => Auth::guard('costumer')->user()->id,
      'customer_name' => $request->customer_name,
      'customer_phone' => $request->customer_phone,
      'customer_address' => $request->customer_address,
      'district_id' => $request->district_id,
      'city_id' => $request->city_id,
      'province_id' => $request->province_id,
      'subtotal' => $request->subtotal,
      'cost' => $request->cost,
      'shipping' => $request->shipping,
      'status' => $request->status,
    ]);

    // Simpan detail order
    foreach ($cart as $row) {
      OrderDetail::create([
        'order_id' => $order->id,
        'product_id' => $row->product_id,
        'price' => $row->cart_price,
        'qty' => $row->qty,
        'weight' => $row->cart_weight,
      ]);
    }

    // Kurangi stok produk
    foreach ($cart as $row) {
      $product = Product::find($row->product_id);
      $product->update([
        'stock' => $product->stock - $row->qty,
      ]);
    }

    // Hapus cart setelah checkout
    Cart::where('customer_id', Auth::guard('costumer')->user()->id)->delete();

    // Konfigurasi Midtrans dan buat transaksi
    Config::$serverKey = env('MIDTRANS_SERVER_KEY');
    Config::$isProduction = false;
    Config::$isSanitized = true;
    Config::$is3ds = true;

    $params = [
      'transaction_details' => [
        'order_id' => $order->invoice,
        'gross_amount' => $request->subtotal + $request->cost,
      ],
      'customer_details' => [
        'first_name' => $request->customer_name,
        'phone' => $request->customer_phone,
        'address' => $request->customer_address,
      ],
      'item_details' => $cart->map(function ($item) {
        return [
          'id' => $item->product_id,
          'price' => $item->cart_price,
          'quantity' => $item->qty,
          'name' => $item->product->name,
        ];
      })->toArray(),
    ];

    try {
      $snapToken = Snap::getSnapToken($params);
      return view('checkout.complete', compact('snapToken', 'order'));
    } catch (\Exception $e) {
      return redirect()->back()->with('error', $e->getMessage());
    }
  }

  public function index($invoice) {
    $cart = Cart::where('customer_id', Auth::guard('costumer')->user()->id)->get();
    $order = Order::where('invoice', $invoice)->first();
    $order_detail = OrderDetail::where('order_id', $order->id)->first();

    $subtotal = collect($cart)->sum(function ($q) {
      return $q->qty * $q->cart_price;
    });

    $weight = collect($cart)->sum(function ($q) {
      return $q->qty * $q->cart_weight;
    });

    return view('customer.order', compact('cart', 'order', 'subtotal', 'weight', 'order_detail'));
  }
}

?>
