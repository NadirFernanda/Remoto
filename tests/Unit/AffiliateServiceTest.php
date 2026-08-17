<?php

namespace Tests\Unit;

use App\Models\Affiliate;
use App\Models\Referral;
use App\Models\User;
use App\Services\AffiliateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Programa de afiliados descontinuado. Estes testes confirmam que o serviço
 * não cria mais nenhuma actividade nova (códigos, indicações, comissões),
 * mantendo apenas leitura de dados históricos já existentes.
 */
class AffiliateServiceTest extends TestCase
{
    use RefreshDatabase;

    private AffiliateService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AffiliateService();
    }

    public function test_generate_code_does_not_create_new_affiliate(): void
    {
        $user = User::factory()->create();

        $result = $this->service->generateCode($user);

        $this->assertNull($result);
        $this->assertDatabaseCount('affiliates', 0);
    }

    public function test_generate_code_returns_existing_historical_record_without_duplicating(): void
    {
        $user      = User::factory()->create();
        $affiliate = Affiliate::create([
            'user_id' => $user->id,
            'codigo'  => 'OLDCODE1',
            'ganhos'  => 0,
            'status'  => 'ativo',
        ]);

        $result = $this->service->generateCode($user);

        $this->assertEquals($affiliate->id, $result->id);
        $this->assertDatabaseCount('affiliates', 1);
    }

    public function test_record_referral_does_not_create_referral(): void
    {
        $affiliateUser = User::factory()->create(['affiliate_code' => 'REFCODE1', 'status' => 'active']);
        $newUser       = User::factory()->create();

        $this->service->recordReferral($newUser, 'REFCODE1', Request::create('/register'));

        $this->assertDatabaseCount('referrals', 0);
    }

    public function test_credit_commission_for_referred_action_does_not_touch_wallet(): void
    {
        $affiliateUser = User::factory()->create();
        $actor         = User::factory()->create();
        Referral::create([
            'user_id'      => $actor->id,
            'affiliate_id' => $affiliateUser->id,
            'ip_address'   => '127.0.0.1',
            'user_agent'   => 'test',
        ]);

        $this->service->creditCommissionForReferredAction($actor, 'publish_service', 1);

        $this->assertDatabaseCount('wallet_logs', 0);
    }
}
