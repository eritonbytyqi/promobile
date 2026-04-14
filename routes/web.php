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
use App\Http\Controllers\Admin\AdminProfileController;


/*
|--------------------------------------------------------------------------
| SHOP — Publik
|--------------------------------------------------------------------------
*/
Route::get('/',                    [ShopController::class, 'home'])->name('home');
Route::get('/shop',                [ShopController::class, 'products'])->name('shop');
Route::get('/shop/search-live',    [ShopController::class, 'liveSearch']);
Route::get('/shop/{uuid}',         [ShopController::class, 'productDetail'])->name('shop.product');

Route::get('/terms',   fn() => view('shop.pages.terms'))->name('terms');
Route::get('/privacy', fn() => view('shop.pages.privacy'))->name('privacy');
Route::get('/contact', fn() => view('shop.pages.contact'))->name('contact');
Route::post('/contact',            [ShopController::class, 'sendContact'])->name('contact.send');

/*
|--------------------------------------------------------------------------
| CART
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
| ORDERS & PAYMENTS
|--------------------------------------------------------------------------
*/
Route::post('/checkout/place',         [ShopController::class,  'placeOrder'])->name('checkout.place');
Route::get('/bank/pay/{uuid}',         [ShopController::class,  'bankSandbox'])->name('bank.sandbox');
Route::post('/bank/confirm',           [ShopController::class,  'bankConfirm'])->name('bank.confirm');
Route::get('/orders/success/{uuid}',   [ShopController::class,  'orderSuccess'])->name('order.success');
Route::get('/payment/{uuid}',          [PaymentController::class, 'page'])->name('payment.page');
Route::post('/payment/intent',         [PaymentController::class, 'createIntent'])->name('payment.intent');
Route::post('/payment/confirm',        [PaymentController::class, 'confirm'])->name('payment.confirm');
Route::post('/payment/refund/{uuid}',  [PaymentController::class, 'refund'])->name('payment.refund');

/*
|--------------------------------------------------------------------------
| PROFILI (auth)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profili-im',    [ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profili-im',   [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profili-im', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/dashboard', fn() => redirect()->route('home'))->name('dashboard');
    
});

/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // ── PRODUCTS (UUID) ──────────────────────────────────────
    Route::delete('products/bulk-delete', [ProductsController::class, 'bulkDelete'])->name('products.bulk-delete');
    Route::get    ('products',                        [ProductsController::class, 'index'])->name('products.index');
    Route::get    ('products/create',                 [ProductsController::class, 'create'])->name('products.create');
    Route::post   ('products',                        [ProductsController::class, 'store'])->name('products.store');
    Route::get    ('products/{uuid}',                 [ProductsController::class, 'show'])->name('products.show');
    Route::get    ('products/{uuid}/edit',            [ProductsController::class, 'edit'])->name('products.edit');
    Route::put    ('products/{uuid}',                 [ProductsController::class, 'update'])->name('products.update');
    Route::patch  ('products/{uuid}',                 [ProductsController::class, 'update']);
    Route::delete ('products/{uuid}',                 [ProductsController::class, 'destroy'])->name('products.destroy');
    Route::delete ('products/{uuid}/images/{image}',  [ProductsController::class, 'deleteImage'])->name('products.images.delete');
    Route::post   ('products/{uuid}/images/{image}/primary', [ProductsController::class, 'setPrimaryImage'])->name('products.images.primary');
    Route::get    ('get-brands-by-category/{category}', [ProductsController::class, 'getBrandsByCategory'])->name('products.brands-by-category');

    // ── CATEGORIES ───────────────────────────────────────────
    Route::resource('categories', CategoriesController::class);
    Route::get('categories/{id}/subcategories', [CategoriesController::class, 'getSubcategories']);

    // ── ORDERS (UUID) ────────────────────────────────────────
    Route::delete('orders/bulk-delete', [AdminOrdersController::class, 'bulkDelete'])->name('orders.bulk-delete');
    Route::get    ('orders',             [AdminOrdersController::class, 'index'])->name('orders.index');
    Route::get    ('orders/{uuid}',      [AdminOrdersController::class, 'show'])->name('orders.show');
    Route::put    ('orders/{uuid}',      [AdminOrdersController::class, 'update'])->name('orders.update');
    Route::patch  ('orders/{uuid}',      [AdminOrdersController::class, 'update']);
    Route::delete ('orders/{uuid}',      [AdminOrdersController::class, 'destroy'])->name('orders.destroy');

    // ── BRANDS ───────────────────────────────────────────────
    Route::resource('brands', BrandsController::class);

    // ── STOCK ────────────────────────────────────────────────
    Route::get   ('stock',                   [StockController::class, 'index'])->name('stock.index');
    Route::get   ('stock/low',               [StockController::class, 'lowStock'])->name('stock.low');
    Route::get   ('stock/{product}/history', [StockController::class, 'history'])->name('stock.history');
    Route::patch ('stock/variant/{id}',      [StockController::class, 'updateVariantStock'])->name('stock.variant.update');
    Route::patch ('stock/product/{id}',      [StockController::class, 'updateProductStock'])->name('stock.product.update');

    // ── BANNERS ──────────────────────────────────────────────
    Route::get   ('banners',         [BannerController::class, 'index'])->name('banners.index');
    Route::get   ('banners/create',  [BannerController::class, 'create'])->name('banners.create');
    Route::post  ('banners',         [BannerController::class, 'store'])->name('banners.store');
    Route::delete('banners/{id}',    [BannerController::class, 'destroy'])->name('banners.destroy');
    Route::post  ('banners/offer',   [BannerController::class, 'storeOffer'])->name('banners.offer');
    Route::delete('offers/{id}',     [BannerController::class, 'destroyOffer'])->name('offers.destroy');
Route::post('banners/upgrade', [BannerController::class, 'storeUpgrade'])->name('banners.upgrade');

   // ── CUSTOMERS (UUID) ─────────────────────────────────────
    Route::get ('customers',                    [CustomersController::class, 'index'])->name('customers.index');
    Route::get ('customers/create',             [CustomersController::class, 'create'])->name('customers.create');  // ← PARA {uuid}
    Route::post('customers',                    [CustomersController::class, 'store'])->name('customers.store');
    Route::get   ('customers/{uuid}/orders',    [CustomersController::class, 'orders'])->name('customers.orders');
    Route::delete('customers/{uuid}',           [CustomersController::class, 'destroy'])->name('customers.destroy');
    Route::patch ('customers/{uuid}/role',      [CustomersController::class, 'updateRole'])->name('customers.role');
    // ── SETTINGS ─────────────────────────────────────────────
    Route::get ('settings',           [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings',           [SettingController::class, 'update'])->name('settings.update');
    Route::post('settings/delivery',  [SettingController::class, 'saveDelivery'])->name('settings.delivery');

    // ── ADMIN PROFILE ─────────────────────────────────────────
    Route::get ('profile', [AdminProfileController::class, 'index'])->name('profile');
    Route::post('profile', [AdminProfileController::class, 'update'])->name('profile.update');
   Route::post('/save-token', [App\Http\Controllers\Admin\AdminProfileController::class, 'saveToken'])->middleware('auth');
});

require __DIR__.'/auth.php';