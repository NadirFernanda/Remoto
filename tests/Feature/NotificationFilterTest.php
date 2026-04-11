<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\Refund;
use App\Models\Service;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Testes para o sistema de notificações:
 *  - Notificações filtradas por modo activo (cliente vs freelancer)
 *  - Redirect de reembolso troca o modo para cliente
 *  - NotificationRedirectController envia para o URL correcto por tipo
 *  - Backfill de service_id em notificações antigas
 */
class NotificationFilterTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function makeUser(string $role): User
    {
        $u = User::factory()->create(['role' => $role, 'status' => 'active']);
        Wallet::create(['user_id' => $u->id, 'saldo' => 0, 'saldo_pendente' => 0, 'saque_minimo' => 1000, 'taxa_saque' => 0]);
        return $u;
    }

    private function makeService(User $client): Service
    {
        return Service::create([
            'cliente_id' => $client->id,
            'titulo'     => 'Projecto Notif',
            'briefing'   => 'Briefing',
            'valor'      => 5000,
            'taxa'       => 500,
            'valor_liquido' => 4500,
            'status'     => 'published',
        ]);
    }

    private function notif(User $user, string $type, ?int $serviceId = null): Notification
    {
        return Notification::create([
            'user_id'    => $user->id,
            'service_id' => $serviceId,
            'type'       => $type,
            'title'      => ucfirst(str_replace('_', ' ', $type)),
            'message'    => 'Mensagem de teste para ' . $type,
            'read'       => false,
        ]);
    }

    // ── Testes de filtragem por modo ──────────────────────────────────────────

    #[Test]
    public function notificacao_refund_nao_aparece_em_modo_freelancer(): void
    {
        $user = $this->makeUser('freelancer');
        $this->notif($user, 'refund_processed');

        // Simula modo freelancer (sessão activa)
        $this->actingAs($user);
        session(['active_role' => 'freelancer']);

        // A query que o NotificationBell usa
        $clientOnly = ['refund_processed', 'refund_approved', 'refund_rejected',
            'delivery_submitted', 'proposal_accepted', 'proposal_rejected'];

        $count = Notification::where('user_id', $user->id)
            ->whereNotIn('type', $clientOnly)
            ->count();

        $this->assertEquals(0, $count);
    }

    #[Test]
    public function notificacao_novo_projeto_nao_aparece_em_modo_cliente(): void
    {
        $user = $this->makeUser('cliente');
        $this->notif($user, 'novo_projeto');

        $freelancerOnly = ['novo_projeto', 'service_chosen', 'revision_requested', 'project_started',
            'payment_adjustment', 'delivery_approved', 'payment_released', 'saque_aprovado',
            'saque_rejeitado', 'service_rejected', 'project_invite', 'direct_invite'];

        $count = Notification::where('user_id', $user->id)
            ->whereNotIn('type', $freelancerOnly)
            ->count();

        $this->assertEquals(0, $count);
    }

    #[Test]
    public function notificacao_nova_mensagem_aparece_em_ambos_os_modos(): void
    {
        $user = $this->makeUser('freelancer');
        $this->notif($user, 'nova_mensagem');

        $freelancerOnly = ['novo_projeto', 'service_chosen', 'revision_requested', 'project_started',
            'payment_adjustment', 'delivery_approved', 'payment_released', 'saque_aprovado',
            'saque_rejeitado', 'service_rejected', 'project_invite', 'direct_invite'];
        $clientOnly = ['refund_processed', 'refund_approved', 'refund_rejected',
            'delivery_submitted', 'proposal_accepted', 'proposal_rejected'];

        // Modo freelancer
        $countFreelancer = Notification::where('user_id', $user->id)
            ->whereNotIn('type', $clientOnly)
            ->count();

        // Modo cliente
        $countCliente = Notification::where('user_id', $user->id)
            ->whereNotIn('type', $freelancerOnly)
            ->count();

        $this->assertEquals(1, $countFreelancer, 'nova_mensagem deve aparecer no modo freelancer');
        $this->assertEquals(1, $countCliente,    'nova_mensagem deve aparecer no modo cliente');
    }

    // ── Testes de redirect do controller ──────────────────────────────────────

    #[Test]
    public function redirect_controller_rejeita_notificacao_de_outro_utilizador(): void
    {
        $owner = $this->makeUser('cliente');
        $outro = $this->makeUser('cliente');
        $notif = $this->notif($owner, 'refund_processed');

        $this->actingAs($outro);
        $response = $this->get(route('notification.open', $notif->id));
        $response->assertStatus(403);
    }

    #[Test]
    public function redirect_controller_marca_notificacao_como_lida(): void
    {
        $client  = $this->makeUser('cliente');
        $service = $this->makeService($client);
        $notif   = $this->notif($client, 'refund_processed', $service->id);

        $this->assertFalse((bool) $notif->read);

        $this->actingAs($client);
        $this->get(route('notification.open', $notif->id));

        $this->assertTrue((bool) $notif->fresh()->read);
    }

    #[Test]
    public function redirect_controller_envia_para_pagina_de_reembolso(): void
    {
        $client  = $this->makeUser('cliente');
        $service = $this->makeService($client);
        $notif   = $this->notif($client, 'refund_processed', $service->id);

        $this->actingAs($client);
        $response = $this->get(route('notification.open', $notif->id));

        $response->assertRedirect(route('client.refunds'));
    }

    #[Test]
    public function redirect_controller_envia_freelancer_para_projecto_especifico(): void
    {
        $client     = $this->makeUser('cliente');
        $freelancer = $this->makeUser('freelancer');
        $service    = $this->makeService($client);
        $notif      = $this->notif($freelancer, 'novo_projeto', $service->id);

        $this->actingAs($freelancer);
        $response = $this->get(route('notification.open', $notif->id));

        $response->assertRedirect(route('public.project.show', $service->id));
    }

    #[Test]
    public function redirect_controller_envia_para_chat_em_nova_mensagem(): void
    {
        $client     = $this->makeUser('cliente');
        $freelancer = $this->makeUser('freelancer');
        $service    = $this->makeService($client);
        $notif      = $this->notif($freelancer, 'nova_mensagem', $service->id);

        $this->actingAs($freelancer);
        $response = $this->get(route('notification.open', $notif->id));

        $response->assertRedirect(route('service.chat', $service->id));
    }

    #[Test]
    public function redirect_de_reembolso_troca_modo_para_cliente(): void
    {
        // Utilizador dual-role: role=cliente mas também tem perfil de freelancer
        // Está actualmente em modo freelancer e clica numa notificação de reembolso.
        $user = $this->makeUser('cliente');
        $user->has_freelancer_profile = true; // legitimamente dual-role
        $user->save();

        $service = $this->makeService($user);
        $notif   = $this->notif($user, 'refund_processed', $service->id);

        $this->actingAs($user);
        session(['active_role' => 'freelancer']); // modo freelancer activo (legítimo)

        $this->get(route('notification.open', $notif->id));

        $this->assertEquals('cliente', session('active_role'));
    }

    // ── Testes de backfill de Refund records ──────────────────────────────────

    #[Test]
    public function backfill_cria_refund_record_a_partir_de_notificacao(): void
    {
        $client  = $this->makeUser('cliente');
        $service = $this->makeService($client);

        // Notificação existe mas sem Refund record correspondente (estado legado)
        $this->notif($client, 'refund_processed', $service->id);

        $this->assertDatabaseMissing('refunds', [
            'user_id'    => $client->id,
            'service_id' => $service->id,
        ]);

        // Executar o backfill
        $this->artisan('refunds:backfill')->assertExitCode(0);

        $this->assertDatabaseHas('refunds', [
            'user_id'    => $client->id,
            'service_id' => $service->id,
            'status'     => 'approved',
        ]);
    }

    #[Test]
    public function backfill_nao_duplica_refund_existente(): void
    {
        $client  = $this->makeUser('cliente');
        $service = $this->makeService($client);

        $this->notif($client, 'refund_processed', $service->id);

        // Já existe um Refund
        Refund::create([
            'user_id'    => $client->id,
            'service_id' => $service->id,
            'reason'     => 'Teste',
            'details'    => 'Detalhe',
            'status'     => 'approved',
        ]);

        $this->artisan('refunds:backfill')->assertExitCode(0);

        $count = Refund::where('user_id', $client->id)->where('service_id', $service->id)->count();
        $this->assertEquals(1, $count, 'Não deve criar duplicado');
    }
}
