<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * CAIXA CINZA — Gray Box Security Tests
 *
 * Testa com conhecimento parcial do sistema (autenticação + estrutura de rotas):
 * - Separação de papéis (role isolation)
 * - KYC middleware para freelancers
 * - IDOR em notificações
 * - Tentativas de acesso a recursos de outros utilizadores
 * - Comutação de papel (role switching)
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

    private function makeAdmin(): User
    {
        return $this->makeUser('admin');
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

    // ─── IDOR — Insecure Direct Object Reference ─────────────────────────────

    /** Utilizador A não pode abrir notificação do utilizador B */
    public function test_user_cannot_open_another_users_notification(): void
    {
        $userA = $this->makeUser('cliente');
        $userB = $this->makeUser('cliente');

        // Criar notificação pertencente ao utilizador B
        $notification = Notification::create([
            'user_id' => $userB->id,
            'type'    => 'project_cancelled',
            'message' => 'Teste IDOR',
            'read'    => false,
        ]);

        // Utilizador A tenta abrir a notificação de B
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

        // Deve redirecionar para a URL da notificação (302), não 403 nem 500
        $response->assertRedirect();
        $this->assertNotEquals(403, $response->getStatusCode());
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

    // ─── Páginas públicas não perdem dados sensíveis ──────────────────────────

    /** A listagem pública de freelancers não expõe dados sensíveis */
    public function test_public_freelancer_listing_does_not_expose_passwords(): void
    {
        $freelancer = $this->makeFreelancer()->fresh();
        // Garantir que há pelo menos um freelancer activo
        $freelancer->update(['is_active' => true]);

        $response = $this->get('/freelancers');

        $response->assertDontSee($freelancer->password);
        // Payload de hash nunca deve aparecer no HTML
        $this->assertStringNotContainsString('$2y$', $response->getContent());
    }

    /** A página de login não expõe stacks de erro em produção */
    public function test_login_page_does_not_expose_stack_trace(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertDontSee('Illuminate\\');
        $response->assertDontSee('vendor/laravel');
    }
}
