<?php

namespace Tests\Unit;

use App\Models\Affiliate;
use App\Models\User;
use App\Services\AffiliateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AffiliateServiceTest extends TestCase
{
    use RefreshDatabase;

    private AffiliateService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new AffiliateService();
    }

    public function test_generate_code_creates_affiliate_with_correct_status(): void
    {
        $user = User::factory()->create();

        $affiliate = $this->service->generateCode($user);

        $this->assertInstanceOf(Affiliate::class, $affiliate);
        $this->assertEquals('ativo', $affiliate->status);
        $this->assertEquals($user->id, $affiliate->user_id);
    }

    public function test_generate_code_produces_unique_uppercase_code(): void
    {
        $user = User::factory()->create();

        $affiliate = $this->service->generateCode($user);

        $this->assertEquals(8, strlen($affiliate->codigo));
        $this->assertEquals(strtoupper($affiliate->codigo), $affiliate->codigo);
    }

    public function test_generate_code_returns_existing_if_already_generated(): void
    {
        $user = User::factory()->create();

        $first  = $this->service->generateCode($user);
        $second = $this->service->generateCode($user);

        $this->assertEquals($first->id, $second->id);
        $this->assertEquals($first->codigo, $second->codigo);
    }

    public function test_generate_code_code_is_unique_across_users(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $codeA = $this->service->generateCode($userA)->codigo;
        // Force collision on next random (stub). Without a stub,
        // statistical chance of collision with 8 alphanumeric chars
        // (~2.8 trillion combos) is negligible; assert DB uniqueness instead.
        $codeB = $this->service->generateCode($userB)->codigo;

        $this->assertEquals(1, Affiliate::where('codigo', $codeA)->count());
        $this->assertEquals(1, Affiliate::where('codigo', $codeB)->count());
    }

    public function test_generate_code_persisted_to_database(): void
    {
        $user = User::factory()->create();

        $affiliate = $this->service->generateCode($user);

        $this->assertDatabaseHas('affiliates', [
            'user_id' => $user->id,
            'codigo'  => $affiliate->codigo,
            'status'  => 'ativo',
        ]);
    }
}
