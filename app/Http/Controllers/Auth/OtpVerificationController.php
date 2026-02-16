<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\User;

class OtpVerificationController extends Controller
{
    public function showOtpForm()
    {
        return view('auth.otp-verify');
    }

    public function sendOtp(Request $request)
    {
        $user = Auth::user();
        $otp = rand(100000, 999999);
        Cache::put('otp_' . $user->id, $otp, now()->addMinutes(10));
        // Envie o OTP por e-mail
        Mail::raw("Seu código de verificação é: $otp", function ($message) use ($user) {
            $message->to($user->email)
                ->subject('Código de verificação de acesso');
        });
        return back()->with('message', 'Código enviado para seu e-mail.');
    }

    public function verifyOtp(Request $request)
    {
       
       
       $request->validate([
            'otp' => 'required|digits:6',
        ]);
        $user = Auth::user();
        $cachedOtp = Cache::get('otp_' . $user->id);
        if ($cachedOtp && $request->otp == $cachedOtp) {
            Cache::forget('otp_' . $user->id);
            $user->email_verified_at = now();
            $user->save();
            // Redireciona para o dashboard correto
            if ($user->role === 'cliente') {
                return redirect('/cliente/dashboard')->with('status', 'E-mail verificado com sucesso!');
            } elseif ($user->role === 'freelancer') {
                return redirect('/freelancer/dashboard')->with('status', 'E-mail verificado com sucesso!');
            } elseif ($user->role === 'admin') {
                return redirect('/admin/dashboard')->with('status', 'E-mail verificado com sucesso!');
            } else {
                return redirect('/')->with('status', 'E-mail verificado com sucesso!');
            }
        }
        return back()->withErrors(['otp' => 'Código inválido ou expirado.']);
    }
}
