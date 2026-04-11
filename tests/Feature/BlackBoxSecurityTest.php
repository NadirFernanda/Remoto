<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * CAIXA PRETA — Black Box Security Tests
 *
 * Testa o sistema como um atacante externo sem conhecimento do código:
 * - Rotas protegidas acessíveis sem autenticação
 * - Throttle de login (brute force)
 * - Tentativas de injecção SQL no campo de pesquisa
 * - Credenciais inválidas não autenticam
 * - Respostas corretas em endpoints JSON sem auth
 */
class BlackBoxSecurityTest extends TestCase
{
    use RefreshDatabase;

    // ─── Autenticação ────────────────────────────────────────────────────────

    /** Visitante não autenticado é redirecionado para /login ao aceder o dashboard */
    public function test_guest_cannot_access_client_dashboard(): void
    {
        $response = $this->get('/cliente/dashboard');
        $response->assertRedirectToRoute('login');
    }

    public function test_guest_cannot_access_freelancer_dashboard(): void
    {
        $response = $this->get('/freelancer/dashboard');
        $response->assertRedirectToRoute('login');
    }

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirectToRoute('login');
    }

    public function test_guest_cannot_access_freelancer_wallet(): void
    {
        $response = $this->get('/freelancer/carteira');
        $response->assertRedirectToRoute('login');
    }

    public function test_guest_cannot_access_client_projects(): void
    {
        $response = $this->get('/cliente/projetos');
        $response->assertRedirectToRoute('login');
    }

    public function test_guest_cannot_access_notifications(): void
    {
        $response = $this->get('/notificacoes');
        $response->assertRedirectToRoute('login');
    }

    public function test_guest_cannot_access_messages(): void
    {
        $response = $this->get('/mensagens');
        $response->assertRedirectToRoute('login');
    }

    public function test_guest_cannot_access_kyc_form(): void
    {
        $response = $this->get('/kyc');
        $response->assertRedirectToRoute('login');
    }

    // ─── Login — credenciais inválidas ───────────────────────────────────────

    /** Login com e-mail inexistente retorna erro, não autentica */
    public function test_login_with_nonexistent_email_fails(): void
    {
        $response = $this->post('/login', [
            'email'    => 'naoexiste@exemplo.com',
            'password' => 'qualquercoisa',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /** Login com senha errada retorna erro, não autentica */
    public function test_login_with_wrong_password_fails(): void
    {
        $user = User::factory()->create(['password' => bcrypt('senha-correta')]);

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'senha-errada',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    /** Login sem e-mail e sem senha retorna erros de validação */
    public function test_login_with_empty_credentials_fails(): void
    {
        $response = $this->post('/login', []);

        $response->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();
    }

    /** Login com e-mail mal formado retorna erro de validação */
    public function test_login_with_invalid_email_format_fails(): void
    {
        $response = $this->post('/login', [
            'email'    => 'nao-e-um-email',
            'password' => 'qualquercoisa',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /** Login com credenciais corretas autentica e redireciona */
    public function test_login_with_valid_credentials_redirects(): void
    {
        $user = User::factory()->create([
            'role'     => 'cliente',
            'password' => bcrypt('senha-certa'),
        ]);

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'senha-certa',
        ]);

        $response->assertRedirect('/cliente/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    // ─── Injecção SQL via campo de pesquisa ──────────────────────────────────

    /** Payload de SQL injection no parâmetro search não deve quebrar o servidor */
    public function test_sql_injection_in_freelancer_search_does_not_crash(): void
    {
        // ' OR 1=1-- é um payload clássico de SQL injection
        $response = $this->get('/freelancers?search=%27+OR+1%3D1--');

        // A página pode retornar 200 (lista vazia) ou 302 — qualquer coisa excepto 500
        $this->assertNotEquals(500, $response->getStatusCode());
    }

    public function test_sql_injection_in_freelancer_search_union_attack(): void
    {
        $response = $this->get('/freelancers?search=%27+UNION+SELECT+null%2C+null%2C+null--');

        $this->assertNotEquals(500, $response->getStatusCode());
    }

    public function test_xss_payload_in_search_does_not_crash(): void
    {
        $response = $this->get('/freelancers?search=%3Cscript%3Ealert(1)%3C/script%3E');

        $this->assertNotEquals(500, $response->getStatusCode());
    }

    // ─── Endpoints protegidos por autenticação (JSON/API) ────────────────────

    public function test_notification_data_endpoint_requires_auth(): void
    {
        $response = $this->getJson('/user/notification-data');

        // Sem auth — deve retornar 401 ou redirecionar (302 em web guards)
        $this->assertTrue(
            in_array($response->getStatusCode(), [401, 302, 403]),
            'O endpoint /user/notification-data deve exigir autenticação. Status: ' . $response->getStatusCode()
        );
    }

    public function test_chat_badge_endpoint_requires_auth(): void
    {
        $response = $this->getJson('/user/chat-badge');

        $this->assertTrue(
            in_array($response->getStatusCode(), [401, 302, 403]),
            'O endpoint /user/chat-badge deve exigir autenticação. Status: ' . $response->getStatusCode()
        );
    }

    /** Rota de logout via POST sem sessão activa deve redirecionar sem erro */
    public function test_logout_without_session_does_not_crash(): void
    {
        $response = $this->post('/logout');

        $this->assertNotEquals(500, $response->getStatusCode());
    }

    // ─── Endpoints de upload sem auth ────────────────────────────────────────

    public function test_chat_file_upload_requires_auth(): void
    {
        $response = $this->postJson('/chat/upload-file', []);

        // 401 é o esperado (controller usa abort_unless(auth()->check(), 401))
        $response->assertStatus(401);
    }
}
