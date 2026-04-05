<?php

namespace Tests\Feature;

use App\Models\FreelancerProfile;
use App\Models\Service;
use App\Models\ServiceCandidate;
use App\Models\User;
use App\Models\Wallet;
use App\Services\FeeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Testes para o ciclo de vida do Service e candidaturas (ServiceCandidate).
 *
 * Cobre:
 *  - Criação e status inicial do serviço
 *  - Fluxo de estados: published → in_progress → delivered → completed
 *  - Candidatura de freelancers
 *  - Escolha de candidato e rejeição dos restantes
 *  - Cálculo de valor_liquido baseado na nova taxa (10%)
 */
class ServiceLifecycleTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function makeClient(): User
    {
        $client = User::factory()->create(['role' => 'cliente', 'status' => 'active']);
        Wallet::create(['user_id' => $client->id, 'saldo' => 100000, 'saldo_pendente' => 0, 'saque_minimo' => 1000, 'taxa_saque' => 0]);
        return $client;
    }

    private function makeFreelancer(): User
    {
        $freelancer = User::factory()->create(['role' => 'freelancer', 'status' => 'active', 'kyc_status' => 'verified']);
        FreelancerProfile::create(['user_id' => $freelancer->id, 'kyc_status' => 'verified', 'skills' => [], 'languages' => []]);
        Wallet::create(['user_id' => $freelancer->id, 'saldo' => 0, 'saldo_pendente' => 0, 'saque_minimo' => 1000, 'taxa_saque' => 0]);
        return $freelancer;
    }

    private function makeService(User $client, string $status = 'published', float $valor = 10000): Service
    {
        $fee = (new FeeService())->calculateServiceFee($valor);
        return Service::create([
            'cliente_id'    => $client->id,
            'titulo'        => 'Teste Lifecycle',
            'briefing'      => 'Briefing de teste',
            'valor'         => $valor,
            'taxa'          => $fee['taxa'],
            'valor_liquido' => $fee['valor_liquido'],
            'status'        => $status,
        ]);
    }

    // ── Criação ───────────────────────────────────────────────────────────────

    #[Test]
    public function servico_criado_com_status_published(): void
    {
        $client  = $this->makeClient();
        $service = $this->makeService($client);

        $this->assertDatabaseHas('services', [
            'cliente_id' => $client->id,
            'status'     => 'published',
        ]);
    }

    #[Test]
    public function valor_liquido_e_90_porcento_do_valor(): void
    {
        $client  = $this->makeClient();
        $service = $this->makeService($client, 'published', 10000);

        $this->assertEquals(9000.0, (float) $service->valor_liquido);
        $this->assertEquals(1000.0, (float) $service->taxa);
    }

    #[Test]
    public function taxa_e_10_porcento_do_valor(): void
    {
        foreach ([5000, 20000, 100000] as $valor) {
            $client  = $this->makeClient();
            $service = $this->makeService($client, 'published', $valor);

            $this->assertEquals(round($valor * 0.10, 2), (float) $service->taxa, "Taxa incorrecta para valor {$valor}");
            $this->assertEquals(round($valor * 0.90, 2), (float) $service->valor_liquido, "Valor líquido incorrecto para valor {$valor}");
        }
    }

    // ── Fluxo de estados ──────────────────────────────────────────────────────

    #[Test]
    public function servico_transita_de_published_para_in_progress(): void
    {
        $client     = $this->makeClient();
        $freelancer = $this->makeFreelancer();
        $service    = $this->makeService($client);

        $service->freelancer_id = $freelancer->id;
        $service->status        = 'in_progress';
        $service->save();

        $this->assertEquals('in_progress', $service->fresh()->status);
    }

    #[Test]
    public function servico_transita_de_in_progress_para_delivered(): void
    {
        $client     = $this->makeClient();
        $freelancer = $this->makeFreelancer();
        $service    = $this->makeService($client, 'in_progress');

        $service->freelancer_id      = $freelancer->id;
        $service->status             = 'delivered';
        $service->delivery_message   = 'Entrega concluída.';
        $service->save();

        $this->assertEquals('delivered', $service->fresh()->status);
        $this->assertNotNull($service->fresh()->delivery_message);
    }

    #[Test]
    public function servico_transita_de_delivered_para_completed(): void
    {
        $client     = $this->makeClient();
        $freelancer = $this->makeFreelancer();
        $service    = $this->makeService($client, 'delivered');
        $service->freelancer_id = $freelancer->id;
        $service->save();

        $service->status             = 'completed';
        $service->is_payment_released = true;
        $service->payment_released_at = now();
        $service->save();

        $fresh = $service->fresh();
        $this->assertEquals('completed', $fresh->status);
        $this->assertTrue((bool) $fresh->is_payment_released);
    }

    #[Test]
    public function servico_soft_delete_nao_apaga_registo(): void
    {
        $client  = $this->makeClient();
        $service = $this->makeService($client);

        $service->delete();

        $this->assertSoftDeleted('services', ['id' => $service->id]);
        $this->assertNull(Service::find($service->id));
        $this->assertNotNull(Service::withTrashed()->find($service->id));
    }

    // ── Candidaturas ──────────────────────────────────────────────────────────

    #[Test]
    public function freelancer_pode_candidatar_se_ao_servico(): void
    {
        $client     = $this->makeClient();
        $freelancer = $this->makeFreelancer();
        $service    = $this->makeService($client);

        $candidate = ServiceCandidate::create([
            'service_id'   => $service->id,
            'freelancer_id' => $freelancer->id,
            'status'       => 'pending',
        ]);

        $this->assertDatabaseHas('service_candidates', [
            'service_id'    => $service->id,
            'freelancer_id' => $freelancer->id,
            'status'        => 'pending',
        ]);
        $this->assertEquals($freelancer->id, $candidate->freelancer->id);
    }

    #[Test]
    public function freelancer_pode_enviar_proposta_com_valor(): void
    {
        $client     = $this->makeClient();
        $freelancer = $this->makeFreelancer();
        $service    = $this->makeService($client);

        $fee = (new FeeService())->calculateServiceFee(12000);

        $candidate = ServiceCandidate::create([
            'service_id'       => $service->id,
            'freelancer_id'    => $freelancer->id,
            'status'           => 'proposal_sent',
            'proposal_message' => 'Posso fazer por 12.000 Kz.',
            'proposal_value'   => 12000,
            'proposal_fee'     => $fee['taxa'],
            'proposal_net'     => $fee['valor_liquido'],
        ]);

        $this->assertEquals(12000, (float) $candidate->proposal_value);
        $this->assertEquals(1200.0, (float) $candidate->proposal_fee);   // 10%
        $this->assertEquals(10800.0, (float) $candidate->proposal_net);  // 90%
    }

    #[Test]
    public function escolher_candidato_rejeita_os_restantes(): void
    {
        $client      = $this->makeClient();
        $freelancerA = $this->makeFreelancer();
        $freelancerB = $this->makeFreelancer();
        $service     = $this->makeService($client);

        $candidateA = ServiceCandidate::create(['service_id' => $service->id, 'freelancer_id' => $freelancerA->id, 'status' => 'pending']);
        $candidateB = ServiceCandidate::create(['service_id' => $service->id, 'freelancer_id' => $freelancerB->id, 'status' => 'pending']);

        // Escolher A
        $candidateA->status = 'chosen';
        $candidateA->save();

        $service->candidates()->where('id', '!=', $candidateA->id)->update(['status' => 'rejected']);

        $this->assertEquals('chosen',   ServiceCandidate::find($candidateA->id)->status);
        $this->assertEquals('rejected', ServiceCandidate::find($candidateB->id)->status);
    }

    #[Test]
    public function servico_tem_relacao_com_candidatos(): void
    {
        $client     = $this->makeClient();
        $freelancer = $this->makeFreelancer();
        $service    = $this->makeService($client);

        ServiceCandidate::create(['service_id' => $service->id, 'freelancer_id' => $freelancer->id, 'status' => 'pending']);

        $this->assertEquals(1, $service->candidates()->count());
        $this->assertInstanceOf(ServiceCandidate::class, $service->candidates->first());
    }
}
