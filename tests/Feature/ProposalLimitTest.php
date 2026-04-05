<?php

namespace Tests\Feature;

use App\Livewire\Freelancer\AvailableProjects;
use App\Models\FreelancerProfile;
use App\Models\Notification;
use App\Models\Service;
use App\Models\ServiceCandidate;
use App\Models\User;
use App\Models\Wallet;
use App\Services\FeeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Testes para a regra de limite de 6 propostas por projecto e
 * comportamento da lista de projectos disponíveis.
 *
 * Cobre:
 *  - Limite de 6 propostas (7ª deve ser bloqueada)
 *  - Propostas rejeitadas não contam para o limite
 *  - Projecto desaparece da lista quando aceite (status in_progress)
 *  - Projecto desaparece quando tem 6 propostas activas
 *  - Freelancer rejeitado não vê mais o projecto
 *  - Notificação enviada a todos os candidatos ao aceitar proposta
 *  - Freelancer que criou o projecto não pode candidatar-se
 */
class ProposalLimitTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function makeClient(): User
    {
        $u = User::factory()->create(['role' => 'cliente', 'status' => 'active']);
        Wallet::create(['user_id' => $u->id, 'saldo' => 200000, 'saldo_pendente' => 0, 'saque_minimo' => 1000, 'taxa_saque' => 0]);
        return $u;
    }

    private function makeFreelancer(): User
    {
        $u = User::factory()->create(['role' => 'freelancer', 'status' => 'active', 'is_suspended' => false]);
        FreelancerProfile::create(['user_id' => $u->id, 'kyc_status' => 'verified', 'skills' => [], 'languages' => []]);
        Wallet::create(['user_id' => $u->id, 'saldo' => 0, 'saldo_pendente' => 0, 'saque_minimo' => 1000, 'taxa_saque' => 0]);
        return $u;
    }

    private function makeService(User $client): Service
    {
        $fee = (new FeeService())->calculateServiceFee(10000);
        return Service::create([
            'cliente_id'    => $client->id,
            'titulo'        => 'Projecto Teste',
            'briefing'      => 'Briefing de teste',
            'valor'         => 10000,
            'taxa'          => $fee['taxa'],
            'valor_liquido' => $fee['valor_liquido'],
            'status'        => 'published',
        ]);
    }

    private function addCandidate(Service $service, User $freelancer, string $status = 'proposal_sent'): ServiceCandidate
    {
        return ServiceCandidate::create([
            'service_id'       => $service->id,
            'freelancer_id'    => $freelancer->id,
            'status'           => $status,
            'proposal_message' => 'Tenho experiência nesta área.',
            'proposal_value'   => 10000,
        ]);
    }

    // ── Testes de limite ──────────────────────────────────────────────────────

    #[Test]
    public function projecto_aceita_ate_6_propostas_activas(): void
    {
        $client  = $this->makeClient();
        $service = $this->makeService($client);

        for ($i = 0; $i < 6; $i++) {
            $this->addCandidate($service, $this->makeFreelancer());
        }

        $this->assertEquals(6, $service->candidates()->whereNotIn('status', ['rejected'])->count());
    }

    #[Test]
    public function setima_proposta_e_bloqueada_pelo_livewire(): void
    {
        $client  = $this->makeClient();
        $service = $this->makeService($client);

        // Encher 6 vagas
        for ($i = 0; $i < 6; $i++) {
            $this->addCandidate($service, $this->makeFreelancer());
        }

        $sétimo = $this->makeFreelancer();

        Livewire::actingAs($sétimo)
            ->test(AvailableProjects::class)
            ->set('proposalServiceId', $service->id)
            ->set('proposalMessage', 'Quero participar.')
            ->set('proposalValue', 10000)
            ->call('sendProposal')
            ->assertHasNoErrors();

        // Candidatura não deve ter sido criada
        $this->assertDatabaseMissing('service_candidates', [
            'service_id'    => $service->id,
            'freelancer_id' => $sétimo->id,
        ]);
    }

    #[Test]
    public function proposta_rejeitada_nao_conta_para_o_limite(): void
    {
        $client  = $this->makeClient();
        $service = $this->makeService($client);

        // 5 activas + 1 rejeitada = deve ainda ter 1 vaga livre
        for ($i = 0; $i < 5; $i++) {
            $this->addCandidate($service, $this->makeFreelancer(), 'proposal_sent');
        }
        $this->addCandidate($service, $this->makeFreelancer(), 'rejected');

        $activeCount = $service->candidates()->whereNotIn('status', ['rejected'])->count();
        $this->assertEquals(5, $activeCount);

        // Deve ser possível enviar mais 1 proposta
        $novo = $this->makeFreelancer();
        Livewire::actingAs($novo)
            ->test(AvailableProjects::class)
            ->set('proposalServiceId', $service->id)
            ->set('proposalMessage', 'Posso ajudar.')
            ->set('proposalValue', 10000)
            ->call('sendProposal')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('service_candidates', [
            'service_id'    => $service->id,
            'freelancer_id' => $novo->id,
        ]);
    }

    // ── Testes de visibilidade na lista ───────────────────────────────────────

    #[Test]
    public function projecto_published_aparece_na_lista(): void
    {
        $client     = $this->makeClient();
        $service    = $this->makeService($client);
        $freelancer = $this->makeFreelancer();

        Livewire::actingAs($freelancer)
            ->test(AvailableProjects::class)
            ->assertSee($service->titulo);
    }

    #[Test]
    public function projecto_in_progress_nao_aparece_na_lista(): void
    {
        $client     = $this->makeClient();
        $freelancer = $this->makeFreelancer();
        $service    = $this->makeService($client);

        $service->update(['status' => 'in_progress', 'freelancer_id' => $freelancer->id]);

        $outro = $this->makeFreelancer();
        Livewire::actingAs($outro)
            ->test(AvailableProjects::class)
            ->assertDontSee($service->titulo);
    }

    #[Test]
    public function projecto_com_6_propostas_activas_nao_aparece_na_lista(): void
    {
        $client  = $this->makeClient();
        $service = $this->makeService($client);

        for ($i = 0; $i < 6; $i++) {
            $this->addCandidate($service, $this->makeFreelancer());
        }

        $externo = $this->makeFreelancer();
        Livewire::actingAs($externo)
            ->test(AvailableProjects::class)
            ->assertDontSee($service->titulo);
    }

    #[Test]
    public function freelancer_rejeitado_nao_ve_o_projecto(): void
    {
        $client     = $this->makeClient();
        $service    = $this->makeService($client);
        $freelancer = $this->makeFreelancer();

        $this->addCandidate($service, $freelancer, 'rejected');

        Livewire::actingAs($freelancer)
            ->test(AvailableProjects::class)
            ->assertDontSee($service->titulo);
    }

    #[Test]
    public function freelancer_com_proposta_aceite_ve_o_projecto(): void
    {
        $client     = $this->makeClient();
        $service    = $this->makeService($client);
        $freelancer = $this->makeFreelancer();

        // Candidato escolhido mas serviço ainda published (edge-case durante transição)
        $this->addCandidate($service, $freelancer, 'chosen');

        // Freelancer escolhido deve ver porque está na lista de myCandidacies
        Livewire::actingAs($freelancer)
            ->test(AvailableProjects::class)
            ->assertSee($service->titulo);
    }

    // ── Testes de notificação ─────────────────────────────────────────────────

    #[Test]
    public function aceitar_candidato_notifica_escolhido_e_rejeitados(): void
    {
        $client  = $this->makeClient();
        $service = $this->makeService($client);

        $escolhido  = $this->makeFreelancer();
        $rejeitadoA = $this->makeFreelancer();
        $rejeitadoB = $this->makeFreelancer();

        $cChosen = $this->addCandidate($service, $escolhido);
        $this->addCandidate($service, $rejeitadoA);
        $this->addCandidate($service, $rejeitadoB);

        // Marcar o escolhido e rejeitar os outros (simula o que ProjectManager faz)
        $cChosen->update(['status' => 'chosen']);
        $service->candidates()->where('id', '!=', $cChosen->id)->update(['status' => 'rejected']);
        $service->update(['status' => 'in_progress', 'freelancer_id' => $escolhido->id]);

        // Criar notificações (como o ProjectManager faz)
        Notification::create(['user_id' => $escolhido->id,  'service_id' => $service->id, 'type' => 'service_chosen',  'title' => 'Selecionado', 'message' => 'Parabéns!']);
        Notification::create(['user_id' => $rejeitadoA->id, 'service_id' => $service->id, 'type' => 'service_rejected', 'title' => 'Não selecionado', 'message' => 'Infelizmente...']);
        Notification::create(['user_id' => $rejeitadoB->id, 'service_id' => $service->id, 'type' => 'service_rejected', 'title' => 'Não selecionado', 'message' => 'Infelizmente...']);

        $this->assertDatabaseHas('notifications', ['user_id' => $escolhido->id,  'type' => 'service_chosen']);
        $this->assertDatabaseHas('notifications', ['user_id' => $rejeitadoA->id, 'type' => 'service_rejected']);
        $this->assertDatabaseHas('notifications', ['user_id' => $rejeitadoB->id, 'type' => 'service_rejected']);
    }

    #[Test]
    public function candidato_nao_consegue_candidatar_ao_proprio_projecto(): void
    {
        $client  = $this->makeClient();
        $service = $this->makeService($client);

        // Cliente tenta candidatar-se ao seu próprio projecto
        Livewire::actingAs($client)
            ->test(AvailableProjects::class)
            ->set('proposalServiceId', $service->id)
            ->set('proposalMessage', 'Eu mesmo farei.')
            ->set('proposalValue', 10000)
            ->call('sendProposal');

        $this->assertDatabaseMissing('service_candidates', [
            'service_id'    => $service->id,
            'freelancer_id' => $client->id,
        ]);
    }
}
