<?php
namespace App\Services;

    use App\Models\Banner;
    use App\Models\Category;
    use App\Models\Offer;
    use App\Models\Product;
    use Illuminate\Http\Request;
    use Illuminate\Support\Collection;
    use Illuminate\Support\Facades\Schema;
    class ShopService
    {
    public function homeData(): array
    {
        $hasCategories = Schema::hasTable('categories');
        $hasBanners = Schema::hasTable('banners');
        $hasOffers = Schema::hasTable('offers');
        $hasProducts = Schema::hasTable('products');

        $accCat = $hasCategories ? Category::accessories()->first() : null;
        $accCatId = $accCat?->id;

        $accessories = ($accCat && $hasProducts)
            ? $accCat->products()
                ->with(['images', 'category'])
                ->where('is_active', true)
                ->latest()
                ->take(8)
                ->get()
            : collect();

        return [
            'banners' => $hasBanners
    ? Banner::with('product')->where('active', 1)->orderBy('sort_order')->get()
    : collect(),

            'categories' => $hasCategories
                ? Category::orderBy('name')->get()
                : collect(),

            'specialOffer' => $hasOffers
                ? Offer::where('is_active', true)->orderBy('sort_order')->latest()->first()
                : null,

            'featured' => $hasProducts
                ? Product::with(['images', 'category', 'brand', 'variants'])
                    ->where('is_active', true)
                    ->where('featured', 1)
                    ->when($accCatId, fn($q) => $q->where('category_id', '!=', $accCatId))
                    ->latest()
                    ->take(8)
                    ->get()
                : collect(),

            'latest' => $hasProducts
                ? Product::with(['images', 'category', 'brand', 'variants'])
                    ->where('is_active', true)
                    ->where('featured', 1)
                    ->when($accCatId, fn($q) => $q->where('category_id', '!=', $accCatId))
                    ->latest()
                    ->take(8)
                    ->get()
                : collect(),

            'accessories' => $accessories,
            'accessoriesCategory' => $accCat,

            'totalProducts' => $hasProducts
                ? Product::where('is_active', true)->count()
                : 0,

            'totalCategories' => $hasCategories
                ? Category::count()
                : 0,
        ];
    }


    public function productList(Request $request): array
    {
        $query = Product::with(['category', 'brand', 'images', 'variants'])
            ->where('is_active', true);

        if ($request->category)    $query->where('category_id', $request->category);
        if ($request->brand)       $query->where('brand_id', $request->brand);
        if ($request->subcategory) $query->where('subcategory', $request->subcategory);
        if ($request->featured)    $query->where('featured', 1);  // ← RIKTHE KËTË
        if ($request->search)      $query->where(fn($q) => $q
            ->where('name', 'like', '%' . $request->search . '%')
            ->orWhere('description', 'like', '%' . $request->search . '%')
        );

        match($request->sort) {
            'price_asc'  => $query->orderBy('price'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc'   => $query->orderBy('name'),
            default      => $query->latest(),
        };

        $activeCategory = $request->category
            ? Category::with('brands')->find($request->category)
            : null;

        $categoryBrands = $activeCategory?->brands ?? collect();

        $activeBrand = $request->brand
            ? \App\Models\Brand::find($request->brand)
            : null;

        $subcategories = collect();
        if ($request->category) {
            $subcatQuery = Product::where('is_active', true)
                ->where('category_id', $request->category)
                ->whereNotNull('subcategory')
                ->where('subcategory', '!=', '');
            if ($request->brand) $subcatQuery->where('brand_id', $request->brand);
            $subcategories = $subcatQuery->distinct()->orderBy('subcategory')->pluck('subcategory');
        }

    $brandSubcategories = [];
    if ($request->category) {
        $subs = \App\Models\CategorySubcategory::where('category_id', $request->category)
            ->orderBy('sort_order')
            ->pluck('name')
            ->toArray();

        if (!empty($subs)) {
            foreach ($categoryBrands as $b) {
                $brandSubcategories[$b->id] = $subs; // ← të gjitha brendet marrin të njëjtat nënkategori
            }
        }
    }
        // ← SHTO EDHE KËTË: nënkategoritë pa brand
    

        // ── FEATURED PRODUCTS për seksionin e veçantë ──────────────
        $featuredProducts = null;
        $noFiltersActive  = !$request->anyFilled(['search', 'category', 'featured', 'brand', 'subcategory']);

        if ($noFiltersActive) {
            $featuredProducts = Product::with(['images', 'variants', 'category', 'brand'])
                ->where('is_active', true)
                ->where('featured', 1)
                ->latest()
                ->take(8)
                ->get();
        }
        // ────────────────────────────────────────────────────────────

        $perPage = $request->featured ? 999 : 12;

        return [
            'products'           => $query->paginate($perPage)->withQueryString(),
            'categories'         => Category::all(),
            'activeCategory'     => $activeCategory,
            'categoryBrands'     => $categoryBrands,
            'activeBrand'        => $activeBrand,
            'subcategories'      => $subcategories,
            'activeSubcat'       => $request->subcategory,
            'brandSubcategories' => $brandSubcategories,
            'featuredProducts'   => $featuredProducts,  // ← E RE
        ];
    }
    public function productDetail(int $id): array
    {
        $product = Product::with([
            'category:id,name',
            'brand:id,name',
            'images'         => fn($q) => $q->orderByDesc('is_primary')->orderBy('id'),
            'variants'       => fn($q) => $q->orderBy('color_name')->orderBy('storage')->orderBy('id'),
            'variantImages'  => fn($q) => $q->orderByDesc('is_primary')->orderBy('sort_order')->orderBy('id'),
            'specs'          => fn($q) => $q->orderBy('id'),
            // ── SHTO KËTË ──
            'accessories'    => fn($q) => $q->with([
                                    'images'   => fn($q2) => $q2->orderByDesc('is_primary')->orderBy('id'),
                                    'variants' => fn($q2) => $q2->orderBy('id'),
                                ])->where('is_active', true),
        ])
        ->where('is_active', true)
        ->findOrFail($id);
    
        return [
            'product' => $product,
            'related' => $this->relatedProducts($product),
        ];
    }
    
    

        public function liveSearch(string $q): Collection
        {
            return Product::with(['images'])->where('is_active',true)->where('name','like','%'.$q.'%')->latest()->take(6)->get()
                ->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'price' => number_format($p->sale_price && $p->sale_price < $p->price ? $p->sale_price : $p->price, 2), 'img' => ($img = $p->images?->firstWhere('is_primary',true) ?? $p->images?->first()) ? asset('storage/'.$img->image_path) : null]);
        }

    private function relatedProducts(Product $product): \Illuminate\Support\Collection
    {
        // Nëse produkti ka aksesorë të lidhur drejtpërdrejt → kthei ato
        if ($product->accessories->isNotEmpty()) {
            return $product->accessories;
        }
    
        // Nëse ky produkt ËSHTË aksesor → shfaq produktet me të cilat është lidhur
        // (p.sh. kur shikon faqen e kalikës, shfaq iPhone-ët me të cilin lidhet)
        $linkedParents = $product->accessoryOf()
            ->with([
                'images'   => fn($q) => $q->orderByDesc('is_primary')->orderBy('id'),
                'variants' => fn($q) => $q->orderBy('id'),
            ])
            ->where('is_active', true)
            ->take(6)
            ->get();
    
        if ($linkedParents->isNotEmpty()) {
            return $linkedParents;
        }
    
        // Fallback — produkte nga e njëjta kategori (logjika e vjetër)
        $with = [
            'images'   => fn($q) => $q->orderByDesc('is_primary')->orderBy('id'),
            'variants' => fn($q) => $q->orderBy('id'),
        ];
    
        return Product::with($with)
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->latest('id')
            ->take(6)
            ->get();
    }
    }
