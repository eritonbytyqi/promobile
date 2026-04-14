<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminProfileController extends Controller
{
    public function index()
    {
        return view('admin.profile');
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'             => 'required|string|max:255',
            'surname'          => 'nullable|string|max:255',
            'phone'            => 'nullable|string|max:30',
            'current_password' => 'nullable|string',
            'password'         => 'nullable|min:8|confirmed',
        ], [
            'password.min'       => 'Fjalëkalimi duhet të ketë të paktën 8 karaktere.',
            'password.confirmed' => 'Fjalëkalimet nuk përputhen.',
        ]);

        $user->update([
            'name'    => $request->name,
            'surname' => $request->surname,
            'phone'   => $request->phone,
        ]);

        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->with('error', 'Fjalëkalimi aktual është i gabuar.');
            }
            $user->update(['password' => Hash::make($request->password)]);
        }

        return back()->with('success', 'Profili u ruajt me sukses!');
    }
 public function saveToken(Request $request)
{
    $token = $request->input('token') ?? $request->json('token');
    
    \Log::info('Save token called: ' . $token);
    
    auth()->user()->update([
        'device_token' => $token
    ]);

    return response()->json(['success' => true]);
}
}