<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function index()
    {
        return view('frontend.index');
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
        return view('frontend.shop');
    }
}
