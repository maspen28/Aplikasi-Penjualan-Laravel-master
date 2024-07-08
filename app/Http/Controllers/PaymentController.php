<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentController extends Controller {
  public function processPayment(Request $request) {
    // Set konfigurasi Midtrans
    Config::$serverKey = env('MIDTRANS_SERVER_KEY');
    Config::$isProduction = false;
    Config::$isSanitized = true;
    Config::$is3ds = true;

    // Buat transaksi
    $params = [
      'transaction_details' => [
        'order_id' => $request->order_id,
        'invoice' => uniqid('INV-'),
        'gross_amount' => $request->amount,
      ],
      'customer_details' => [
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'email' => $request->email,
        'phone' => $request->phone,
      ],
    ];

    try {
      $paymentUrl = Snap::createTransaction($params)->redirect_url;
      return redirect($paymentUrl);
    } catch (\Exception $e) {
      return back()->with('error', $e->getMessage());
    }
  }
}
