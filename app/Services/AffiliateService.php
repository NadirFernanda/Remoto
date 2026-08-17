<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Programa de afiliados descontinuado (ver decisão do produto). Esta classe
 * só existe para não partir os call sites históricos espalhados pela
 * plataforma — todos os métodos são no-ops e não criam mais actividade nova.
 * Dados históricos (Affiliate, Referral, WalletLog de comissões antigas)
 * permanecem intactos na base de dados para efeitos de relatório.
 */
class AffiliateService
{
    /**
     * Programa de afiliados descontinuado: já não são gerados novos códigos.
     * Mantido apenas para não partir referências antigas; dados históricos preservados.
     */
    public function generateCode(User $user): ?Affiliate
    {
        return Affiliate::where('user_id', $user->id)->first();
    }

    /**
     * Programa de afiliados descontinuado: já não são registadas novas indicações.
     */
    public function recordReferral(User $newUser, string $affiliateCode, Request $request): void
    {
        return;
    }

    /**
     * Programa de afiliados descontinuado: já não são creditadas novas comissões.
     */
    public function creditCommission(User $affiliate, User $referred, float $commission, string $reason = 'purchase'): void
    {
        return;
    }

    /**
     * Programa de afiliados descontinuado: já não são creditadas novas comissões.
     */
    public function creditCommissionForReferredAction(User $actor, string $actionType, ?int $referenceId = null): void
    {
        return;
    }
}
