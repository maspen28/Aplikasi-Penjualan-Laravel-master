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
    // Config::$serverKey = '';
    // Config::$isProduction = false;
    // Config::$isSanitized = true;
    // Config::$is3ds = true;
  }

  public function createPayment(Request $request, $orderId) {
    $order = Order::find($orderId);

    if (!$order) {
      return response()->json(['message' => 'Order not found'], 404);
    }

    $params = [
      'transaction_details' => [
        'order_id' => $order->invoice,
        'gross_amount' => $order->cost, // total pembayaran
      ],
      'customer_details' => [
        'first_name' => $order->customer_name,
        'email' => $order->customer_email,
        'phone' => $order->customer_phone,
      ],
    ];

    $snapToken = Snap::getSnapToken($params);
    return response()->json(['snap_token' => $snapToken]);
  }

  public function notificationHandler(Request $request) {
    $notif = new Notification();

    $transaction = $notif->transaction_status;
    $type = $notif->payment_type;
    $orderId = $notif->order_id;
    $fraud = $notif->fraud_status;

    $order = Order::where('invoice', $orderId)->first();

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
