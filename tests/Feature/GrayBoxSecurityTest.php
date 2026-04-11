<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Service;
use App\Models\Wallet;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * CAIXA CINZA — Gray Box Penetration Tests (aprofundado)
 *
 * Atacante autenticado com conhecimento parcial da estrutura interna:
 * - Separação de papéis (role isolation)
 * - KYC middleware bypass
 * - IDOR em notificações, pagamentos escrow, reembolsos, projetos
 * - Manipulação de estado de serviço
 * - Escalada de papel / injeção de sessão
 * - Módulos de admin isolados por sub-role
 * - Filtro de status público expõe dados privados
 * - Double-spend / re-release de pagamento
 * - Mass assignment no modelo User (role, admin_role)
 * - Dados sensíveis não expostos em listagens públicas
 */
class GrayBoxSecurityTest extends TestCase
{
    use RefreshDatabase;

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function makeUser(string $role = 'cliente', array $extra = []): User
    {
        return User::factory()->create(array_merge(['role' => $role], $extra));
    }

    private function makeFreelancer(array $extra = []): User
    {
        return $this->makeUser('freelancer', $extra);
    }

    private function makeAdmin(array $extra = []): User
    {
        return $this->makeUser('admin', $extra);
    }

    /** Cria um Service com dois participantes e estado configurável */
    private function makeService(User $client, User $freelancer, array $extra = []): Service
    {
        return Service::create(array_merge([
            'cliente_id'         => $client->id,
            'freelancer_id'      => $freelancer->id,
            'titulo'             => 'Serviço de Teste',
            'descricao'          => 'Descrição',
            'categoria'          => 'Dev. Web',
            'prazo'              => now()->addDays(7)->toDateString(),
            'valor'              => 50000,
            'status'             => 'delivered',
            'is_payment_released' => false,
        ], $extra));
    }

    // ─── Separação de papéis ─────────────────────────────────────────────────

    /** Cliente autenticado não pode aceder ao dashboard do freelancer */
    public function test_client_cannot_access_freelancer_dashboard(): void
    {
        $client = $this->makeUser('cliente');

        $response = $this->actingAs($client)->get('/freelancer/dashboard');

        // Role middleware redireciona para /dashboard, não para /freelancer/dashboard
        $response->assertRedirect();
        $this->assertNotEquals('/freelancer/dashboard', $response->headers->get('Location'));
    }

    /** Freelancer autenticado não pode aceder ao dashboard do cliente */
    public function test_freelancer_cannot_access_client_dashboard(): void
    {
        $freelancer = $this->makeFreelancer(['kyc_status' => 'verified']);

        $response = $this->actingAs($freelancer)->get('/cliente/dashboard');

        // Role middleware redireciona para /dashboard, não para /cliente/dashboard
        $response->assertRedirect();
        $this->assertNotEquals('/cliente/dashboard', $response->headers->get('Location'));
    }

