<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Order;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function show($id)
{
    $customer = Customer::find($id);
    if ($customer) {
        return view('costumer.show', compact('customer'));
    } else {
        // Handle if customer not found
        return redirect()->route('customers.index')->withErrors('Customer not found');
    }

    
}

public function index()
{
    $totalCustomers = Customer::count();
    $totalProducts = Product::count();
    $monthlyRevenue = Order::whereMonth('created_at', date('m'))->sum('subtotal');
    $ordersToShip = Order::where('status', 2)->count();

    // Menghitung omset harian
    $dailyOrders = Order::selectRaw('DATE(created_at) as date, SUM(subtotal) as total')
        ->whereDate('created_at', '>=', Carbon::now()->subDays(7)) // Contoh: Ambil data 7 hari terakhir
        ->groupBy('date')
        ->orderBy('date')
        ->get();

    $dailyLabels = $dailyOrders->pluck('date')->map(function ($date) {
        return Carbon::parse($date)->format('d M');
    });

    $dailyRevenue = $dailyOrders->pluck('total');

    // Menghitung omset bulanan
    $monthlyOrders = Order::selectRaw('DATE_FORMAT(created_at, "%b %Y") as month, SUM(subtotal) as total')
        ->whereYear('created_at', Carbon::now()->year)
        ->groupBy('month')
        ->orderBy('month')
        ->get();

    $monthlyLabels = $monthlyOrders->pluck('month');
    $monthlyRevenue = $monthlyOrders->pluck('total');

    return view('dashboard', compact('totalCustomers', 'totalProducts', 'monthlyRevenue', 'ordersToShip'));
}



}
