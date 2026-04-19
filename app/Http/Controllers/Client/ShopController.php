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

        // ✅ Bank/Stripe — mos krijo order, ruaj në sesion dhe shko te pagesa
        if (in_array($request->payment_method, ['bank', 'stripe'])) {
            session(['pending_checkout' => $request->all()]);
            return redirect()->route('payment.page');
        }

        // ✅ Cash — krijo order dhe dërgo njoftime
        $order = $this->orders->createFromCheckout($request);
        $this->dispatchNotifications($order);

        return redirect()->route('order.success', $order->uuid);
    }

    public function orderSuccess(string $uuid)
    {
        $order = Order::where('uuid', $uuid)->with('items.product')->firstOrFail();
        return view('shop.order.success', compact('order'));
    }

    public function bankSandbox()
    {
        if (!session('pending_checkout')) {
            return redirect()->route('cart.checkout')->with('error', 'Nuk ka porosi në pritje.');
        }

        $pending = session('pending_checkout');
        $cart    = session('cart', []);
        $total   = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);
        $total  += (float) ($pending['shipping_cost'] ?? 0);

        return view('shop.order.stripe-payment', [
            'pending' => $pending,
            'total'   => $total,
            'cart'    => $cart,
        ]);
    }

   public function bankConfirm(Request $request)
{
    if ($request->status !== 'confirmed') {
        session()->forget('pending_checkout');
        return response()->json(['success' => false, 'error' => 'Pagesa dështoi.'], 400);
    }

    $pending = session('pending_checkout');

    if (!$pending) {
        return response()->json(['success' => false, 'error' => 'Sesioni skadoi.'], 422);
    }

    $fakeRequest = new Request();
    $fakeRequest->replace(array_merge($pending, [
        // ✅ Ruaj payment_intent_id në order
        'payment_intent_id' => $request->payment_intent_id
    ]));

    $order = $this->orders->createFromCheckout($fakeRequest);
    
    // Ruaj payment_intent_id në order
    $order->update(['payment_intent_id' => $request->payment_intent_id]);

    session()->forget('pending_checkout');

    $this->dispatchNotifications($order);

    // ✅ Kthe JSON me redirect URL
    return response()->json([
        'success'  => true,
        'redirect' => route('order.success', $order->uuid)
    ]);
}

    public function sendContact(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'message' => 'required|string|min:10',
        ]);

        \Mail::html(
            $this->buildContactEmail($request->name, $request->email, $request->message),
            fn($m) => $m
                ->to(config('mail.from.address'))
                ->subject('📩 Kontakt nga ' . $request->name)
        );

        return redirect()->route('contact')->with('contact_success', true);
    }

    // ══════════════════════════════════════════
    //  NOTIFICATIONS — Email Admin + FCM
    // ══════════════════════════════════════════

    private function dispatchNotifications(Order $order): void
    {
        dispatch(function () use ($order) {

            // ── EMAIL ADMIN ──
            try {
                $order->load('items.product');

                \Mail::html(
                    $this->buildAdminOrderEmail($order),
                    fn($m) => $m
                        ->to(config('mail.from.address'))
                        ->subject('🛒 Porosi e re #' . ($order->order_number ?? ''))
                );

                \Log::info('Email admin u dërgua — ' . $order->order_number);
            } catch (\Exception $e) {
                \Log::error('Email error: ' . $e->getMessage());
            }

            // ── FCM ──
            try {
                $admins = \App\Models\User::where('role', 'admin')
                    ->whereNotNull('device_token')
                    ->get();

                if ($admins->isEmpty()) return;

                $token = $this->getFirebaseToken(
                    json_decode(file_get_contents(
                        storage_path('app/firebase/my-project-ef682-ca867058a36a.json')
                    ), true)
                );

                foreach ($admins as $admin) {
                    \Illuminate\Support\Facades\Http::withHeaders([
                        'Authorization' => 'Bearer ' . $token,
                        'Content-Type'  => 'application/json',
                    ])->post('https://fcm.googleapis.com/v1/projects/my-project-ef682/messages:send', [
                        'message' => [
                            'token'        => $admin->device_token,
                            'notification' => [
                                'title' => '🛒 Porosi e re',
                                'body'  => 'Porosi nga ' . $order->customer_name,
                            ],
                        ],
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('FCM error: ' . $e->getMessage());
            }

        })->afterResponse();
    }

    // ══════════════════════════════════════════
    //  EMAIL BUILDERS
    // ══════════════════════════════════════════

    private function buildAdminOrderEmail(Order $order): string
    {
        $items = '';
        foreach ($order->items ?? [] as $item) {
            $name   = htmlspecialchars($item->product->name ?? 'Produkt');
            $qty    = $item->quantity ?? 1;
            $price  = number_format(($item->unit_price ?? 0) * $qty, 2);
            $items .= '
            <tr>
                <td style="padding:12px 20px;border-bottom:1px solid #eef0f8;font-size:14px;color:#1a1c1d;">' . $name . '</td>
                <td style="padding:12px 20px;border-bottom:1px solid #eef0f8;font-size:14px;color:#1a1c1d;text-align:center;">× ' . $qty . '</td>
                <td style="padding:12px 20px;border-bottom:1px solid #eef0f8;font-size:14px;font-weight:700;color:#0059b5;text-align:right;">' . $price . ' €</td>
            </tr>';
        }

        $paymentLabel = match($order->payment_method ?? 'cash') {
            'bank'   => '🏦 Transfertë Bankare',
            'stripe' => '💳 Kartë Krediti',
            default  => '💵 Cash në Dorëzim',
        };

        return '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"></head>
<body style="margin:0;padding:0;background:#f4f6fb;font-family:Inter,Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6fb;padding:32px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
  <tr>
    <td style="background:#0f0f0f;padding:18px 32px;text-align:center;">
      <span style="font-size:20px;font-weight:800;color:#ffffff;letter-spacing:-0.5px;">Pro<span style="color:#0071e3;">Mobile</span></span>
    </td>
  </tr>
  <tr>
    <td style="background:linear-gradient(135deg,#0059b5 0%,#0071e3 100%);padding:36px 40px;text-align:center;">
      <div style="font-size:40px;margin-bottom:10px;">🛒</div>
      <h1 style="color:#ffffff;margin:0 0 6px;font-size:26px;font-weight:800;">Porosi e Re!</h1>
      <p style="color:rgba(255,255,255,0.75);margin:0;font-size:14px;">' . htmlspecialchars($order->order_number ?? '') . '</p>
    </td>
  </tr>
  <tr>
    <td style="padding:32px 40px;">
      <p style="font-size:15px;color:#414753;margin:0 0 24px;line-height:1.6;">Një porosi e re u pranua. Shiko detajet më poshtë.</p>
      <p style="font-size:11px;font-weight:700;text-transform:uppercase;color:#8b95b0;margin:0 0 10px;letter-spacing:0.8px;">Detajet e Klientit</p>
      <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f9ff;border-radius:14px;overflow:hidden;margin-bottom:24px;">
        <tr><td style="padding:14px 20px;border-bottom:1px solid #eef0f8;">
          <span style="font-size:11px;font-weight:700;text-transform:uppercase;color:#8b95b0;">Klienti</span><br>
          <span style="font-size:15px;font-weight:700;color:#1a1c1d;">' . htmlspecialchars($order->customer_name ?? 'N/A') . '</span>
        </td></tr>
        <tr><td style="padding:14px 20px;border-bottom:1px solid #eef0f8;">
          <span style="font-size:11px;font-weight:700;text-transform:uppercase;color:#8b95b0;">Telefoni</span><br>
          <span style="font-size:15px;font-weight:700;color:#1a1c1d;">' . htmlspecialchars($order->customer_phone ?? 'N/A') . '</span>
        </td></tr>
        <tr><td style="padding:14px 20px;border-bottom:1px solid #eef0f8;">
          <span style="font-size:11px;font-weight:700;text-transform:uppercase;color:#8b95b0;">Email</span><br>
          <span style="font-size:15px;font-weight:700;color:#0059b5;">' . htmlspecialchars($order->customer_email ?? 'N/A') . '</span>
        </td></tr>
        <tr><td style="padding:14px 20px;">
          <span style="font-size:11px;font-weight:700;text-transform:uppercase;color:#8b95b0;">Adresa</span><br>
          <span style="font-size:15px;font-weight:700;color:#1a1c1d;">' . htmlspecialchars(($order->shipping_address ?? 'N/A') . ', ' . ($order->city ?? 'N/A')) . '</span>
        </td></tr>
      </table>
      <p style="font-size:11px;font-weight:700;text-transform:uppercase;color:#8b95b0;margin:0 0 10px;letter-spacing:0.8px;">Produktet e Porositura</p>
      <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f9ff;border-radius:14px;overflow:hidden;margin-bottom:24px;">
        <tr style="background:#eef0f8;">
          <td style="padding:10px 20px;font-size:11px;font-weight:700;text-transform:uppercase;color:#8b95b0;">Produkti</td>
          <td style="padding:10px 20px;font-size:11px;font-weight:700;text-transform:uppercase;color:#8b95b0;text-align:center;">Sasia</td>
          <td style="padding:10px 20px;font-size:11px;font-weight:700;text-transform:uppercase;color:#8b95b0;text-align:right;">Çmimi</td>
        </tr>
        ' . $items . '
      </table>
      <table width="100%" cellpadding="0" cellspacing="0" style="background:linear-gradient(135deg,#0059b5,#0071e3);border-radius:14px;margin-bottom:24px;">
        <tr>
          <td style="padding:20px 24px;">
            <span style="font-size:12px;text-transform:uppercase;color:rgba(255,255,255,0.7);">Totali</span><br>
            <span style="font-size:36px;font-weight:800;color:#ffffff;">' . number_format($order->total_amount ?? 0, 2) . ' €</span>
          </td>
          <td style="padding:20px 24px;text-align:right;vertical-align:middle;">
            <span style="display:inline-block;background:rgba(255,255,255,0.15);border-radius:999px;padding:8px 16px;font-size:13px;font-weight:700;color:#ffffff;">' . $paymentLabel . '</span>
          </td>
        </tr>
      </table>
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr><td align="center">
          <a href="' . url('/admin/orders/' . ($order->uuid ?? '')) . '"
             style="display:inline-block;background:#1a1c1d;color:#ffffff;padding:15px 40px;border-radius:999px;font-size:14px;font-weight:700;text-decoration:none;">
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
</body>
</html>';
    }

    private function buildContactEmail(string $name, string $email, string $message): string
    {
        return '<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:0;background:#f9f9fb;font-family:Inter,Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f9f9fb;padding:20px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">
  <tr>
    <td style="background:#0f0f0f;padding:18px 32px;text-align:center;">
      <span style="font-size:20px;font-weight:800;color:#ffffff;">Pro<span style="color:#0071e3;">Mobile</span></span>
    </td>
  </tr>
  <tr>
    <td style="background:linear-gradient(135deg,#0059b5,#0071e3);padding:28px 32px;text-align:center;">
      <div style="font-size:36px;margin-bottom:8px;">📩</div>
      <h1 style="color:#ffffff;margin:0;font-size:22px;font-weight:800;">Mesazh i Ri Kontakti</h1>
    </td>
  </tr>
  <tr>
    <td style="padding:28px 32px;">
      <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f9ff;border-radius:12px;overflow:hidden;">
        <tr><td style="padding:14px 20px;border-bottom:1px solid #eef0f8;">
          <span style="font-size:11px;font-weight:700;text-transform:uppercase;color:#8b95b0;">Emri</span><br>
          <span style="font-size:15px;font-weight:700;color:#1a1c1d;">' . htmlspecialchars($name) . '</span>
        </td></tr>
        <tr><td style="padding:14px 20px;border-bottom:1px solid #eef0f8;">
          <span style="font-size:11px;font-weight:700;text-transform:uppercase;color:#8b95b0;">Email</span><br>
          <span style="font-size:15px;font-weight:700;color:#0059b5;">' . htmlspecialchars($email) . '</span>
        </td></tr>
        <tr><td style="padding:14px 20px;">
          <span style="font-size:11px;font-weight:700;text-transform:uppercase;color:#8b95b0;">Mesazhi</span><br>
          <span style="font-size:14px;color:#1a1c1d;line-height:1.7;">' . nl2br(htmlspecialchars($message)) . '</span>
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
</td></tr></table>
</body>
</html>';
    }

    // ══════════════════════════════════════════
    //  FIREBASE TOKEN
    // ══════════════════════════════════════════

    private function getFirebaseToken(array $serviceAccount): string
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

        return \Illuminate\Support\Facades\Http::asForm()
            ->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion'  => $jwt,
            ])->json()['access_token'];
    }
}