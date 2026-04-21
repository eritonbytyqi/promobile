<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Repositories\ProductRepository;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
class ProductsController extends Controller
{
    public function __construct(
        protected ProductService    $products,
        protected ProductRepository $repo,
    ) {}

    private function findByUuid(string $uuid): Product
    {
        return Product::where('uuid', $uuid)->firstOrFail();
    }

public function index(Request $request)
{
    return view('admin.products.index', [
        'products'   => $this->repo->allPaginated(
            perPage:  25,
            search:   $request->q,
            category: $request->category,
            brand:    $request->brand,
            status:   $request->status,
        ),
        'categories' => Cache::remember('all_categories', 3600, fn() => Category::all()),
        'brands'     => Cache::remember('all_brands', 3600, fn() => Brand::all()),
    ]);
}
    public function create()
    {
        $accessoryCategoryIds = Category::accessories()->pluck('id');

        $mainProducts = Product::whereNotIn('category_id', $accessoryCategoryIds)
            ->where('is_active', true)
            ->select('id', 'name', 'category_id')
            ->with('category:id,name')
            ->orderBy('name')
            ->get();

        return view('admin.products.create', [
            'categories'           => Category::all(),
            'brands'               => Brand::all(),
            'accessoryCategoryIds' => $accessoryCategoryIds,
            'mainProducts'         => $mainProducts,
            'categorySubcategories' => [],
        ]);
    }

    public function store(Request $request)
    {
        $request->validate($this->rules());

        if (!$request->filled('name') || !$request->filled('category_id')) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Emri dhe kategoria janë të detyrueshme.');
        }

        $this->products->store($request);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produkti u krijua me sukses!');
    }

    public function show(string $uuid)
    {
        $product = $this->findByUuid($uuid);

        $product->load([
            'category', 'brand', 'images', 'specs',
            'variants', 'variantImages',
            'accessories.images', 'accessories.category',
            'accessoryOf.images', 'accessoryOf.category',
        ]);

        return view('admin.products.show', compact('product'));
    }

    public function edit(string $uuid)
    {
        $product = $this->findByUuid($uuid);

        // ✅ FIX: load ALL relations që nevojiten nga blade + service
        $product->load([
            'images',
            'variants',
            'variantImages',
            'specs',
            'accessoryOf',
            'brand',
            'category',
        ]);

        $accessoryCategoryIds = Category::accessories()->pluck('id');

        $mainProducts = Product::whereNotIn('category_id', $accessoryCategoryIds)
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->select('id', 'name', 'category_id')
            ->with('category:id,name')
            ->orderBy('name')
            ->get();

        $categorySubcategories = $product->category_id
            ? \App\Models\CategorySubcategory::where('category_id', $product->category_id)
                ->orderBy('sort_order')
                ->pluck('name')
                ->toArray()
            : [];

        return view('admin.products.create', [
            'product'               => $product,
            'categories'            => Category::all(),
            'brands'                => Brand::all(),
            'accessoryCategoryIds'  => $accessoryCategoryIds,
            'mainProducts'          => $mainProducts,
            'categorySubcategories' => $categorySubcategories,
        ]);
    }

    public function update(Request $request, string $uuid)
    {
        $request->validate($this->rules(required: true));
        $product = $this->findByUuid($uuid);
        $this->products->update($request, $this->repo->findWithRelations($product->id));
        return redirect()->route('admin.products.index')
            ->with('success', 'Produkti u përditësua me sukses!');
    }

    public function destroy(string $uuid)
    {
        $product = $this->findByUuid($uuid);

        $result = $this->products->delete(
            Product::with(['images', 'variants', 'variantImages', 'orderItems'])
                ->findOrFail($product->id)
        );

        $message = $result === 'deleted'
            ? 'Produkti u fshi!'
            : 'Produkti u çaktivizua sepse ekziston në porosi.';

        return redirect()->route('admin.products.index')->with('success', $message);
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('product_ids', []);

        if (empty($ids)) {
            return redirect()->route('admin.products.index')
                ->with('error', 'Nuk u zgjodh asnjë produkt.');
        }

        $result  = $this->products->bulkDelete($ids);
        $message = '';

        if ($result['deleted']     > 0) $message .= "{$result['deleted']} produkte u fshinë. ";
        if ($result['deactivated'] > 0) $message .= "{$result['deactivated']} produkte u çaktivizuan sepse ekzistojnë në porosi.";

        return redirect()->route('admin.products.index')->with('success', trim($message));
    }

    public function deleteImage($productUuid, $imageId)
    {
        $product = $this->findByUuid($productUuid);
        $this->products->deleteImage(
            ProductImage::where('product_id', $product->id)->where('id', $imageId)->firstOrFail()
        );
        return response()->json(['success' => true]);
    }

    public function setPrimaryImage($productUuid, $imageId)
    {
        $product = $this->findByUuid($productUuid);
        $this->products->setPrimaryImage($product->id, $imageId);
        return response()->json(['success' => true]);
    }

    public function getBrandsByCategory($categoryId)
    {
        $category = Category::with('brands')->find($categoryId);
        return response()->json($category ? $category->brands : []);
    }

    private function rules(bool $required = false): array
    {
        $r = $required ? 'required' : 'nullable';
        return [
            'name'                             => "{$r}|string|max:255",
            'description'                      => 'nullable|string',
            'price'                            => 'nullable|numeric|min:0',
            'sale_price'                       => 'nullable|numeric|min:0',
            'stock'                            => 'nullable|integer|min:0',
            'category_id'                      => "{$r}|exists:categories,id",
            'brand_id'                         => 'nullable|exists:brands,id',
            'images'                           => 'nullable|array',
            'images.*'                         => 'nullable|image|mimes:jpeg,png,jpg,webp,avif|max:4096',
            'primary_index'                    => 'nullable|integer|min:0',
            'specs'                            => 'nullable|array',
            'specs.*.key'                      => 'nullable|string|max:255',
            'specs.*.value'                    => 'nullable|string|max:1000',
            'variants'                         => 'nullable|array',
            'variants.*.color_name'            => 'nullable|string|max:255',
            'variants.*.color_hex'             => 'nullable|string|max:20',
            'variants.*.images'                => 'nullable|array',
            'variants.*.images.*'              => 'nullable|image|mimes:jpeg,png,jpg,webp,avif|max:4096',
            'variants.*.existing_images'       => 'nullable|array',
            'variants.*.existing_images.*'     => 'nullable|string|max:500',
            'variants.*.storages'              => 'nullable|array',
            'variants.*.storages.*.storage'    => 'nullable|string|max:50',
            'variants.*.storages.*.base_price' => 'nullable|numeric|min:0',
            'variants.*.storages.*.extra_price'=> 'nullable|numeric|min:0',
            'variants.*.storages.*.stock'      => 'nullable|integer|min:0',
            'linked_product_ids'               => 'nullable|array',
            'linked_product_ids.*'             => 'nullable|exists:products,id',
            'subcategory'                      => 'nullable|string|max:100',
            'featured'                         => 'nullable|boolean',
            'is_active'                        => 'nullable|boolean',
            'allow_preorder'      => 'nullable|boolean',
            'preorder_note'       => 'nullable|string|max:255',
            'low_stock_threshold' => 'nullable|string|max:100',
        ];
    }
    public function toggle(string $uuid)
{
    $product = Product::where('uuid', $uuid)->firstOrFail();
    $product->update(['is_active' => !$product->is_active]);

    return redirect()->back()->with('success',
        $product->is_active ? 'Produkti u aktivizua!' : 'Produkti u çaktivizua!'
    );
}
}   