<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use App\Services\ShopService;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function __construct(protected ShopService $shop, protected OrderService $orders) {}

    public function home() { return view('shop.pages.home', $this->shop->homeData()); }

    public function products(Request $r) { return view('shop.pages.products', $this->shop->productList($r)); }

    public function productDetail(string $uuid)
    {
        $product = Product::where('uuid', $uuid)->firstOrFail();
        return view('shop.pages.product-detail', $this->shop->productDetail($product->id));
    }

    public function liveSearch(Request $request)
    {
        $q = $request->get('q', '');
        if (strlen($q) < 1) return response()->json([]);
        return response()->json($this->shop->liveSearch($q));
    }

    public function placeOrder(Request $request)
    {
        $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_email'   => 'required|email|max:255',
            'customer_phone'   => 'nullable|string|max:30',
            'shipping_address' => 'nullable|string|max:255',
            'city'             => 'nullable|string|max:100',
            'payment_method'   => 'required|in:cash,bank,stripe',
            'shipping_cost'    => 'nullable|numeric|min:0',
        ]);

        if (empty(session('cart', []))) {
            return redirect()->route('cart.checkout')->with('error', 'Shporta është bosh!');
        }

        $order = $this->orders->createFromCheckout($request);

        // ── EMAIL PARA FCM ──
        try {
            $emailHtml = '<!DOCTYPE html><html><head><meta charset="UTF-8"></head>
            <body style="margin:0;padding:0;background:#f4f6fb;font-family:Inter,Arial,sans-serif;">
            <table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6fb;padding:32px 0;">
            <tr><td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
              <tr>
                <td style="background:linear-gradient(135deg,#0059b5 0%,#0071e3 100%);padding:32px 40px;text-align:center;">
                  <div style="font-size:32px;margin-bottom:8px;">🛒</div>
                  <h1 style="color:#ffffff;margin:0;font-size:24px;font-weight:800;">Porosi e Re!</h1>
                  <p style="color:rgba(255,255,255,0.75);margin:6px 0 0;font-size:14px;">' . ($order->order_number ?? '') . '</p>
                </td>
              </tr>
              <tr>
                <td style="padding:32px 40px;">
                  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f9ff;border-radius:14px;overflow:hidden;margin-bottom:24px;">
                    <tr><td style="padding:14px 20px;border-bottom:1px solid #eef0f8;">
                      <span style="font-size:11px;font-weight:700;text-transform:uppercase;color:#8b95b0;">Klienti</span><br>
                      <span style="font-size:15px;font-weight:700;color:#1a1c1d;">' . ($order->customer_name ?? 'N/A') . '</span>
                    </td></tr>
                    <tr><td style="padding:14px 20px;border-bottom:1px solid #eef0f8;">
                      <span style="font-size:11px;font-weight:700;text-transform:uppercase;color:#8b95b0;">Telefoni</span><br>
                      <span style="font-size:15px;font-weight:700;color:#1a1c1d;">' . ($order->customer_phone ?? 'N/A') . '</span>
                    </td></tr>
                    <tr><td style="padding:14px 20px;border-bottom:1px solid #eef0f8;">
                      <span style="font-size:11px;font-weight:700;text-transform:uppercase;color:#8b95b0;">Email</span><br>
                      <span style="font-size:15px;font-weight:700;color:#0059b5;">' . ($order->customer_email ?? 'N/A') . '</span>
                    </td></tr>
                    <tr><td style="padding:14px 20px;">
                      <span style="font-size:11px;font-weight:700;text-transform:uppercase;color:#8b95b0;">Adresa</span><br>
                      <span style="font-size:15px;font-weight:700;color:#1a1c1d;">' . ($order->shipping_address ?? 'N/A') . ', ' . ($order->city ?? 'N/A') . '</span>
                    </td></tr>
                  </table>
                  <table width="100%" cellpadding="0" cellspacing="0" style="background:linear-gradient(135deg,#0059b5,#0071e3);border-radius:14px;margin-bottom:28px;">
                    <tr><td style="padding:20px 24px;">
                      <span style="font-size:12px;text-transform:uppercase;color:rgba(255,255,255,0.7);">Totali</span><br>
                      <span style="font-size:32px;font-weight:800;color:#ffffff;">' . number_format($order->total_amount, 2) . ' €</span>
                    </td></tr>
                  </table>
                  <table width="100%" cellpadding="0" cellspacing="0">
                    <tr><td align="center">
                      <a href="' . url('/admin/orders/' . $order->uuid) . '" style="display:inline-block;background:#1a1c1d;color:#ffffff;padding:14px 36px;border-radius:999px;font-size:14px;font-weight:700;text-decoration:none;">
                        Shiko Porosinë →
                      </a>
                    </td></tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td style="background:#f8f9ff;padding:20px 40px;text-align:center;border-top:1px solid #eef0f8;">
                  <p style="color:#8b95b0;font-size:12px;margin:0;">ProMobile Store · Sistemi i Porosive</p>
                </td>
              </tr>
            </table>
            </td></tr></table>
            </body></html>';

            \Mail::html($emailHtml, fn($m) => $m
                ->to('bytyqieriton58@gmail.com')
                ->subject('🛒 Porosi e re #' . ($order->order_number ?? ''))
            );
            \Log::info('Email u dërgua!');
        } catch (\Exception $e) {
            \Log::error('Email error: ' . $e->getMessage());
        }

        // ── FCM PAS — afterResponse nuk bllokon ──
        try {
            $orderData = ['name' => $order->customer_name, 'uuid' => $order->uuid];
            dispatch(function () use ($orderData) {
                $admins = \App\Models\User::where('role', 'admin')->whereNotNull('device_token')->get();
                if ($admins->isEmpty()) return;

                $serviceAccount = json_decode(
                    file_get_contents(storage_path('app/firebase/my-project-ef682-ca867058a36a.json')), true
                );
                $now     = time();
                $header  = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
                $payload = base64_encode(json_encode([
                    'iss'   => $serviceAccount['client_email'],
                    'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                    'aud'   => 'https://oauth2.googleapis.com/token',
                    'iat'   => $now,
                    'exp'   => $now + 3600,
                ]));
                openssl_sign("$header.$payload", $signature, $serviceAccount['private_key'], 'SHA256');
                $jwt   = "$header.$payload." . base64_encode($signature);
                $token = \Illuminate\Support\Facades\Http::asForm()
                    ->post('https://oauth2.googleapis.com/token', [
                        'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                        'assertion'  => $jwt,
                    ])->json()['access_token'];

                foreach ($admins as $admin) {
                    \Illuminate\Support\Facades\Http::withHeaders([
                        'Authorization' => 'Bearer ' . $token,
                        'Content-Type'  => 'application/json',
                    ])->post('https://fcm.googleapis.com/v1/projects/my-project-ef682/messages:send', [
                        'message' => [
                            'token'        => $admin->device_token,
                            'notification' => [
                                'title' => '🛒 Porosi e re',
                                'body'  => 'Porosi nga ' . $orderData['name'],
                            ],
                        ]
                    ]);
                }
            })->afterResponse();
        } catch (\Exception $e) {
            \Log::error('FCM error: ' . $e->getMessage());
        }

        return in_array($request->payment_method, ['bank', 'stripe'])
            ? redirect()->route('payment.page', $order->uuid)
            : redirect()->route('order.success', $order->uuid);
    }

    private function getFirebaseToken($serviceAccount)
    {
        $now     = time();
        $header  = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $payload = base64_encode(json_encode([
            'iss'   => $serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'iat'   => $now,
            'exp'   => $now + 3600,
        ]));
        openssl_sign("$header.$payload", $signature, $serviceAccount['private_key'], 'SHA256');
        $jwt = "$header.$payload." . base64_encode($signature);

        $response = \Illuminate\Support\Facades\Http::withOptions([
            'verify' => false,
        ])->asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ]);

        return $response->json()['access_token'];
    }

    public function orderSuccess(string $uuid)
    {
        $order = Order::where('uuid', $uuid)->with('items.product')->firstOrFail();
        return view('shop.order.success', compact('order'));
    }

    public function bankSandbox(string $uuid)
    {
        $order = Order::where('uuid', $uuid)->firstOrFail();
        return view('shop.order.stripe-payment', compact('order'));
    }

    public function bankConfirm(Request $request)
    {
        $order = Order::where('uuid', $request->order_id)->firstOrFail();

        if ($request->status === 'confirmed') {
            $order->update(['status' => 'pending']);
            return redirect()->route('order.success', $order->uuid)->with('payment_success', true);
        }

        $order->update(['status' => 'payment_failed']);
        return redirect()->route('bank.sandbox', $order->uuid)->with('error', 'Pagesa dështoi.');
    }

    public function sendContact(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'message' => 'required|string|min:10',
        ]);

        \Mail::html(
            '<table width="100%" cellpadding="0" cellspacing="0" style="background:#f9f9fb;padding:20px 0;">
            <tr><td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;">
              <tr>
                <td style="background:linear-gradient(135deg,#0059b5,#0071e3);padding:24px 32px;">
                  <h1 style="color:#ffffff;margin:0;font-size:20px;font-family:Arial,sans-serif;">📩 Mesazh Kontakti!</h1>
                </td>
              </tr>
              <tr>
                <td style="padding:24px 32px;font-family:Arial,sans-serif;">
                  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f9ff;border-radius:12px;overflow:hidden;">
                    <tr><td style="padding:12px 18px;border-bottom:1px solid #eef0f8;">
                      <span style="font-size:11px;text-transform:uppercase;color:#8b95b0;">Emri</span><br>
                      <span style="font-size:15px;font-weight:700;color:#1a1c1d;">' . $request->name . '</span>
                    </td></tr>
                    <tr><td style="padding:12px 18px;border-bottom:1px solid #eef0f8;">
                      <span style="font-size:11px;text-transform:uppercase;color:#8b95b0;">Email</span><br>
                      <span style="font-size:15px;font-weight:700;color:#0059b5;">' . $request->email . '</span>
                    </td></tr>
                    <tr><td style="padding:12px 18px;">
                      <span style="font-size:11px;text-transform:uppercase;color:#8b95b0;">Mesazhi</span><br>
                      <span style="font-size:14px;color:#1a1c1d;line-height:1.6;">' . nl2br(e($request->message)) . '</span>
                    </td></tr>
                  </table>
                </td>
              </tr>
              <tr>
                <td style="background:#f8f9ff;padding:16px 32px;text-align:center;border-top:1px solid #eef0f8;">
                  <p style="color:#8b95b0;font-size:12px;margin:0;">ProMobile Store</p>
                </td>
              </tr>
            </table>
            </td></tr></table>',
            fn($m) => $m->to('bytyqieriton58@gmail.com')
                        ->subject('📩 Kontakt nga ' . $request->name)
        );

        return redirect()->route('contact')->with('contact_success', true);
    }
}