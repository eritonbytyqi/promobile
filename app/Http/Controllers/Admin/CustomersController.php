<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
class CustomersController extends Controller
{
 public function index(Request $request)
{
    $query = User::orderByDesc('created_at');

    if ($request->filled('role')) {
        $query->where('role', $request->role);
    }

    $customers = $query->paginate(20);

    $customers->getCollection()->transform(function ($c) {
        $c->orders_count_custom = Order::where('customer_email', $c->email)->count();
        return $c;
    });

    return view('admin.customers.index', compact('customers'));
}
  public function orders(string $uuid)
{
    $customer = User::where('uuid', $uuid)->firstOrFail();
    $orders   = Order::where('customer_email', $customer->email)->orderByDesc('created_at')->paginate(15);
    return view('admin.customers.orders', compact('customer', 'orders'));
}

    public function destroy(string $uuid)
    {
        $customer = User::where('role', 'customer')->where('uuid', $uuid)->firstOrFail();
        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('success', 'Klienti u fshi me sukses!');
    }
    public function updateRole(Request $request, string $uuid)
{
    $user = User::where('uuid', $uuid)->firstOrFail();
    $user->update(['role' => $request->role]);
    return back()->with('success', 'Roli u ndryshua!');
}
public function create()
{
    return view('admin.customers.create');
}

public function store(Request $request)
{
    $request->validate([
        'name'     => 'required|string|max:255',
        'surname'  => 'nullable|string|max:255',
        'email'    => 'required|email|unique:users,email',
        'phone'    => 'nullable|string|max:30',
        'password' => 'required|min:8|confirmed',
        'role'     => 'required|in:admin,customer',
    ], [
        'email.unique'       => 'Ky email është tashmë i regjistruar.',
        'password.min'       => 'Fjalëkalimi duhet të ketë të paktën 8 karaktere.',
        'password.confirmed' => 'Fjalëkalimet nuk përputhen.',
    ]);

    User::create([
        'name'     => $request->name,
        'surname'  => $request->surname,
        'email'    => $request->email,
        'phone'    => $request->phone,
        'password' => \Hash::make($request->password),
        'role'     => $request->role,
        'is_admin' => $request->role === 'admin',
    ]);

    return redirect()->route('admin.customers.index')
        ->with('success', 'Përdoruesi u krijua me sukses!');
}
}