<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Discount;
use App\Utils\ApiResponseUtils;
use Illuminate\Http\Request;

class ProductController extends Controller {
    public static function index(Request $request)
    {
        // Eager load the discount relationship
        $products = Product::where('status', 1)
            ->with('discount') // Correct eager loading
            ->get();

        $categories = Category::all();
        $discounts = Discount::all();

        // Format the product data to include discount details
        $products = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'category_id' => $product->category_id,
                'description' => $product->description,
                'image' => $product->image,
                'price' => $product->price,
                'stock' => $product->stock,
                'weight' => $product->weight,
                'status' => $product->status,
                'discount_id' => $product->discount_id,
                'discount_name' => $product->discount ? $product->discount->discount_name : null,
                'besar_diskon' => $product->discount ? $product->discount->besar_diskon : null,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at,
                'category' => $product->category,
            ];
        });

        return ApiResponseUtils::make('Permintaan berhasil!', compact('products', 'categories', 'discounts'));
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
