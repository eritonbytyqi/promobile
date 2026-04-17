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
            [
                'stock'     => $request->stock ?? 0,
                'featured'  => $request->boolean('featured'),
                'is_active' => $request->boolean('is_active'),
            ]
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
            $request->only(['name', 'description', 'price', 'sale_price', 'stock', 'category_id', 'brand_id', 'subcategory']),
            [
                'stock'     => $request->stock ?? 0,
                'featured'  => $request->boolean('featured'),
                'is_active' => $request->boolean('is_active'),
            ]
        ));

        $this->saveImages($request, $product, isUpdate: true);

        $product->specs()->delete();
        $this->saveSpecs($request, $product);

        // ✅ FIX: fresh load nga DB para sync — eliminon cache të vjetër
        $product->load('variantImages');
        $this->syncVariantImages($request, $product);

        $product->variants()->delete();
        $this->saveVariants($request, $product);

        $this->saveLinkedProducts($request, $product);

        return $product->fresh();
    }

    private function saveLinkedProducts(Request $request, Product $product): void
    {
        $linkedIds = array_filter((array) $request->input('linked_product_ids', []));

        \DB::table('product_accessories')
            ->where('accessory_id', $product->id)
            ->delete();

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
            if ($isPrimary && $isUpdate) {
                $product->images()->update(['is_primary' => false]);
            }
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
            if ($key && $value) {
                $product->specs()->create(['spec_key' => $key, 'spec_value' => $value]);
            }
        }
    }

   private function saveVariants(Request $request, Product $product): void
{
    foreach ($request->variants ?? [] as $vi => $variantInput) {
        $vi        = (int) $vi;
        $colorName = trim($variantInput['color_name'] ?? '');
        $colorHex  = trim($variantInput['color_hex']  ?? '');

        $storages = $variantInput['storages'] ?? [];

        if (!empty($storages)) {
            // Ka storage rows — krijo një rekord për çdo storage
            foreach ($storages as $sd) {
                $storageName = ($sd['storage'] ?? '') === 'custom'
                    ? trim($sd['storage_custom'] ?? '')
                    : trim($sd['storage'] ?? '');

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
        } else {
            // ✅ S'ka storage — krijo variant bazë pa storage
            $product->variants()->create([
                'color_name'  => $colorName,
                'color_hex'   => $colorHex,
                'storage'     => null,
                'base_price'  => 0,
                'extra_price' => 0,
                'price'       => 0,
                'sale_price'  => null,
                'stock'       => 0,
            ]);
        }

        $this->saveVariantImages($request, $product, $vi, $colorName, $colorHex);
    }
}

    private function saveVariantImages(
        Request $request,
        Product $product,
        int     $vi,
        string  $colorName,
        string  $colorHex
    ): void {
        // 1. Ruaj fotot e REJA të ngarkuara
        $newFiles = $request->file("variants.{$vi}.images") ?? [];
        foreach ($newFiles as $idx => $file) {
            if (!$file || !$file->isValid()) continue;
            ProductVariantImage::create([
                'product_id' => $product->id,
                'color_name' => $colorName,
                'color_hex'  => $colorHex,
                'image_path' => $file->store('variant-images', 'public'),
                'is_primary' => $idx === 0 && !$this->hasVariantImages($product->id, $colorHex),
                'sort_order' => $idx,
            ]);
        }

        // 2. ✅ FIX: Ekzistueset — UPDATE ngjyrën nëse u ndryshua, krijo nëse mungon
        $existingPaths = array_values(array_filter((array) ($request->variants[$vi]['existing_images'] ?? [])));
        foreach ($existingPaths as $idx => $path) {
            $img = ProductVariantImage::where('product_id', $product->id)
                ->where('image_path', $path)
                ->first();

            if ($img) {
                // Ekziston — vetëm update color nëse u ndryshua
                $img->update([
                    'color_name' => $colorName,
                    'color_hex'  => $colorHex,
                ]);
            } else {
                // Nuk ekziston (u fshi aksidentalisht) — rikrijoje
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

    // Helper: ka imazhe kjo ngjyrë tashmë?
    private function hasVariantImages(int $productId, string $colorHex): bool
    {
        return ProductVariantImage::where('product_id', $productId)
            ->where('color_hex', $colorHex)
            ->exists();
    }

    private function syncVariantImages(Request $request, Product $product): void
    {
        // Mbledh të gjitha path-et që duhet MBAJTUR (nga hidden inputs)
        $kept = collect($request->variants ?? [])
            ->flatMap(fn($v) => array_values(array_filter((array) ($v['existing_images'] ?? []))))
            ->unique()
            ->all();

        // ✅ FIX: punon me fresh-loaded collection (nga $product->load('variantImages') në update())
        $product->variantImages->each(function ($img) use ($kept) {
            if (!in_array($img->image_path, $kept, true)) {
                Storage::disk('public')->delete($img->image_path);
                $img->delete();
            }
        });
    }
}