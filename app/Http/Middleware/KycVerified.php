<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class KycVerified
{
    /**
     * Bloqueia freelancers cujo KYC ainda não está aprovado.
     * Utilizadores com outros papéis passam sem restrição.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->role === 'freelancer') {
            $profile = $user->freelancerProfile;

            if (!$profile || $profile->kyc_status !== 'approved') {
                return redirect()->route('kyc.submit')
                    ->with('warning', 'É necessário ter o KYC aprovado para aceder a esta funcionalidade. Complete a sua verificação de identidade.');
            }
        }

        return $next($request);
    }
}
