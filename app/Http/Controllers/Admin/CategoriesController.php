<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\CategorySubcategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoriesController extends Controller
{
    public function index()
    {
        return view('admin.categories.index', [
            'categories' => Category::withCount(['products', 'brands'])->latest()->get(),
            'brands'     => Brand::orderBy('name')->get(),
        ]);
    }

    public function create() { return $this->index(); }

    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'slug'          => 'nullable|string|max:255|unique:categories,slug',
            'description'   => 'nullable|string',
            'brand_ids'     => 'nullable|array',
            'brand_ids.*'   => 'exists:brands,id',
            'subcategories' => 'nullable|array',
            'subcategories.*' => 'nullable|string|max:100',
        ]);

        $category = Category::create([
            'name'        => $request->name,
            'slug'        => $request->slug ?: Str::slug($request->name),
            'icon'        => 'fa-' . ltrim($request->icon ?? 'tag', 'fa-'),
            'description' => $request->description,
        ]);

        $category->brands()->sync($request->brand_ids ?? []);
        $this->saveSubcategories($request, $category);

        return redirect()->route('admin.categories.index')->with('success', 'Kategoria u shtua!');
    }

    public function edit($id)
    {
        return view('admin.categories.index', [
            'categories'   => Category::withCount(['products', 'brands'])->latest()->get(),
            'brands'       => Brand::orderBy('name')->get(),
            'editCategory' => Category::with(['brands', 'subcategories'])->findOrFail($id),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'slug'          => 'nullable|string|max:255|unique:categories,slug,' . $id . ',id',
            'description'   => 'nullable|string',
            'brand_ids'     => 'nullable|array',
            'brand_ids.*'   => 'exists:brands,id',
            'subcategories' => 'nullable|array',
            'subcategories.*' => 'nullable|string|max:100',
        ]);

        $category = Category::findOrFail($id);
        $category->update([
            'name'        => $request->name,
            'slug'        => $request->slug ?: Str::slug($request->name),
            'icon'        => 'fa-' . ltrim($request->icon ?? ltrim($category->icon ?? 'tag', 'fa-'), 'fa-'),
            'description' => $request->description,
        ]);

        $category->brands()->sync($request->brand_ids ?? []);

        // Fshi të vjetrat dhe rishto të rejat
        $category->subcategories()->delete();
        $this->saveSubcategories($request, $category);

        return redirect()->route('admin.categories.index')->with('success', 'Kategoria u përditësua!');
    }

    public function destroy($id)
    {
        $category = Category::withCount('products')->findOrFail($id);
        if ($category->products_count > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', "Nuk mund të fshish — ka {$category->products_count} produkte!");
        }
        $category->brands()->detach();
        $category->subcategories()->delete();
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Kategoria u fshi!');
    }

    // ── API: Merr nënkategoritë e një kategorie (për JS në formën e produktit) ──
    public function getSubcategories($categoryId)
    {
        $subcategories = CategorySubcategory::where('category_id', $categoryId)
            ->orderBy('sort_order')
            ->pluck('name');

        return response()->json($subcategories);
    }

    private function saveSubcategories(Request $request, Category $category): void
    {
        $subs = array_filter(array_map('trim', $request->subcategories ?? []));
        foreach (array_values($subs) as $i => $name) {
            CategorySubcategory::create([
                'category_id' => $category->id,
                'name'        => $name,
                'sort_order'  => $i,
            ]);
        }
    }
}