<?php

namespace App\Services;

use App\Models\Banner;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class BannerService
{
    public function createBanner(Request $request): Banner
    {
        $image = $request->hasFile('banner_image')
            ? $request->file('banner_image')->store('banners', 'public')
            : null;

        $video = $request->hasFile('banner_video')
            ? $request->file('banner_video')->store('banners/videos', 'public')
            : null;

     [$pUrl, $sUrl] = $request->filled('product_id')
    ? (function() use ($request) {
        $product = \App\Models\Product::find($request->product_id);
        $url = $product ? route('shop.product', $product->uuid) : '/shop';
        return [$url, $url];
    })()
    : ['/shop', '/shop'];

        return Banner::create([
            'title'              => $request->banner_title,
            'subtitle'           => $request->banner_subtitle,
            'badge_text'         => $request->banner_badge,
            'image'              => $image,
            'video'              => $video,
            'image_position'     => $request->banner_image_position ?? 'center center',
            'bg_color'           => $request->banner_bg_color ?? '#0a0a1a',
            'btn_primary_text'   => $request->banner_btn_primary_text ?: 'Buy Now',
            'btn_primary_url'    => $pUrl,
            'btn_secondary_text' => $request->banner_btn_secondary_text ?: 'Shiko detajet',
            'btn_secondary_url'  => $sUrl,
            'sort_order'         => $request->banner_sort ?? 0,
            'active'             => 1,
        ]);
    }

    public function deleteBanner(Banner $banner): void
    {
        if ($banner->image && Storage::disk('public')->exists($banner->image)) {
            Storage::disk('public')->delete($banner->image);
        }

        if ($banner->video && Storage::disk('public')->exists($banner->video)) {
            Storage::disk('public')->delete($banner->video);
        }

        $banner->delete();
    }

    public function createOffer(Request $request): Offer
    {
        $data = [
            'title'       => $request->offer_title,
            'subtitle'    => $request->offer_subtitle,
            'badge'       => $request->offer_badge,
            'link_text'   => $request->offer_link_text ?: 'Shiko ofertën →',
            'url'         => $request->offer_url,
            'bg_color'    => $request->offer_bg_color ?: '#0071e3',
            'text_color'  => $request->offer_text_color ?: '#ffffff',
            'sort_order'  => $request->offer_sort ?? 0,
            'product_id'  => $request->offer_product_id,
            'is_active'   => $request->boolean('offer_active', true),
        ];

        if ($request->hasFile('offer_image')) {
            $data['image'] = $request->file('offer_image')->store('offers', 'public');
        }

        return Offer::create($data);
    }

    public function deleteOffer(Offer $offer): void
    {
        if ($offer->image && Storage::disk('public')->exists($offer->image)) {
            Storage::disk('public')->delete($offer->image);
        }

        $offer->delete();
    }

    public function saveUpgradeSection(Request $request): void
    {
        if ($request->hasFile('upgrade_image')) {
            $old = Cache::get('upgrade_image');

            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }

            $path = $request->file('upgrade_image')->store('banners/upgrade', 'public');
            Cache::forever('upgrade_image', $path);
        }

        Cache::forever('upgrade_badge', $request->upgrade_badge);
        Cache::forever('upgrade_title', $request->upgrade_title);
        Cache::forever('upgrade_subtitle', $request->upgrade_subtitle);
        Cache::forever('upgrade_button_text', $request->upgrade_button_text);
        Cache::forever('upgrade_url', $request->upgrade_url ?: '/shop');
        Cache::forever('upgrade_active', $request->boolean('upgrade_active'));
    }
}