    /** Cliente não pode aceder a rotas de admin — role:admin devolve 403 */
    public function test_client_cannot_access_admin_dashboard(): void
    {
        $client = $this->makeUser('cliente');

        $response = $this->actingAs($client)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    /** Freelancer não pode aceder a rotas de admin — role:admin devolve 403 */
    public function test_freelancer_cannot_access_admin_dashboard(): void
    {
        $freelancer = $this->makeFreelancer(['kyc_status' => 'verified']);

        $response = $this->actingAs($freelancer)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    /** Admin consegue aceder ao painel de admin */
    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        // 200 OK ou aceitável se Livewire render funcionar
        $this->assertNotEquals(403, $response->getStatusCode());
        $this->assertNotEquals(302, $response->getStatusCode());
    }

    /** Cliente não pode aceder a rotas de briefing de freelancer */
    public function test_client_cannot_access_freelancer_projects(): void
    {
        $client = $this->makeUser('cliente');

        $response = $this->actingAs($client)->get('/freelancer/projetos');

        $response->assertRedirect();
    }

    /** Freelancer não pode aceder a rotas de projetos do cliente */
    public function test_freelancer_cannot_access_client_projects(): void
    {
        $freelancer = $this->makeFreelancer(['kyc_status' => 'verified']);

        $response = $this->actingAs($freelancer)->get('/cliente/projetos');

        $response->assertRedirect();
    }

    // ─── KYC Middleware ──────────────────────────────────────────────────────

    /** Freelancer sem KYC é bloqueado na carteira e redirecionado para /kyc */
    public function test_freelancer_without_kyc_cannot_access_wallet(): void
    {
        $freelancer = $this->makeFreelancer(['kyc_status' => 'pending']);

        $response = $this->actingAs($freelancer)->get('/freelancer/carteira');

        $response->assertRedirectToRoute('kyc.submit');
    }

    /** Freelancer com KYC num estado não-verificado (submitted) é bloqueado igualmente */
    public function test_freelancer_with_null_kyc_cannot_access_wallet(): void
    {
        // kyc_status NOT NULL — usar valor válido diferente de 'verified'
        $freelancer = $this->makeFreelancer(['kyc_status' => 'submitted']);

        $response = $this->actingAs($freelancer)->get('/freelancer/carteira');

        $response->assertRedirectToRoute('kyc.submit');
    }

    /** Freelancer com KYC rejected é bloqueado */
    public function test_freelancer_with_rejected_kyc_cannot_access_wallet(): void
    {
        $freelancer = $this->makeFreelancer(['kyc_status' => 'rejected']);

        $response = $this->actingAs($freelancer)->get('/freelancer/carteira');

        $response->assertRedirectToRoute('kyc.submit');
    }

    /** Freelancer com KYC verified consegue aceder à carteira */
    public function test_freelancer_with_verified_kyc_can_access_wallet(): void
    {
        $freelancer = $this->makeFreelancer(['kyc_status' => 'verified']);

        $response = $this->actingAs($freelancer)->get('/freelancer/carteira');

        // Não deve ser redirecionado para /kyc
        $this->assertNotEquals(
            route('kyc.submit'),
            $response->headers->get('Location')
        );
    }

    /** KYC middleware não bloqueia clientes (apenas freelancers) */
    public function test_kyc_middleware_does_not_block_clients(): void
    {
        // /freelancer/carteira está protegido por role:freelancer primeiro,
        // mas /kyc em si é acessível por qualquer pessoa autenticada
        $client = $this->makeUser('cliente');

        $response = $this->actingAs($client)->get('/kyc');

        // Deve carregar o formulário KYC sem erro
        $this->assertNotEquals(500, $response->getStatusCode());
    }

    // ─── Comutação de papel (role switching) ─────────────────────────────────

    /** Admin não pode fazer switch de papel (apenas clientes e freelancers podem) */
    public function test_user_without_dual_role_cannot_switch(): void
    {
        // canSwitchRole() retorna false apenas para admins
        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->post('/switch-role');

        // Deve ser 403 — abort(403) acionado em canSwitchRole() false
        $response->assertStatus(403);
    }

    /** Cliente puro (sem has_freelancer_profile) não pode fazer switch para freelancer */
    public function test_client_can_switch_role_without_error(): void
    {
        // Utilizador registado como 'cliente' sem perfil de freelancer
        $client = $this->makeUser('cliente', ['has_freelancer_profile' => false]);

        $response = $this->actingAs($client)->post('/switch-role');

        // canSwitchRole() = false → abort(403)
        $response->assertStatus(403);
    }

    // ─── Injeção de sessão / tentativa de escalar papel ──────────────────────

    /**
     * Tentativa de escalar papel via manipulação manual de sessão.
     * O campo 'active_role' na sessão é lido pelo activeRole(),
     * mas o Role middleware valida $user->role (DB) antes de tudo.
     */
    public function test_session_role_injection_does_not_bypass_middleware(): void
    {
        $client = $this->makeUser('cliente');

        // Injetar 'admin' na sessão como se fosse o active_role
        $response = $this->actingAs($client)
            ->withSession(['active_role' => 'admin'])
            ->get('/admin/dashboard');

        // Middleware Role verifica $user->role da DB, não a sessão
        $response->assertStatus(403);
    }

    public function test_session_role_injection_does_not_unlock_freelancer_routes(): void
    {
        $client = $this->makeUser('cliente');

        // Tentar aceder rotas de freelancer injetando sessão
        $response = $this->actingAs($client)
            ->withSession(['active_role' => 'freelancer'])
            ->get('/freelancer/projetos');

        // Role middleware usa activeRole() mas valida DB role ('cliente' ≠ 'freelancer')
        $response->assertRedirect();
    }

    // ─── Mass Assignment — User Model ────────────────────────────────────────

    /** O campo 'role' não está em $fillable — não pode ser escalado via update() */
    public function test_role_field_is_not_mass_assignable(): void
    {
        $client = $this->makeUser('cliente');

        // Tentar escalar role via mass assignment (como se viesse de um form)
        $client->fill(['role' => 'admin']);
        $client->save();

        // Recarregar da DB — role deve permanecer 'cliente'
        $this->assertEquals('cliente', $client->fresh()->role);
    }

    /** O campo 'admin_role' também não está em $fillable */
    public function test_admin_role_field_is_not_mass_assignable(): void
    {
        $client = $this->makeUser('cliente');

        $client->fill(['admin_role' => 'master']);
        $client->save();

        $this->assertNull($client->fresh()->admin_role);
    }

    // ─── IDOR — Escrow / Pagamento ───────────────────────────────────────────

    /** Cliente B não pode libertar pagamento de serviço pertencente ao Cliente A */
    public function test_client_cannot_release_payment_of_another_clients_service(): void
    {
        $clientA    = $this->makeUser('cliente');
        $clientB    = $this->makeUser('cliente');
        $freelancer = $this->makeFreelancer(['kyc_status' => 'verified']);

        $service = $this->makeService($clientA, $freelancer, ['status' => 'delivered']);

        $response = $this->actingAs($clientB)
            ->post("/servico/{$service->id}/liberar-pagamento");

        // Deve ser redirecionado com erro, nunca liberar o pagamento
        $response->assertRedirect();
        $this->assertFalse((bool) $service->fresh()->is_payment_released);
    }

    /** Freelancer não pode libertar o seu próprio pagamento */
    public function test_freelancer_cannot_release_own_payment(): void
    {
        $client     = $this->makeUser('cliente');
        $freelancer = $this->makeFreelancer(['kyc_status' => 'verified']);

        $service = $this->makeService($client, $freelancer, ['status' => 'delivered']);

        $response = $this->actingAs($freelancer)
            ->post("/servico/{$service->id}/liberar-pagamento");

        $response->assertRedirect();
        $this->assertFalse((bool) $service->fresh()->is_payment_released);
    }

    /** Pagamento já libertado não pode ser libertado novamente (double-spend) */
    public function test_payment_cannot_be_released_twice(): void
    {
        $client     = $this->makeUser('cliente');
        $freelancer = $this->makeFreelancer(['kyc_status' => 'verified']);

        $service = $this->makeService($client, $freelancer, [
            'status'              => 'completed',
            'is_payment_released' => true,
            'valor'               => 50000,
        ]);

        Wallet::create(['user_id' => $freelancer->id, 'saldo' => 40000, 'saldo_pendente' => 0, 'saque_minimo' => 0, 'taxa_saque' => 0]);

        $saldoAntes = $freelancer->wallet->saldo;

        $this->actingAs($client)->post("/servico/{$service->id}/liberar-pagamento");

        // O saldo não deve ter sido incrementado uma segunda vez
        $this->assertEquals($saldoAntes, Wallet::where('user_id', $freelancer->id)->first()->saldo);
    }

    /** Serviço não entregue não pode ter pagamento libertado (pré-condição) */
    public function test_undelivered_service_payment_cannot_be_released(): void
    {
        $client     = $this->makeUser('cliente');
        $freelancer = $this->makeFreelancer(['kyc_status' => 'verified']);

        $service = $this->makeService($client, $freelancer, ['status' => 'in_progress']);

        $response = $this->actingAs($client)
            ->post("/servico/{$service->id}/liberar-pagamento");

        $response->assertRedirect();
        $this->assertEquals('in_progress', $service->fresh()->status);
    }

    // ─── IDOR — Reembolso ────────────────────────────────────────────────────

    /** Cliente B não pode solicitar reembolso de serviço de Cliente A */
    public function test_client_cannot_request_refund_of_another_clients_service(): void
    {
        $clientA    = $this->makeUser('cliente');
        $clientB    = $this->makeUser('cliente');
        $freelancer = $this->makeFreelancer(['kyc_status' => 'verified']);

        $service = $this->makeService($clientA, $freelancer, ['status' => 'in_progress']);

        $response = $this->actingAs($clientB)->post("/servico/{$service->id}/solicitar-reembolso", [
            'reason'  => 'atraso',
            'details' => 'O freelancer não entregou no prazo acordado.',
        ]);

        $response->assertStatus(403);
        // Serviço não deve ter sido cancelado
        $this->assertEquals('in_progress', $service->fresh()->status);
    }

    /** Reembolso impossível após pagamento já libertado */
    public function test_cannot_request_refund_after_payment_released(): void
    {
        $client     = $this->makeUser('cliente');
        $freelancer = $this->makeFreelancer(['kyc_status' => 'verified']);

        $service = $this->makeService($client, $freelancer, [
            'status'              => 'completed',
            'is_payment_released' => true,
        ]);

        $response = $this->actingAs($client)->post("/servico/{$service->id}/solicitar-reembolso", [
            'reason'  => 'qualidade',
            'details' => 'A qualidade do trabalho não foi satisfatória.',
        ]);

        $response->assertRedirect();
        // Serviço deve permanecer em 'completed', não ser revertido para 'cancelled'
        $this->assertEquals('completed', $service->fresh()->status);
    }

    // ─── Filtro de status público expõe projetos privados ───────────────────

    /**
     * A listagem pública /projetos deve mostrar apenas 'published'.
     * Um atacante que injecta ?status=in_progress não deve ver projetos em andamento.
     */
    public function test_public_project_listing_only_shows_published_by_default(): void
    {
        $client     = $this->makeUser('cliente');
        $freelancer = $this->makeFreelancer(['kyc_status' => 'verified']);

        $pubService  = $this->makeService($client, $freelancer, ['status' => 'published',   'titulo' => 'Projecto Público']);
        $inprService = $this->makeService($client, $freelancer, ['status' => 'in_progress', 'titulo' => 'Projecto Privado']);

        $response = $this->get('/projetos');

        $response->assertSee('Projecto Público');
        $response->assertDontSee('Projecto Privado');
    }

    // ─── Módulos de admin isolados por sub-role ──────────────────────────────

    /** Admin com sub-role 'suporte' não pode aceder ao módulo 'financeiro' */
    public function test_admin_suporte_cannot_access_financeiro_module(): void
    {
        $admin = $this->makeAdmin(['admin_role' => 'suporte']);

        $response = $this->actingAs($admin)->get('/admin/financeiro');

        $response->assertStatus(403);
    }

    /** Admin com sub-role 'financeiro' não pode aceder ao módulo 'suporte' */
    public function test_admin_financeiro_cannot_access_suporte_module(): void
    {
        $admin = $this->makeAdmin(['admin_role' => 'financeiro']);

        $response = $this->actingAs($admin)->get('/admin/disputas');

        $response->assertStatus(403);
    }

    /** Admin master (admin_role = null) pode aceder a todos os módulos */
    public function test_admin_master_can_access_all_modules(): void
    {
        $master = $this->makeAdmin(['admin_role' => null]);

        $finResponse     = $this->actingAs($master)->get('/admin/financeiro');
        $suporteResponse = $this->actingAs($master)->get('/admin/disputas');

        $this->assertNotEquals(403, $finResponse->getStatusCode());
        $this->assertNotEquals(403, $suporteResponse->getStatusCode());
    }

    /** Admin com sub-role 'suporte' pode aceder ao módulo de disputas */
    public function test_admin_suporte_can_access_own_module(): void
    {
        $admin = $this->makeAdmin(['admin_role' => 'suporte']);

        $response = $this->actingAs($admin)->get('/admin/disputas');

        $this->assertNotEquals(403, $response->getStatusCode());
    }

    // ─── Páginas públicas não expõem dados sensíveis ─────────────────────────

    /** A listagem pública de freelancers não expõe hashes de password */
    public function test_public_freelancer_listing_does_not_expose_passwords(): void
    {
        $freelancer = $this->makeFreelancer()->fresh();

        $response = $this->get('/freelancers');

        $response->assertDontSee($freelancer->password);
        $this->assertStringNotContainsString('$2y$', $response->getContent());
    }

    /** A página de login não expõe stack traces */
    public function test_login_page_does_not_expose_stack_trace(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertDontSee('Illuminate\\');
        $response->assertDontSee('vendor/laravel');
    }

    // ─── IDOR — Notificações ─────────────────────────────────────────────────

    /** Utilizador A não pode abrir notificação do utilizador B */
    public function test_user_cannot_open_another_users_notification(): void
    {
        $userA = $this->makeUser('cliente');
        $userB = $this->makeUser('cliente');

        $notification = Notification::create([
            'user_id' => $userB->id,
            'type'    => 'project_cancelled',
            'message' => 'Teste IDOR',
            'read'    => false,
        ]);

        $response = $this->actingAs($userA)->get("/notificacao/{$notification->id}/abrir");

        $response->assertStatus(403);
    }

    /** Utilizador dono pode abrir a própria notificação */
    public function test_user_can_open_own_notification(): void
    {
        $user = $this->makeUser('cliente');

        $notification = Notification::create([
            'user_id' => $user->id,
            'type'    => 'project_cancelled',
            'message' => 'Aviso de cancelamento',
            'read'    => false,
        ]);

        $response = $this->actingAs($user)->get("/notificacao/{$notification->id}/abrir");

        $response->assertRedirect();
        $this->assertNotEquals(403, $response->getStatusCode());
    }
}
