<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Kavist\RajaOngkir\Facades\RajaOngkir;

class ShippingController extends Controller {
  public function calculateShippingCost(Request $request) {
    $origin = $request->input('origin'); // ID kota asal
    $destination = $request->input('destination'); // ID kota tujuan
    $weight = $request->input('weight'); // Berat dalam gram
    $courier = $request->input('courier'); // Kode kurir (jne, pos, tiki, dll)

    $cost = RajaOngkir::ongkosKirim([
      'origin' => $origin,
      'destination' => $destination,
      'weight' => $weight,
      'courier' => $courier,
    ])->get();

    return response()->json($cost);
  }
}
?>
