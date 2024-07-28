<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller {
  public function index(Request $request) {
    $currentMonth = str_pad($request->input('month', date('m')), 2, '0', STR_PAD_LEFT);
    $currentYear = $request->input('year', date('Y'));

    $totalCustomers = Customer::count();
    $totalProducts = Product::count();
    $monthlyRevenue = Order::whereMonth('created_at', $currentMonth)
      ->whereYear('created_at', $currentYear)
      ->sum('cost');
    $ordersToShip = Order::where('status', 3)->count();

    $soldProducts = OrderDetail::join('products', 'order_details.product_id', '=', 'products.id')
      ->selectRaw('products.name as product_name, SUM(order_details.qty) as total')
      ->whereMonth('order_details.created_at', $currentMonth)
      ->whereYear('order_details.created_at', $currentYear)
      ->groupBy('order_details.product_id', 'products.name')
      ->get()
      ->pluck('total', 'product_name');

    $productNames = $soldProducts->keys()->toArray();
    $soldQuantities = $soldProducts->values()->toArray();

    $chartData = [
      'productNames' => $productNames,
      'soldQuantities' => $soldQuantities,
    ];

    return view('dashboard', compact(
      'totalCustomers', 'totalProducts', 'monthlyRevenue', 'ordersToShip',
      'soldQuantities', 'productNames', 'currentMonth', 'currentYear', 'chartData'
    ));
  }

  public function filter(Request $request) {
    try {
      $month = str_pad($request->input('month'), 2, '0', STR_PAD_LEFT);
      $year = $request->input('year');

      Log::info("Filtering for month: $month and year: $year");

      $totalCustomers = Customer::count();
      $totalProducts = Product::count();
      $monthlyRevenue = Order::whereMonth('created_at', $month)
        ->whereYear('created_at', $year)
        ->sum('cost');
      $ordersToShip = Order::where('status', 3)->count();

      $soldProducts = OrderDetail::join('products', 'order_details.product_id', '=', 'products.id')
        ->selectRaw('products.name as product_name, SUM(order_details.qty) as total')
        ->whereMonth('order_details.created_at', $month)
        ->whereYear('order_details.created_at', $year)
        ->groupBy('order_details.product_id', 'products.name')
        ->get()
        ->pluck('total', 'product_name');

      $productNames = $soldProducts->keys()->toArray();
      $soldQuantities = $soldProducts->values()->toArray();

      Log::info("Data filtered successfully for month: $month and year: $year");

      return response()->json([
        'totalCustomers' => $totalCustomers,
        'totalProducts' => $totalProducts,
        'monthlyRevenue' => $monthlyRevenue,
        'ordersToShip' => $ordersToShip,
        'chartData' => [
          'soldQuantities' => $soldQuantities,
          'productNames' => $productNames,
        ],
      ]);
    } catch (\Exception $e) {
      Log::error('Error in filter method: ' . $e->getMessage());
      return response()->json(['error' => 'Something went wrong'], 500);
    }
  }
}


