<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Product;


class BrandsController extends Controller
{
    public function index()
    {
        return view('admin.brands.index', ['brands' => Brand::with('categories')->withCount(['products','categories'])->latest()->get(), 'categories' => Category::orderBy('name')->get()]);
    }

    public function create() { return $this->index(); }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255', 'slug' => 'nullable|string|max:255|unique:brands,slug', 'description' => 'nullable|string', 'category_ids' => 'nullable|array', 'category_ids.*' => 'exists:categories,id']);
        $brand = Brand::create(['name' => $request->name, 'slug' => Str::slug($request->filled('slug') ? $request->slug : $request->name), 'description' => $request->description, 'is_active' => 1]);
        $brand->categories()->sync($request->category_ids ?? []);
        return redirect()->route('admin.brands.index')->with('success', 'Brendi u shtua!');
    }

    public function edit($id)
    {
        return view('admin.brands.index', ['brands' => Brand::withCount(['products','categories'])->latest()->get(), 'categories' => Category::orderBy('name')->get(), 'editBrand' => Brand::with('categories')->findOrFail($id)]);
    }

    public function update(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);
        $request->validate(['name' => 'required|string|max:255', 'slug' => 'nullable|string|max:255|unique:brands,slug,'.$brand->id, 'description' => 'nullable|string', 'category_ids' => 'nullable|array', 'category_ids.*' => 'exists:categories,id']);
        $brand->update(['name' => $request->name, 'slug' => Str::slug($request->filled('slug') ? $request->slug : $request->name), 'description' => $request->description]);
        $brand->categories()->sync($request->category_ids ?? []);
        return redirect()->route('admin.brands.index')->with('success', 'Brendi u përditësua!');
    }

   public function destroy($id)
{
    $brand = Brand::findOrFail($id);

    $hasProducts = Product::where('brand_id', $brand->id)->exists();

    if ($hasProducts) {
        return redirect()->route('admin.brands.index')
            ->with('error', 'Ky brend ka produkte të lidhura dhe nuk mund të fshihet!');
    }

    $brand->delete();

    return redirect()->route('admin.brands.index')
        ->with('success', 'Brendi u fshi!');
}
}
