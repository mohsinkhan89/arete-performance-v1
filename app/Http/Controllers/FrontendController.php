<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;

class FrontendController extends Controller
{
    public function index()
    {
        return view('frontend.index', [
            'categories' => Category::withCount('products')->where('status', 'active')->orderBy('sort_order')->take(8)->get(),
            'featuredProducts' => Product::with('category')->where('status', 'active')->where('is_featured', true)->latest()->take(5)->get(),
            'reviews' => Review::where('status', 'active')->latest()->take(5)->get(),
        ]);
    }

    public function myCart()
    {
        return view('frontend.my-cart');
    }

    public function checkout()
    {
        return view('frontend.checkout');
    }

    public function orderSuccess()
    {
        return view('frontend.order-success');
    }

    public function productDetails()
    {
        return view('frontend.product-details');
    }

    public function search()
    {
        return view('frontend.search');
    }

    public function shop()
    {
        $products = Product::with('category')->where('status', 'active')->latest()->paginate(12);

        return view('frontend.shop', [
            'categories' => Category::withCount(['products' => fn ($query) => $query->where('status', 'active')])->where('status', 'active')->orderBy('sort_order')->get(),
            'products' => $products,
        ]);
    }
}
