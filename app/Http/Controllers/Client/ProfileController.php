<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user    = auth()->user();
        $address = UserAddress::where('user_id', $user->id)->where('is_default', 1)->first();
        $orders  = Order::where('customer_email', $user->email)->latest()->get();
        return view('shop.profile.index', compact('user', 'address', 'orders'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
      $request->validate([
    'name'             => 'required|string|max:255',
    'surname'          => 'nullable|string|max:255',
    'phone'            => 'nullable|string|max:30',
    'address_line_1'   => 'nullable|string|max:255',
    'city'             => 'nullable|string|max:100',
    'postal_code'      => 'nullable|string|max:20',
    'country'          => 'nullable|string|max:100',
    'current_password' => 'nullable|string',
    'password'         => 'nullable|min:8|confirmed',
], [
    'name.required'        => 'Emri është i detyrueshëm.',
    'name.max'             => 'Emri nuk mund të jetë më i gjatë se 255 karaktere.',
    'phone.max'            => 'Numri i telefonit nuk është i vlefshëm.',
    'password.min'         => 'Fjalëkalimi i ri duhet të ketë të paktën 8 karaktere.',
    'password.confirmed'   => 'Fjalëkalimet e reja nuk përputhen.',
]);
        $user->name = $request->name; $user->surname = $request->surname; $user->phone = $request->phone;
        if ($request->filled('current_password') && $request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) return back()->with('error', 'Fjalëkalimi aktual është i gabuar.')->withInput();
            $user->password = Hash::make($request->password);
        }
        $user->save();
        UserAddress::updateOrCreate(['user_id' => $user->id, 'is_default' => 1], ['full_name' => trim($user->name.' '.$user->surname), 'phone' => $user->phone ?? '', 'address_line_1' => $request->address_line_1, 'city' => $request->city, 'postal_code' => $request->postal_code, 'country' => $request->country]);
        return redirect()->route('profile.index')->with('success', 'Profili u ruajt me sukses!');
    }

    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', ['password' => ['required', 'current_password']]);
        $user = $request->user();
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
