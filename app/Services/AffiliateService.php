<?php

namespace App\Services;

use App\Events\AffiliateCommissionEarned;
use App\Models\Affiliate;
use App\Models\Referral;
use App\Models\User;
use App\Models\WalletLog;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AffiliateService
{
    /**
     * Comissão fixa por registo via link de afiliado (em AOA).
     */
    public const SIGNUP_COMMISSION = 200.0;

    /**
     * Limite de indicações por IP por dia (anti-fraude).
     */
    public const MAX_REFERRALS_PER_IP_PER_DAY = 10;

    /**
     * Gera um código de afiliado único para o utilizador e cria o registo Affiliate.
     * Se já existir, retorna o existente sem criar duplicado.
     */
    public function generateCode(User $user): Affiliate
    {
        $existing = Affiliate::where('user_id', $user->id)->first();
        if ($existing) {
            if (empty($user->affiliate_code)) {
                $user->affiliate_code = $existing->codigo;
                $user->save();
            }
            return $existing;
        }

        // Garantia de unicidade: do-while + DB UNIQUE constraint como rede de segurança.
        // O try-catch trata a janela de concorrência (dois pedidos simultâneos).
        $code = $user->affiliate_code;
        if (empty($code)) {
            do {
                $code = strtoupper(Str::random(8));
            } while (Affiliate::where('codigo', $code)->exists() || User::where('affiliate_code', $code)->exists());
        }

        try {
            if ($user->affiliate_code !== $code) {
                $user->affiliate_code = $code;
                $user->save();
            }

            return Affiliate::create([
                'user_id' => $user->id,
                'codigo'  => $code,
                'ganhos'  => 0,
                'status'  => 'ativo',
            ]);
        } catch (UniqueConstraintViolationException) {
            // Colisão de corrida: outro processo criou o mesmo código; recomeçar.
            return $this->generateCode($user);
        }
    }

    /**
     * Regista uma indicação a partir de um código de afiliado na URL.
     * Aplica protecções anti-fraude: sem auto-indicação, sem duplicados, limite por IP.
     */
    public function recordReferral(User $newUser, string $affiliateCode, Request $request): void
    {
        $affiliate = User::where('affiliate_code', $affiliateCode)
            ->where('status', 'active')
            ->first();

        if (!$affiliate || $affiliate->id === $newUser->id) {
            return;
        }

        // Sem indicações duplicadas para o mesmo utilizador
        if (Referral::where('user_id', $newUser->id)->exists()) {
            return;
        }

        // Limite por IP por dia
        $ip = $request->ip();
        $todayCount = Referral::where('ip_address', $ip)
            ->where('created_at', '>=', now()->startOfDay())
            ->count();

        if ($todayCount >= self::MAX_REFERRALS_PER_IP_PER_DAY) {
            return;
        }

        Referral::create([
            'user_id'      => $newUser->id,
            'affiliate_id' => $affiliate->id,
            'ip_address'   => $ip,
            'user_agent'   => $request->userAgent(),
        ]);

        // Disparar evento para creditar comissão via listener assíncrono
        AffiliateCommissionEarned::dispatch($affiliate, $newUser, self::SIGNUP_COMMISSION, 'signup');
    }

    /**
     * Credita uma comissão personalizada ao afiliado (ex: por compra de infoproduto).
     * Usado directamente quando o valor da comissão é calculado externamente.
     */
    public function creditCommission(User $affiliate, User $referred, float $commission, string $reason = 'purchase'): void
    {
        AffiliateCommissionEarned::dispatch($affiliate, $referred, $commission, $reason);
    }

    /**
     * Credita comissão fixa ao afiliado dono do link quando o referido executa
     * uma ação elegível na plataforma.
     */
    public function creditCommissionForReferredAction(User $actor, string $actionType, ?int $referenceId = null): void
    {
        $referral = Referral::where('user_id', $actor->id)->first();
        if (!$referral || $referral->affiliate_id === $actor->id) {
            return;
        }

        $affiliate = User::where('id', $referral->affiliate_id)
            ->where('status', 'active')
            ->first();

        if (!$affiliate) {
            return;
        }

        $refKey = $referenceId ?? 0;
        $marker = '[AFF_ACTION:' . $actionType . ':' . $refKey . ':USER' . $actor->id . ']';

        $alreadyCredited = WalletLog::where('user_id', $affiliate->id)
            ->where('tipo', 'comissao_afiliado')
            ->where('descricao', 'like', '%' . $marker . '%')
            ->exists();

        if ($alreadyCredited) {
            return;
        }

        $reason = 'action:' . $actionType . ':' . $refKey . ':' . $actor->id;
        AffiliateCommissionEarned::dispatch($affiliate, $actor, self::SIGNUP_COMMISSION, $reason);
    }
}
