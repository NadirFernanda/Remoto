<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\OtpCodeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;

class OtpVerificationController extends Controller
{
    public function showOtpForm()
    {
        $user = Auth::user();

        // Se já está verificado, redireciona direto para o dashboard
        if ($user && $user->hasVerifiedEmail()) {
            return match($user->role) {
                'freelancer' => redirect('/freelancer/dashboard'),
                'admin'      => redirect('/admin/dashboard'),
                'creator'    => redirect('/creator/dashboard'),
                default      => redirect('/cliente/dashboard'),
            };
        }

        // Envia o OTP automaticamente ao exibir o formulário
        if ($user) {
            $otp = rand(100000, 999999);
            Cache::put('otp_' . $user->id, $otp, now()->addMinutes(10));
            Mail::to($user->email)->send(new OtpCodeMail($user, (string) $otp));
        }

        return view('auth.otp-verify');
    }

    public function sendOtp(Request $request)
    {
        $user = Auth::user();
        $otp = rand(100000, 999999);
        Cache::put('otp_' . $user->id, $otp, now()->addMinutes(10));
        Mail::to($user->email)->send(new OtpCodeMail($user, (string) $otp));
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
            return match($user->role) {
                'freelancer' => redirect('/freelancer/dashboard')->with('status', 'E-mail verificado com sucesso!'),
                'admin'      => redirect('/admin/dashboard')->with('status', 'E-mail verificado com sucesso!'),
                'creator'    => redirect('/creator/dashboard')->with('status', 'E-mail verificado com sucesso!'),
                default      => redirect('/cliente/dashboard')->with('status', 'E-mail verificado com sucesso!'),
            };
        }
        return back()->withErrors(['otp' => 'Código inválido ou expirado.']);
    }
}
