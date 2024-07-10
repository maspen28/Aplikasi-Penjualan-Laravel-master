<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller {
  public function show($id) {
    $customer = Customer::find($id);
    if ($customer) {
      return view('costumer.show', compact('customer'));
    } else {
      // Handle if customer not found
      return redirect()->route('customers.index')->withErrors('Customer not found');
    }
  }

  public function index() {
    $totalCustomers = Customer::count();
    $totalProducts = Product::count();
    $monthlyRevenue = Order::whereMonth('created_at', date('m'))->sum('cost'); // pastikan ini float/integer
    $ordersToShip = Order::where('status', 3)->count();

        // Query untuk mengambil data jumlah produk yang terjual dari orders_detail
        $soldProducts = OrderDetail::selectRaw('product_id, SUM(qty) as total')
            ->groupBy('product_id')
            ->get()
            ->pluck('cost', 'product_id');

        // Ambil daftar nama produk (bisa disiapkan untuk digunakan jika diperlukan)
        $productNames = $soldProducts->keys();

        // Jumlah produk yang terjual
        $soldQuantities = $soldProducts->values();

        // Kirim data ke view 'dashboard'
        return view('dashboard', compact(
            'totalCustomers', 'totalProducts', 'monthlyRevenue', 'ordersToShip',
            'soldQuantities', 'productNames'
        ));
  }

    public function getData(Request $request)
    {
        // Ambil bulan dan tahun dari request, defaultnya saat ini jika tidak ada
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        // Buat tanggal mulai dan akhir berdasarkan bulan dan tahun yang dipilih
        $startDate = Carbon::createFromDate($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        // Query untuk mengambil data jumlah produk yang terjual dari orders_detail
        $soldProducts = OrderDetail::whereHas('order', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->selectRaw('product_id, SUM(qty) as total')
            ->groupBy('product_id')
            ->get()
            ->pluck('cost', 'product_id');

        // Ambil daftar nama produk (bisa disiapkan untuk digunakan jika diperlukan)
        $productNames = $soldProducts->keys();

        // Jumlah produk yang terjual
        $soldQuantities = $soldProducts->values();

        return view('dashboard', [
            'soldQuantities' => $soldQuantities,
            'productNames' => $productNames,
        ]);
    }
}
