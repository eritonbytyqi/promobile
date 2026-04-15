<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
class HomeController extends Controller
{
  public function index()
{
    $hasBanners = Schema::hasTable('banners');
    $hasCategories = Schema::hasTable('categories');
    $hasProducts = Schema::hasTable('products');

    $banners = $hasBanners
        ? Banner::where('active', 1)->orderBy('sort_order')->get()
        : collect();

    $categories = $hasCategories
        ? Category::orderBy('name')->get()
        : collect();

    $featured = $hasProducts
        ? Product::with('images')->where('featured', 1)->latest()->take(8)->get()
        : collect();

    $latest = $hasProducts
        ? Product::with('images')->latest()->take(8)->get()
        : collect();

    $totalProducts = $hasProducts ? Product::count() : 0;
    $totalCategories = $hasCategories ? Category::count() : 0;

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