<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSpec;
use App\Models\ProductVariantImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function store(Request $request): Product
    {
        $product = Product::create(array_merge(
          $request->only(['name', 'description', 'price', 'sale_price', 'stock', 'category_id', 'brand_id', 'subcategory']),
            ['stock' => $request->stock ?? 0, 'featured' => $request->boolean('featured'), 'is_active' => $request->boolean('is_active')]
        ));

        $this->saveImages($request, $product);
        $this->saveSpecs($request, $product);
        $this->saveVariants($request, $product);
        $this->saveLinkedProducts($request, $product);

        return $product;
    }

 public function update(Request $request, Product $product): Product
{
    $product->update(array_merge(
        $request->only(['name', 'description', 'price', 'sale_price', 'stock', 'category_id', 'brand_id', 'subcategory']), // ← SHTO 'subcategory'
        ['stock' => $request->stock ?? 0, 'featured' => $request->boolean('featured'), 'is_active' => $request->boolean('is_active')]
    ));

        $this->saveImages($request, $product, isUpdate: true);
        $product->specs()->delete();
        $this->saveSpecs($request, $product);
        $this->syncVariantImages($request, $product);
        $product->variants()->delete();
        $this->saveVariants($request, $product);
        $this->saveLinkedProducts($request, $product);

        return $product->fresh();
    }

    // ── LIDHJA E AKSESORIT ME PRODUKTE KRYESORE ─────────────────
    //
    // Logjika: ky produkt (aksesor) lidhet me shumë produkte kryesore.
    // Në tabelën product_accessories:
    //   product_id    = produkti kryesor (p.sh. iPhone 17 Pro Max)
    //   accessory_id  = ky aksesor (p.sh. Kalikë MagSafe)
    //
    // Pra për çdo produkt_id që zgjedh, bëjmë sync nga ana e tij.
    //
    private function saveLinkedProducts(Request $request, Product $product): void
    {
        $linkedIds = array_filter((array) $request->input('linked_product_ids', []));

        // Hiq lidhjet e vjetra të këtij aksesori nga të gjithë produktet kryesore
        // (fshi rreshtat ku accessory_id = $product->id)
        \DB::table('product_accessories')
            ->where('accessory_id', $product->id)
            ->delete();

        // Rishto lidhjet e reja
        foreach ($linkedIds as $mainProductId) {
            \DB::table('product_accessories')->insertOrIgnore([
                'product_id'   => (int) $mainProductId,
                'accessory_id' => $product->id,
                'sort_order'   => 0,
            ]);
        }
    }

    public function delete(Product $product): string
    {
        if ($product->orderItems()->exists()) {
            $product->update(['is_active' => false]);
            return 'deactivated';
        }

        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->image_path);
        }

        $product->variantImages->each(function ($img) {
            Storage::disk('public')->delete($img->image_path);
        });

        $product->variants->pluck('image_path')
            ->filter()
            ->unique()
            ->each(fn($path) => Storage::disk('public')->delete($path));

        $product->images()->delete();
        $product->variantImages()->delete();
        $product->variants()->delete();
        $product->specs()->delete();

        // Pastro lidhjet nga pivot (si produkt kryesor dhe si aksesor)
        \DB::table('product_accessories')
            ->where('product_id', $product->id)
            ->orWhere('accessory_id', $product->id)
            ->delete();

        $product->delete();

        return 'deleted';
    }

    public function bulkDelete(array $ids): array
    {
        $products = Product::with(['images', 'variants', 'variantImages', 'specs', 'orderItems'])
            ->whereIn('id', $ids)
            ->get();

        $deleted = $deactivated = 0;

        foreach ($products as $product) {
            $result = $this->delete($product);
            if ($result === 'deleted')     $deleted++;
            if ($result === 'deactivated') $deactivated++;
        }

        return ['deleted' => $deleted, 'deactivated' => $deactivated, 'total' => $products->count()];
    }

    public function deleteImage(ProductImage $image): void
    {
        Storage::disk('public')->delete($image->image_path);
        $wasPrimary = $image->is_primary;
        $productId  = $image->product_id;
        $image->delete();
        if ($wasPrimary) {
            $next = ProductImage::where('product_id', $productId)->first();
            if ($next) $next->update(['is_primary' => true]);
        }
    }

    public function setPrimaryImage(int $productId, int $imageId): void
    {
        ProductImage::where('product_id', $productId)->update(['is_primary' => false]);
        ProductImage::where('product_id', $productId)->where('id', $imageId)->update(['is_primary' => true]);
    }

    private function saveImages(Request $request, Product $product, bool $isUpdate = false): void
    {
        if (!$request->hasFile('images')) return;
        $primaryIndex  = (int) $request->input('primary_index', 0);
        $existingCount = $isUpdate ? $product->images()->count() : 0;
        $hasNoPrimary  = $isUpdate ? !$product->images()->where('is_primary', true)->exists() : true;

        foreach ($request->file('images') as $i => $file) {
            $isPrimary = ($i === $primaryIndex) && (!$isUpdate || $hasNoPrimary || $existingCount === 0);
            if ($isPrimary && $isUpdate) $product->images()->update(['is_primary' => false]);
            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $file->store('products', 'public'),
                'is_primary' => $isPrimary,
                'sort_order' => $existingCount + $i,
            ]);
        }
    }

    private function saveSpecs(Request $request, Product $product): void
    {
        foreach ($request->specs ?? [] as $spec) {
            $key   = trim($spec['key']   ?? '');
            $value = trim($spec['value'] ?? '');
            if ($key && $value) $product->specs()->create(['spec_key' => $key, 'spec_value' => $value]);
        }
    }

    private function saveVariants(Request $request, Product $product): void
    {
        foreach ($request->variants ?? [] as $vi => $variantInput) {
            $vi        = (int) $vi;
            $colorName = $variantInput['color_name'] ?? '';
            $colorHex  = $variantInput['color_hex']  ?? '';

            foreach ($variantInput['storages'] ?? [] as $sd) {
                $storageName = $sd['storage'] === 'custom'
                    ? ($sd['storage_custom'] ?? '')
                    : ($sd['storage'] ?? '');

                $base  = (float) ($sd['base_price']  ?? 0);
                $extra = (float) ($sd['extra_price'] ?? 0);

                $product->variants()->create([
                    'color_name'  => $colorName,
                    'color_hex'   => $colorHex,
                    'storage'     => $storageName,
                    'base_price'  => $base,
                    'extra_price' => $extra,
                    'price'       => $base + $extra,
                    'sale_price'  => !empty($sd['sale_price']) ? (float) $sd['sale_price'] : null,
                    'stock'       => (int) ($sd['stock'] ?? 0),
                ]);
            }

            $this->saveVariantImages($request, $product, $vi, $colorName, $colorHex);
        }
    }

    private function saveVariantImages(Request $request, Product $product, int $vi, string $colorName, string $colorHex): void
    {
        foreach ($request->file("variants.{$vi}.images") ?? [] as $idx => $file) {
            if (!$file || !$file->isValid()) continue;
            ProductVariantImage::create([
                'product_id' => $product->id,
                'color_name' => $colorName,
                'color_hex'  => $colorHex,
                'image_path' => $file->store('variant-images', 'public'),
                'is_primary' => $idx === 0,
                'sort_order' => $idx,
            ]);
        }
        foreach (array_filter((array) ($request->variants[$vi]['existing_images'] ?? [])) as $idx => $path) {
            if (!ProductVariantImage::where('product_id', $product->id)->where('image_path', $path)->exists()) {
                ProductVariantImage::create([
                    'product_id' => $product->id,
                    'color_name' => $colorName,
                    'color_hex'  => $colorHex,
                    'image_path' => $path,
                    'is_primary' => $idx === 0,
                    'sort_order' => $idx,
                ]);
            }
        }
    }

    private function syncVariantImages(Request $request, Product $product): void
    {
        $kept = collect($request->variants ?? [])
            ->flatMap(fn($v) => array_values(array_filter((array) ($v['existing_images'] ?? []))))
            ->all();

        $product->variantImages->each(function ($img) use ($kept) {
            if (!in_array($img->image_path, $kept)) {
                Storage::disk('public')->delete($img->image_path);
                $img->delete();
            }
        });
    }
}