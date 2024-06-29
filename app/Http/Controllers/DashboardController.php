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
        $monthlyRevenue = Order::whereMonth('created_at', date('m'))->sum('cost'); // pastikan ini float/integer
        $ordersToShip = Order::where('status', 3)->count();

        $dailyRevenue = Order::selectRaw('DATE(created_at) as date, SUM(cost) as total')
            ->groupBy('date')
            ->pluck('total', 'date');

        $dailyLabels = $dailyRevenue->keys();
        $dailyRevenueValues = $dailyRevenue->values();

        $monthlyRevenueData = Order::selectRaw('MONTH(created_at) as month, SUM(cost) as total')
            ->groupBy('month')
            ->pluck('total', 'month');

        $monthlyLabels = $monthlyRevenueData->keys();
        $monthlyRevenueValues = $monthlyRevenueData->values();

        return view('dashboard', compact(
            'totalCustomers', 'totalProducts', 'monthlyRevenue', 'ordersToShip', 
            'dailyLabels', 'dailyRevenueValues', 'monthlyLabels', 'monthlyRevenueValues'
        ));
    }

    public function getData(Request $request) {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        // Query your data based on the selected month and year
        $startDate = Carbon::createFromDate($year, $month, 1);
        $endDate = $startDate->copy()->endOfMonth();

        // Daily revenue
        $dailyRevenue = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(cost) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date');

        $dailyLabels = $dailyRevenue->keys();
        $dailyRevenueValues = $dailyRevenue->values();

        // Monthly revenue
        $monthlyRevenue = Order::whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as month, SUM(cost) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month');

        $monthlyLabels = $monthlyRevenue->keys();
        $monthlyRevenueValues = $monthlyRevenue->values();

        return response()->json([
            'dailyLabels' => $dailyLabels,
            'dailyRevenueValues' => $dailyRevenueValues,
            'monthlyLabels' => $monthlyLabels,
            'monthlyRevenueValues' => $monthlyRevenueValues,
        ]);
    }





}
