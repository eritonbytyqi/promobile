<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Banner;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $banners         = Banner::where('active', 1)->orderBy('sort_order')->get();
        $categories      = Category::orderBy('name')->get();
        $featured = Product::with('images')->where('featured', 1)->latest()->take(8)->get();
        $latest   = Product::with('images')->latest()->take(8)->get();
        $totalProducts   = Product::count();
        $totalCategories = Category::count();

        return view('client.home', compact(
            'banners',
            'categories',
            'featured',
            'latest',
            'totalProducts',
            'totalCategories'
        ));
    }
public function saveDelivery(Request $request)
{
    cache()->forever('delivery_kosovo',   $request->delivery_kosovo   ?? 'Dërgesa Falas');
    cache()->forever('delivery_albania',  $request->delivery_albania  ?? 'Dërgesa 5€');
    cache()->forever('delivery_macedonia',$request->delivery_macedonia ?? 'Dërgesa 3€');
    cache()->forever('delivery_serbia',   $request->delivery_serbia   ?? 'Dërgesa 4€');

    return back()->with('delivery_success', 'Dërgesa u ruajt me sukses!');
}
}