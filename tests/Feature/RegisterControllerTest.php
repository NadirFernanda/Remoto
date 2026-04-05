<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Testes de integração para RegisterController.
 *
 * Cobre registo de cliente e freelancer, validação de inputs,
 * unicidade de e-mail, assignação de role e código de afiliado.
 */
class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    // ── Registo de cliente ────────────────────────────────────────────────────

    #[Test]
    public function cliente_pode_registar_com_dados_validos(): void
    {
        $response = $this->post('/register/client', [
            'name'                  => 'Ana Cliente',
            'email'                 => 'ana@example.com',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
            'role'                  => 'client',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseHas('users', [
            'email' => 'ana@example.com',
            'role'  => 'cliente',
        ]);
    }

    #[Test]
    public function cliente_recebe_codigo_de_afiliado_unico(): void
    {
        $this->post('/register/client', [
            'name'                  => 'Bob Cliente',
            'email'                 => 'bob@example.com',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
            'role'                  => 'client',
        ]);

        $user = User::where('email', 'bob@example.com')->first();

        $this->assertNotNull($user->affiliate_code);
        $this->assertEquals(8, strlen($user->affiliate_code));
        $this->assertEquals(strtoupper($user->affiliate_code), $user->affiliate_code);
    }

    #[Test]
    public function cliente_nao_pode_registar_com_email_duplicado(): void
    {
        User::factory()->create(['email' => 'duplo@example.com']);

        $response = $this->post('/register/client', [
            'name'                  => 'Duplo',
            'email'                 => 'duplo@example.com',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
            'role'                  => 'client',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertEquals(1, User::where('email', 'duplo@example.com')->count());
    }

    #[Test]
    public function cliente_nao_pode_registar_sem_nome(): void
    {
        $response = $this->post('/register/client', [
            'name'                  => '',
            'email'                 => 'semnom@example.com',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
            'role'                  => 'client',
        ]);

        $response->assertSessionHasErrors('name');
    }

    #[Test]
    public function cliente_nao_pode_registar_com_senhas_diferentes(): void
    {
        $response = $this->post('/register/client', [
            'name'                  => 'Teste',
            'email'                 => 'teste@example.com',
            'password'              => 'secret123',
            'password_confirmation' => 'different456',
            'role'                  => 'client',
        ]);

        $response->assertSessionHasErrors('password');
    }

    #[Test]
    public function cliente_nao_pode_registar_com_senha_curta(): void
    {
        $response = $this->post('/register/client', [
            'name'                  => 'Short',
            'email'                 => 'short@example.com',
            'password'              => '123',
            'password_confirmation' => '123',
            'role'                  => 'client',
        ]);

        $response->assertSessionHasErrors('password');
    }

    // ── Registo de freelancer ─────────────────────────────────────────────────

    #[Test]
    public function freelancer_pode_registar_com_dados_validos(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'Carlos Freelancer',
            'email'                 => 'carlos@example.com',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
            'role'                  => 'freelancer',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseHas('users', [
            'email' => 'carlos@example.com',
            'role'  => 'freelancer',
        ]);
    }

    #[Test]
    public function freelancer_recebe_codigo_de_afiliado_unico(): void
    {
        $this->post('/register', [
            'name'                  => 'Diana Freelancer',
            'email'                 => 'diana@example.com',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
            'role'                  => 'freelancer',
        ]);

        $user = User::where('email', 'diana@example.com')->first();

        $this->assertNotNull($user->affiliate_code);
        $this->assertEquals(8, strlen($user->affiliate_code));
    }

    #[Test]
    public function dois_utilizadores_nao_partilham_codigo_de_afiliado(): void
    {
        $this->post('/register/client', [
            'name'                  => 'User A',
            'email'                 => 'usera@example.com',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
            'role'                  => 'client',
        ]);
        $this->post('/register', [
            'name'                  => 'User B',
            'email'                 => 'userb@example.com',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
            'role'                  => 'freelancer',
        ]);

        $codeA = User::where('email', 'usera@example.com')->value('affiliate_code');
        $codeB = User::where('email', 'userb@example.com')->value('affiliate_code');

        $this->assertNotEquals($codeA, $codeB);
    }

    #[Test]
    public function role_nao_pode_ser_admin_por_registo(): void
    {
        $this->post('/register', [
            'name'                  => 'Hacker',
            'email'                 => 'hacker@example.com',
            'password'              => 'secret123',
            'password_confirmation' => 'secret123',
            'role'                  => 'admin',
        ]);

        // O utilizador não deve ser criado com role admin
        $user = User::where('email', 'hacker@example.com')->first();
        if ($user) {
            $this->assertNotEquals('admin', $user->role);
        } else {
            // Preferível: registo rejeitado por validação
            $this->assertTrue(true);
        }
    }
}
