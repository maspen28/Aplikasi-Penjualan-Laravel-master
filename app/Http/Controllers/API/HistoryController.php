<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class HistoryController extends Controller {
  public function getHistory(Request $request) {
    $customer_id = $request->input('customer_id');

    if (!$customer_id) {
      return response()->json(['status' => 'failed', 'message' => 'Customer ID is required'], 400);
    }

    $orders = Order::with('details.product')
      ->where('customer_id', $customer_id)
      ->orderBy('created_at', 'desc')
      ->get();

    if ($orders->isEmpty()) {
      return response()->json(['status' => 'failed', 'message' => 'No orders found for this customer'], 404);
    }

    $orders->transform(function ($order) {
      $order->status_label = $this->getStatusLabel($order->status);
      return $order;
    });

    return response()->json(['status' => 'success', 'orders' => $orders]);
  }

  private function getStatusLabel($status) {
    switch ($status) {
    case 1:
      return 'Dikonfirmasi';
    case 2:
      return 'Sudah Dibayar';
    case 3:
      return 'Dikirim';
    case 4:
      return 'Selesai';
    default:
      return 'Tidak Diketahui';
    }
  }
}
