<?php

namespace App\Http\Controllers\Ecommerce;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class FrontController extends Controller
{
    public function index()
    {
        $products = Product::where('status', 1)
                        ->orderBy('created_at', 'DESC')
                        ->paginate(8);

        return view('costumer.index', compact('products'));
    }


    public function product()
    {
        $products = Product::where('status', 1)
                        ->orderBy('created_at', 'DESC')
                        ->paginate(12);

        return view('costumer.produk', compact('products'));
    }


    public function categoryProduct($slug)
    {
        $products = Category::where('slug', $slug)->first()->product()->orderBy('created_at', 'DESC')->paginate(12);

        return view('costumer.produk', compact('products'));
    }

    public function show($slug)
    {
        $product = Product::with(['category'])->where('slug', $slug)->first();

        return view('costumer.show', compact('product'));
    }
}
