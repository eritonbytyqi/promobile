<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function __construct(
        protected OrderService    $orders,
        protected OrderRepository $repo
    ) {}

    // ── HELPER ───────────────────────────────────────────────
    private function findByUuid(string $uuid): Order
    {
        return Order::where('uuid', $uuid)->firstOrFail();
    }

public function index(Request $request)
{
    return view('admin.orders.index', [
        'orders' => $this->repo->allPaginated(
            perPage: 20,
            search:  $request->q,
            status:  $request->status,
        )
    ]);
}

    public function show(string $uuid)
    {
        $order = $this->findByUuid($uuid);
        return view('admin.orders.show', [
            'order' => $this->repo->findWithItems($order->id)
        ]);
    }

    public function create()
    {
        return view('admin.orders.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_phone'   => 'required|string|max:255',
            'customer_email'   => 'nullable|email',
            'shipping_address' => 'required|string|max:255',
            'city'             => 'required|string|max:255',
            'product_id'       => 'required|exists:products,id',
            'quantity'         => 'nullable|integer|min:1',
        ]);

        $order = $this->orders->createManual($request);
 try {
        \Mail::html(
            view('emails.admin-new-order', ['order' => $order])->render(),
            fn($m) => $m
                ->to(config('mail.from.address'))
                ->subject('🛍️ Porosi e Re #' . $order->id . ' — ' . $order->customer_name)
        );
    } catch (\Exception $e) {
        \Log::error('Admin email error: ' . $e->getMessage());
    }

    return redirect()
        ->route('admin.orders.show', $order->uuid)
        ->with('success', 'Porosia u krijua!');
}
   public function update(Request $request, string $uuid)
{
    $request->validate(['status' => 'required|string']);

    $order = $this->findByUuid($uuid);
    $order = $this->repo->findWithStock($order->id);

    if (in_array($order->status, ['cancelled', 'payment_failed'])) {
        return redirect()->back()->with('error', 'Porosia e anuluar nuk mund të ndryshohet!');
    }

    // Nëse vjen nga modal me email manual — skip emailin automatik
    $skipEmail = $request->has('send_email');

    $this->orders->updateStatus(
        $order,
        $request->status,
        array_filter($request->only([
            'customer_name', 'customer_email', 'customer_phone',
            'shipping_address', 'notes', 'payment_method'
        ])),
        $skipEmail
    );

    // Dërgo email manual nga modal
    if ($request->send_email && $request->email_subject && $request->email_body) {
        try {
            \Mail::html(
                view('emails.order-status-custom', [
                    'order'   => $order,
                    'subject' => $request->email_subject,
                    'body'    => nl2br($request->email_body),
                ])->render(),
                fn($m) => $m
                    ->to($order->customer_email)
                    ->subject($request->email_subject)
            );
        } catch (\Exception $e) {
            \Log::error('Email error: ' . $e->getMessage());
        }
    }

    if ($request->wantsJson()) {
        return response()->json(['success' => true]);
    }

    return redirect()
        ->route('admin.orders.index')
        ->with('success', $this->orders->statusMessage($request->status));
}

    public function destroy(string $uuid)
    {
        $order = $this->findByUuid($uuid);
        $order = $this->repo->findWithStock($order->id);

        $this->orders->delete($order);

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'Porosia u fshi dhe stoku u kthye.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->back()->with('error', 'Nuk u zgjodhën porosi!');
        }

        $orders = Order::with(['items.product', 'items.product.variants'])
            ->whereIn('uuid', $ids)
            ->get();

        foreach ($orders as $order) {
            $this->orders->delete($order);
        }

        return redirect()
            ->route('admin.orders.index')
            ->with('success', count($ids) . ' porosi u fshinë dhe stoku u kthye!');
    }
}