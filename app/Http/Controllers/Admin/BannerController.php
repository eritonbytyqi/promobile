<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Offer;
use App\Models\Product;
use App\Services\BannerService;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function __construct(protected BannerService $banners)
    {
    }

    public function index()
    {
        return view('admin.banners.create', [
            'products' => Product::where('is_active', true)->orderBy('name')->get(),
            'banners'  => Banner::orderBy('sort_order')->orderByDesc('created_at')->get(),
            'offers'   => Offer::with('product')->orderBy('sort_order')->orderByDesc('created_at')->get(),
        ]);
    }

    public function create()
    {
        return $this->index();
    }

    public function store(Request $request)
    {
        $request->validate([
            'banner_title'              => 'nullable|string|max:255',
            'banner_subtitle'           => 'nullable|string|max:255',
            'banner_badge'              => 'nullable|string|max:255',
            'banner_bg_color'           => 'nullable|string|max:20',
            'banner_btn_primary_text'   => 'nullable|string|max:255',
            'banner_btn_secondary_text' => 'nullable|string|max:255',
            'banner_sort'               => 'nullable|integer|min:0',
            'banner_image'              => 'nullable|image|mimes:jpeg,png,jpg,webp,avif|max:6144',
            'banner_video'              => 'nullable|mimes:mp4,webm|max:20480',
            'banner_image_position'     => 'nullable|string|max:50',
            'product_id'                => 'nullable|exists:products,id',
        ]);

        $this->banners->createBanner($request);

        return redirect()
            ->route('admin.banners.create')
            ->with('success', 'Banneri u krijua me sukses!');
    }

    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        $this->banners->deleteBanner($banner);

        return redirect()
            ->back()
            ->with('success', 'Banneri u fshi me sukses!');
    }

    public function storeOffer(Request $request)
    {
        $request->validate([
            'offer_title'      => 'nullable|string|max:255',
            'offer_subtitle'   => 'nullable|string|max:255',
            'offer_badge'      => 'nullable|string|max:100',
            'offer_link_text'  => 'nullable|string|max:100',
            'offer_url'        => 'nullable|string|max:255',
            'offer_bg_color'   => 'nullable|string|max:20',
            'offer_text_color' => 'nullable|string|max:20',
            'offer_sort'       => 'nullable|integer|min:0',
            'offer_product_id' => 'nullable|exists:products,id',
            'offer_image'      => 'nullable|image|mimes:jpeg,png,jpg,webp,avif|max:4096',
            'offer_active'     => 'nullable|boolean',
        ]);

        $this->banners->createOffer($request);

        return redirect()
            ->back()
            ->with('success', 'Oferta u ruajt me sukses.');
    }

    public function destroyOffer($id)
    {
        $offer = Offer::findOrFail($id);
        $this->banners->deleteOffer($offer);

        return redirect()
            ->back()
            ->with('success', 'Oferta u fshi me sukses.');
    }

public function storeUpgrade(Request $request)
{
    $request->validate([
        'upgrade_badge'       => 'nullable|string|max:100',
        'upgrade_title'       => 'nullable|string|max:255',
        'upgrade_subtitle'    => 'nullable|string|max:500',
        'upgrade_button_text' => 'nullable|string|max:100',
        'upgrade_url'         => 'nullable|string|max:255',
        'upgrade_image'       => 'nullable|image|mimes:jpeg,png,jpg,webp,avif|max:6144',
        'upgrade_active'      => 'nullable|boolean',
    ]);

    $this->banners->saveUpgradeSection($request);

    return redirect()
        ->back()
        ->with('success', 'Upgrade section u ruajt me sukses.');
}
}