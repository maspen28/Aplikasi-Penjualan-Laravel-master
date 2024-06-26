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
                        ->select('products.id as product_id', 'products.name as nama_produk', 'products.price', 'products.weight', 'carts.qty')
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
    $id = $request->input('product_id');
    $customer_id = $request->input('customer_id');
    $cart = Cart::where('product_id', $id)->where('customer_id', $customer_id)->get();
    if ($cart) {
      if (is_array($cart)) {
        foreach ($cart as $c) {
          $c->delete();
        }
      } else {
        $cart->delete();
      }

      return ApiResponseUtils::success();
    } else {
      return ApiResponseUtils::failed();
    }

  }

  public static function checkout(Request $request) {
    $id = $request->input('product_id');
    $customer_id = $request->input('customer_id');
    $cart = Cart::where('customer_id', $customer_id);
    if ($id) {
      $cart->where('product_id', $id);
    }

    $cart = $cart->get();

    if ($cart) {
      if (is_array($cart)) {
        foreach ($cart as $c) {
          Order::create([
            'product_id' => $c->product_id,
            'customer_id' => $c->customer_id,
            'qty' => $c->qty,
          ]);
          $c->delete();
        }
      } else {
        Order::create([
          'product_id' => $c->product_id,
          'customer_id' => $c->customer_id,
          'qty' => $c->qty,
        ]);
        $cart->delete();
      }
      return ApiResponseUtils::success();
    } else {
      return ApiResponseUtils::failed();
    }

  }
}
