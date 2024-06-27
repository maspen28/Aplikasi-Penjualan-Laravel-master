<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller; // Tambahkan baris ini
use App\Models\City;
use App\Models\District;
use App\Models\Province;

class LocationController extends Controller {

  public function getDistrict($provinceId, $cityId) {
    $province = Province::find($provinceId);
    $city = City::find($cityId);

    if ($province && $city) {
      $districts = District::where('province_id', $provinceId)
        ->where('city_id', $cityId)
        ->get();

      $districtsWithPostalcode = $districts->map(function ($district) {
        return [
          'id' => $district->id,
          'name' => $district->name,
          'postalcode' => $district->postalcode, // Assuming 'postalcode' is an attribute of the District model
        ];
      });

      return response()->json([
        'province' => $province->name,
        'city' => $city->name,
        'districts' => $districtsWithPostalcode,
      ]);
    } else {
      return response()->json(['message' => 'Province or City not found'], 404);
    }
  }

  public function getCity($provinceId) {
    $province = Province::find($provinceId);
    if ($province) {
      $cities = City::where('province_id', $provinceId)->get();

      return response()->json([
        'province' => $province,
        'cities' => $cities,
      ]);
    } else {
      return response()->json(['message' => 'Province not found'], 404);
    }
  }

  public function getProvinces() {
    $provinces = Province::all();
    return response()->json($provinces);
  }

}
