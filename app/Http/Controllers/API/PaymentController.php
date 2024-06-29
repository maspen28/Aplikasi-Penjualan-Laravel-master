<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;

class PaymentController extends Controller {
  public function __construct() {
    // Set konfigurasi Midtrans
    Config::$serverKey = config('services.midtrans.serverKey');
    Config::$isProduction = config('services.midtrans.isProduction');
    Config::$isSanitized = config('services.midtrans.is_sanitized');
    Config::$is3ds = config('services.midtrans.is_3ds');
  }

  public function createPayment(Request $request, $orderId) {
    $order = Order::find($orderId);

    if (!$order) {
      return response()->json(['message' => 'Order not found'], 404);
    }

    $params = [
      'transaction_details' => [
        'order_id' => $order->id, // Menggunakan 'id' sebagai 'order_id'
        'gross_amount' => $order->cost, // total pembayaran
      ],
      'customer_details' => [
        'first_name' => $order->customer->name,
        'email' => $order->customer->email,
        'phone' => $order->customer->phone,
      ],
    ];

    $snapToken = Snap::getSnapToken($params);
    return response()->json(['snap_token' => $snapToken]);
  }

  public function notificationHandler(Request $request) {
    $notif = new Notification();

    $transaction = $notif->transaction_status;
    $type = $notif->payment_type;
    $orderId = $notif->order_id; // Menggunakan 'order_id' dari Midtrans
    $fraud = $notif->fraud_status;

    $order = Order::find($orderId); // Menggunakan 'id' untuk mencari order

    if (!$order) {
      return response()->json(['message' => 'Order not found'], 404);
    }

    if ($transaction == 'capture') {
      if ($type == 'credit_card') {
        if ($fraud == 'challenge') {
          // Update order status menjadi challenge
          $order->update(['status' => 'challenge']);
        } else {
          // Update order status menjadi success
          $order->update(['status' => 'success']);
        }
      }
    } else if ($transaction == 'settlement') {
      // Update order status menjadi success
      $order->update(['status' => 'success']);
    } else if ($transaction == 'pending') {
      // Update order status menjadi pending
      $order->update(['status' => 'pending']);
    } else if ($transaction == 'deny') {
      // Update order status menjadi deny
      $order->update(['status' => 'failed']);
    } else if ($transaction == 'expire') {
      // Update order status menjadi expired
      $order->update(['status' => 'expired']);
    } else if ($transaction == 'cancel') {
      // Update order status menjadi cancel
      $order->update(['status' => 'cancel']);
    }

    return response()->json(['message' => 'Notification processed']);
  }
}

?>
