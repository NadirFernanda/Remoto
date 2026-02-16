<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:40',
            'bio' => 'nullable|string|max:1000',
            'location' => 'nullable|string|max:255',
        ]);

        $user->fill($data);
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'Perfil atualizado com sucesso.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Senha atual incorreta.']);
        }

        $user->password = bcrypt($validated['new_password']);
        $user->save();

        return Redirect::route('profile.edit')->with('status', 'Senha alterada com sucesso.');
    }

    public function generateAffiliate(Request $request)
    {
        $user = Auth::user();
        if ($user->affiliate_code) {
            return redirect()->back()->with('status', 'Você já possui um código de afiliado.');
        }

        // Gera um código curto único
        do {
            $code = strtoupper(substr(bin2hex(random_bytes(6)), 0, 8));
        } while (\App\Models\User::where('affiliate_code', $code)->exists());

        $user->affiliate_code = $code;
        $user->save();

        return redirect()->back()->with('status', 'Código de afiliado gerado com sucesso.');
    }
}
