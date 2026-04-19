<?php
// ── PËRDORIMI në ShopController (brenda dispatch afterResponse) ──
// $emailHtml = adminNewOrderEmail($order);
// \Mail::html($emailHtml, fn($m) => $m
//     ->to(config('mail.from.address'))
//     ->subject('🛒 Porosi e re #' . $order->order_number)
// );

function adminNewOrderEmail(object $order): string
{
    $items = '';
    foreach ($order->items ?? [] as $item) {
        $name    = $item->product->name ?? 'Produkt';
        $qty     = $item->quantity ?? 1;
        $price   = number_format(($item->unit_price ?? 0) * $qty, 2);
        $items  .= '
        <tr>
            <td style="padding:12px 20px;border-bottom:1px solid #eef0f8;font-size:14px;color:#1a1c1d;">
                ' . htmlspecialchars($name) . '
            </td>
            <td style="padding:12px 20px;border-bottom:1px solid #eef0f8;font-size:14px;color:#1a1c1d;text-align:center;">
                × ' . $qty . '
            </td>
            <td style="padding:12px 20px;border-bottom:1px solid #eef0f8;font-size:14px;font-weight:700;color:#0059b5;text-align:right;">
                ' . $price . ' €
            </td>
        </tr>';
    }

    $paymentLabel = match($order->payment_method ?? 'cash') {
        'bank'   => '🏦 Transfertë Bankare',
        'stripe' => '💳 Kartë Krediti',
        default  => '💵 Cash në Dorëzim',
    };

    return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background:#f4f6fb;font-family:Inter,Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6fb;padding:32px 0;">
<tr><td align="center">
<table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);">

  <!-- HEADER -->
  <tr>
    <td style="background:#0f0f0f;padding:18px 32px;text-align:center;">
      <span style="font-size:20px;font-weight:800;color:#ffffff;letter-spacing:-0.5px;">
        Pro<span style="color:#0071e3;">Mobile</span>
      </span>
    </td>
  </tr>

  <!-- HERO -->
  <tr>
    <td style="background:linear-gradient(135deg,#0059b5 0%,#0071e3 100%);padding:36px 40px;text-align:center;">
      <div style="width:56px;height:56px;background:rgba(255,255,255,0.2);border-radius:50%;margin:0 auto 12px;display:flex;align-items:center;justify-content:center;font-size:28px;line-height:56px;">
        🛒
      </div>
      <h1 style="color:#ffffff;margin:0 0 6px;font-size:26px;font-weight:800;letter-spacing:-0.5px;">
        Porosi e Re!
      </h1>
      <p style="color:rgba(255,255,255,0.75);margin:0;font-size:14px;">
        ' . htmlspecialchars($order->order_number ?? '') . '
      </p>
    </td>
  </tr>

  <!-- BODY -->
  <tr>
    <td style="padding:32px 40px;">

      <p style="font-size:15px;color:#414753;margin:0 0 24px;line-height:1.6;">
        Një porosi e re u pranua në platformë. Shiko detajet më poshtë.
      </p>

      <!-- DETAJET E KLIENTIT -->
      <p style="font-size:11px;font-weight:700;text-transform:uppercase;color:#8b95b0;margin:0 0 10px;letter-spacing:0.8px;">
        Detajet e Klientit
      </p>
      <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f9ff;border-radius:14px;overflow:hidden;margin-bottom:24px;">
        <tr>
          <td style="padding:14px 20px;border-bottom:1px solid #eef0f8;">
            <span style="font-size:11px;font-weight:700;text-transform:uppercase;color:#8b95b0;">Klienti</span><br>
            <span style="font-size:15px;font-weight:700;color:#1a1c1d;">' . htmlspecialchars($order->customer_name ?? 'N/A') . '</span>
          </td>
        </tr>
        <tr>
          <td style="padding:14px 20px;border-bottom:1px solid #eef0f8;">
            <span style="font-size:11px;font-weight:700;text-transform:uppercase;color:#8b95b0;">Telefoni</span><br>
            <span style="font-size:15px;font-weight:700;color:#1a1c1d;">' . htmlspecialchars($order->customer_phone ?? 'N/A') . '</span>
          </td>
        </tr>
        <tr>
          <td style="padding:14px 20px;border-bottom:1px solid #eef0f8;">
            <span style="font-size:11px;font-weight:700;text-transform:uppercase;color:#8b95b0;">Email</span><br>
            <span style="font-size:15px;font-weight:700;color:#0059b5;">' . htmlspecialchars($order->customer_email ?? 'N/A') . '</span>
          </td>
        </tr>
        <tr>
          <td style="padding:14px 20px;">
            <span style="font-size:11px;font-weight:700;text-transform:uppercase;color:#8b95b0;">Adresa</span><br>
            <span style="font-size:15px;font-weight:700;color:#1a1c1d;">
              ' . htmlspecialchars(($order->shipping_address ?? 'N/A') . ', ' . ($order->city ?? 'N/A')) . '
            </span>
          </td>
        </tr>
      </table>

      <!-- PRODUKTET -->
      <p style="font-size:11px;font-weight:700;text-transform:uppercase;color:#8b95b0;margin:0 0 10px;letter-spacing:0.8px;">
        Produktet e Porositura
      </p>
      <table width="100%" cellpadding="0" cellspacing="0" style="background:#f8f9ff;border-radius:14px;overflow:hidden;margin-bottom:24px;">
        <tr style="background:#eef0f8;">
          <td style="padding:10px 20px;font-size:11px;font-weight:700;text-transform:uppercase;color:#8b95b0;">Produkti</td>
          <td style="padding:10px 20px;font-size:11px;font-weight:700;text-transform:uppercase;color:#8b95b0;text-align:center;">Sasia</td>
          <td style="padding:10px 20px;font-size:11px;font-weight:700;text-transform:uppercase;color:#8b95b0;text-align:right;">Çmimi</td>
        </tr>
        ' . $items . '
      </table>

      <!-- TOTALI -->
      <table width="100%" cellpadding="0" cellspacing="0" style="background:linear-gradient(135deg,#0059b5,#0071e3);border-radius:14px;margin-bottom:24px;">
        <tr>
          <td style="padding:20px 24px;">
            <span style="font-size:12px;text-transform:uppercase;color:rgba(255,255,255,0.7);letter-spacing:0.8px;">Totali i Porosisë</span><br>
            <span style="font-size:36px;font-weight:800;color:#ffffff;">' . number_format($order->total_amount ?? 0, 2) . ' €</span>
          </td>
          <td style="padding:20px 24px;text-align:right;vertical-align:middle;">
            <span style="display:inline-block;background:rgba(255,255,255,0.15);border-radius:999px;padding:8px 16px;font-size:13px;font-weight:700;color:#ffffff;">
              ' . $paymentLabel . '
            </span>
          </td>
        </tr>
      </table>

      <!-- BUTONI -->
      <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td align="center">
            <a href="' . url('/admin/orders/' . ($order->uuid ?? '')) . '"
               style="display:inline-block;background:#1a1c1d;color:#ffffff;padding:15px 40px;border-radius:999px;font-size:14px;font-weight:700;text-decoration:none;letter-spacing:0.3px;">
              Shiko Porosinë →
            </a>
          </td>
        </tr>
      </table>

    </td>
  </tr>

  <!-- FOOTER -->
  <tr>
    <td style="background:#f8f9ff;padding:20px 40px;text-align:center;border-top:1px solid #eef0f8;">
      <p style="color:#8b95b0;font-size:12px;margin:0;">
        ProMobile Store · Sistemi i Porosive
      </p>
    </td>
  </tr>

</table>
</td></tr></table>
</body>
</html>';
}