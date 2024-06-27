<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Utils\ApiResponseUtils;
use Illuminate\Http\Request;

class ProductController extends Controller {
  public static function index(Request $request) {
    return ApiResponseUtils::make('Permintaan berhasil!', array(
      'products' => Product::all(),
      'categories' => Category::all()));
  }

  public static function detail(Request $request, $id) {
    $product = Product::find($id);
    if ($product) {
      $product->category = $product->category();
      return ApiResponseUtils::make('Permintaan berhasil!', $product);
    } else {
      return ApiResponseUtils::notFound();
    }

  }

    public static function cart(Request $request) {
        $customer_id = $request->input('customer_id');
        
        $cartItems = Cart::where('customer_id', $customer_id)
                        ->join('products', 'carts.product_id', '=', 'products.id')
                        ->select('carts.id', 'products.id as product_id', 'products.name as nama_produk', 'products.price', 'products.weight', 'products.image', 'carts.qty')
                        ->get();

        if ($cartItems->isNotEmpty()) {
            return ApiResponseUtils::success($cartItems);
        } else {
            return ApiResponseUtils::failed();
        }
    }

    public static function addToCart(Request $request) {
        $id = $request->input('product_id');
        $qty = $request->input('qty');
        $customer_id = $request->input('customer_id');
        $product = Product::find($id);
        if ($product) {
            if (is_array($product)) {
                foreach ($product as $p) {
                    $cart = $p->addCart($customer_id, $qty);  // Mendefinisikan $cart dalam loop
                }
            } else {
                $cart = $product->addCart($customer_id, $qty);  // Mendefinisikan $cart di sini
            }

            return ApiResponseUtils::success($cart);  // Mengembalikan $cart yang telah didefinisikan
        } else {
            return ApiResponseUtils::failed('Product not found');
        }
    }

    public static function removeFromCart(Request $request) {
        $cart_id = $request->input('id'); // Mengambil cart_id dari request
        $cart = Cart::find($cart_id); // Mencari cart berdasarkan cart_id

        if ($cart) {
            $cart->delete(); // Menghapus cart jika ditemukan
            return ApiResponseUtils::success();
        } else {
            return ApiResponseUtils::failed('Cart item not found');
        }
    }
}
