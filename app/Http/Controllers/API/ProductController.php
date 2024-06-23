<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Utils\ApiResponseUtils;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public static function index(Request $request) {
        return ApiResponseUtils::make('Permintaan berhasil!', array(
                'products' => Product::all(),
                'categories' => Category::all()));
    }

    public static function detail(Request $request, $id) {
        $product = Product::find($id);
        if ($product){
            $product->category = $product->category();
            return ApiResponseUtils::make('Permintaan berhasil!', $product);
        }else{
            return ApiResponseUtils::notFound();
        }
        
    }
}
