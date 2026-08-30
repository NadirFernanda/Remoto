<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Modules\Admin\Services\AuditLogger;
use App\Services\TwoFactorAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    public function __construct(private TwoFactorAuthService $twoFactor)
    {
    }

    public function showSetup(Request $request): View|RedirectResponse
    {
        abort_if($request->user()->role !== 'admin', 404);

        if ($request->user()->two_factor_confirmed_at !== null) {
            return redirect()->route('2fa.challenge');
        }

        $secret = session('2fa_setup_secret') ?? $this->twoFactor->generateSecret();
        session(['2fa_setup_secret' => $secret]);

        return view('auth.two-factor-setup', [
            'qrSvg'  => $this->twoFactor->qrCodeSvg($request->user(), $secret),
            'secret' => $secret,
        ]);
    }

    public function confirmSetup(Request $request): RedirectResponse
    {
        abort_if($request->user()->role !== 'admin', 404);

        $request->validate(['code' => 'required|string']);

        $secret = session('2fa_setup_secret');

        if (!$secret || !$this->twoFactor->verify($secret, $request->string('code'))) {
            return back()->withErrors(['code' => 'Código inválido. Tente novamente.']);
        }

        $user = $request->user();
        $plaintextCodes = $this->twoFactor->generateRecoveryCodes();

        $user->two_factor_secret = $secret;
        $user->two_factor_recovery_codes = $this->twoFactor->hashRecoveryCodes($plaintextCodes);
        $user->two_factor_confirmed_at = now();
        $user->save();

        session()->forget('2fa_setup_secret');
        session(['2fa_passed_at' => now(), '2fa_recovery_codes' => $plaintextCodes]);

        AuditLogger::log('2fa_enrolled', "2FA configurado por {$user->name} ({$user->email}).", 'User', $user->id, category: 'sistema');

        return redirect()->route('2fa.recovery-codes');
    }

    public function showRecoveryCodes(Request $request): View|RedirectResponse
    {
        $codes = session('2fa_recovery_codes');

        if (!$codes) {
            return redirect()->route('2fa.challenge');
        }

        return view('auth.two-factor-recovery-codes', ['codes' => $codes]);
    }

    public function acknowledgeRecoveryCodes(Request $request): RedirectResponse
    {
        session()->forget('2fa_recovery_codes');

        return $this->redirectForRole($request->user());
    }

    public function showChallenge(Request $request): View|RedirectResponse
    {
        abort_if($request->user()->role !== 'admin', 404);

        if ($request->user()->two_factor_confirmed_at === null) {
            return redirect()->route('2fa.setup');
        }

        if (session('2fa_passed_at')) {
            return $this->redirectForRole($request->user());
        }

        return view('auth.two-factor-challenge');
    }

    public function verifyChallenge(Request $request): RedirectResponse
    {
        abort_if($request->user()->role !== 'admin', 404);

        $request->validate([
            'code'          => 'nullable|string',
            'recovery_code' => 'nullable|string',
        ]);

        $user = $request->user();

        if ($request->filled('recovery_code')) {
            if ($this->twoFactor->consumeRecoveryCode($user, $request->string('recovery_code'))) {
                session(['2fa_passed_at' => now()]);
                $request->session()->regenerate();
                AuditLogger::log('2fa_recovery_code_used', "Código de recuperação usado por {$user->name} ({$user->email}). Recomenda-se regenerar os códigos.", 'User', $user->id, category: 'sistema');

                return $this->redirectForRole($user)->with('warning', 'Usou um código de recuperação. Regenere novos códigos em "Meu Perfil".');
            }

            AuditLogger::log('2fa_challenge_failed', "Código de recuperação inválido para {$user->name} ({$user->email}).", 'User', $user->id, category: 'sistema');

            return back()->withErrors(['recovery_code' => 'Código de recuperação inválido.']);
        }

        if (!$request->filled('code') || !$this->twoFactor->verify($user->two_factor_secret, $request->string('code'))) {
            AuditLogger::log('2fa_challenge_failed', "Código 2FA inválido para {$user->name} ({$user->email}).", 'User', $user->id, category: 'sistema');

            return back()->withErrors(['code' => 'Código inválido. Tente novamente.']);
        }

        session(['2fa_passed_at' => now()]);
        $request->session()->regenerate();

        return $this->redirectForRole($user);
    }

    private function redirectForRole($user): RedirectResponse
    {
        return match ($user->role) {
            'admin' => redirect()->intended('/admin/dashboard'),
            default => redirect()->intended('/'),
        };
    }
}
