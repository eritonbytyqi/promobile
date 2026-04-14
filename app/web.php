<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\BrandsController;
use App\Http\Controllers\Admin\CategoriesController;
use App\Http\Controllers\Admin\CustomersController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrdersController as AdminOrdersController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StockController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\PaymentController;
use App\Http\Controllers\Client\ProfileController;
use App\Http\Controllers\Client\ShopController;
use App\Http\Controllers\NewsletterController;

/*
|--------------------------------------------------------------------------
| CLIENT ROUTES — Storefront publik
|--------------------------------------------------------------------------
*/

Route::get('/', [ShopController::class, 'home'])->name('home');
Route::get('/shop', [ShopController::class, 'products'])->name('shop');
Route::get('/shop/search-live', [ShopController::class, 'liveSearch']);
Route::get('/shop/{id}', [ShopController::class, 'productDetail'])->name('shop.product');

// Faqet statike
Route::get('/terms',   fn() => view('client.pages.terms'))->name('terms');
Route::get('/privacy', fn() => view('client.pages.privacy'))->name('privacy');
Route::get('/contact', fn() => view('client.pages.contact'))->name('contact');
Route::post('/contact', [ShopController::class, 'sendContact'])->name('contact.send');
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

/*
|--------------------------------------------------------------------------
| CART ROUTES
|--------------------------------------------------------------------------
*/

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/',        [CartController::class, 'index'])->name('index');
    Route::get('/sidebar', [CartController::class, 'sidebar'])->name('sidebar');
    Route::get('/checkout',[CartController::class, 'checkout'])->name('checkout');
    Route::post('/add',    [CartController::class, 'add'])->name('add');
    Route::post('/update', [CartController::class, 'update'])->name('update');
    Route::post('/remove', [CartController::class, 'remove'])->name('remove');
    Route::post('/clear',  [CartController::class, 'clear'])->name('clear');
});

/*
|--------------------------------------------------------------------------
| ORDER & PAYMENT ROUTES
|--------------------------------------------------------------------------
*/

Route::post('/checkout/place', [ShopController::class, 'placeOrder'])->name('checkout.place');

// Pagesa bankare (sandbox)
Route::get('/bank/pay/{order}',  [ShopController::class, 'bankSandbox'])->name('bank.sandbox');
Route::post('/bank/confirm',     [ShopController::class, 'bankConfirm'])->name('bank.confirm');

// Stripe
Route::get('/payment/{order}',         [PaymentController::class, 'page'])->name('payment.page');
Route::post('/payment/intent',         [PaymentController::class, 'createIntent'])->name('payment.intent');
Route::post('/payment/confirm',        [PaymentController::class, 'confirm'])->name('payment.confirm');
Route::post('/payment/refund/{order}', [PaymentController::class, 'refund'])->name('payment.refund');

// Sukses
Route::get('/orders/success/{id}', [ShopController::class, 'orderSuccess'])->name('order.success');

/*
|--------------------------------------------------------------------------
| PROFILE ROUTES — vetëm për user të kyçur
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profili-im',           [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profili-im',          [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/dashboard', fn() => redirect()->route('home'))->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Resources standarde
    Route::resource('products',   ProductsController::class);
    Route::resource('categories', CategoriesController::class);
    Route::resource('orders',     AdminOrdersController::class);
    Route::resource('brands',     BrandsController::class);

    // Produkte — veprime shtesë
    Route::delete('products/bulk-delete',                          [ProductsController::class, 'bulkDelete'])->name('products.bulk-delete');
    Route::delete('products/{product}/images/{image}',             [ProductsController::class, 'deleteImage'])->name('products.images.delete');
    Route::post('products/{product}/images/{image}/primary',       [ProductsController::class, 'setPrimaryImage'])->name('products.images.primary');
    Route::get('get-brands-by-category/{category}',                [ProductsController::class, 'getBrandsByCategory'])->name('products.brands-by-category');

    // Porositë — bulk delete
    Route::delete('orders/bulk-delete', [AdminOrdersController::class, 'bulkDelete'])->name('orders.bulk-delete');

    // Stoku
    Route::get('stock',                   [StockController::class, 'index'])->name('stock.index');
    Route::get('stock/low',               [StockController::class, 'lowStock'])->name('stock.low');
    Route::get('stock/{product}/history', [StockController::class, 'history'])->name('stock.history');
    Route::patch('stock/variant/{id}',    [StockController::class, 'updateVariantStock'])->name('stock.variant.update');
    Route::patch('stock/product/{id}',    [StockController::class, 'updateProductStock'])->name('stock.product.update');

    // Bannerat & Ofertat
    Route::get('banners',         [BannerController::class, 'index'])->name('banners.index');
    Route::get('banners/create',  [BannerController::class, 'create'])->name('banners.create');
    Route::post('banners',        [BannerController::class, 'store'])->name('banners.store');
    Route::delete('banners/{id}', [BannerController::class, 'destroy'])->name('banners.destroy');
    Route::post('banners/offer',  [BannerController::class, 'storeOffer'])->name('banners.offer');
    Route::delete('offers/{id}',  [BannerController::class, 'destroyOffer'])->name('offers.destroy');

    // Klientët
    Route::get('customers',                   [CustomersController::class, 'index'])->name('customers.index');
    Route::get('customers/{customer}/orders', [CustomersController::class, 'orders'])->name('customers.orders');

    // Cilësimet
    Route::get('settings',           [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings',          [SettingController::class, 'update'])->name('settings.update');
    Route::post('settings/delivery', [SettingController::class, 'saveDelivery'])->name('settings.delivery');
});

/*
|--------------------------------------------------------------------------
| AUTH ROUTES — Breeze
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
