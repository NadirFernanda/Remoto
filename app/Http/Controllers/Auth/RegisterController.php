<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RegisterController extends Controller
{
    public function registerClient(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:client',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
        ]);

        $user->sendEmailVerificationNotification();

        event(new \App\Events\ClientRegistered($user));

        return redirect('/login')->with('status', 'Cadastro realizado! Verifique seu e-mail para ativar a conta.');
    }
    public function showFreelancerForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|in:freelancer,cliente',
        ]);

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'role' => $validated['role'],
            ]);

            // Envia notificação de verificação de e-mail
            $user->sendEmailVerificationNotification();

            // Dispara evento para ações pós-cadastro
            if ($user->role === 'freelancer') {
                event(new \App\Events\FreelancerRegistered($user));
            } else {
                event(new \App\Events\ClientRegistered($user));
            }

            return redirect('/login')->with('status', 'Cadastro realizado! Verifique seu e-mail para ativar a conta.');
        } catch (\Exception $e) {
            return back()->withInput()->withErrors(['Ocorreu um erro ao cadastrar. Tente novamente. Detalhe: ' . $e->getMessage()]);
        }
    }
}
