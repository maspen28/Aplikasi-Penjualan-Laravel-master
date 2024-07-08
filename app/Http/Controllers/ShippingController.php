<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ShippingController extends Controller {
  public function calculateShippingCost(Request $request) {
    $key = env('RAJAONGKIR_API_KEY');
    $response = Http::withHeaders([
      'key' => $key,
    ])->post('https://api.rajaongkir.com/starter/cost', [
      'origin' => env('ORIGIN_CITY_ID'), // Set your origin city ID here
      'destination' => $request->district_id,
      'weight' => $request->weight,
      'courier' => 'jne', // You can set other couriers here
    ]);

    return response()->json($response->json()['rajaongkir']['results']);
  }
}

