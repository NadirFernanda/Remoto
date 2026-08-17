<?php

namespace Tests\Feature;

use App\Livewire\Auth\RegisterWizard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Testes de integração para o assistente de registo em 2 passos
 * (App\Livewire\Auth\RegisterWizard): dados da conta + verificação de
 * identidade (KYC), obrigatória para concluir o registo.
 *
 * Cobre registo de cliente e freelancer, validação de inputs,
 * unicidade de e-mail, assignação de role, código de afiliado, e
 * criação do KycSubmission.
 */
class RegisterWizardTest extends TestCase
{
    use RefreshDatabase;

    private function completeStep2($wizard, array $overrides = [])
    {
        return $wizard
            ->assertSet('step', 2)
            ->set('documentType', $overrides['documentType'] ?? 'bi')
            ->set('documentNumber', $overrides['documentNumber'] ?? '00'.uniqid().'LA000')
            ->set('documentFront', UploadedFile::fake()->image('frente.jpg'))
            ->set('documentBack', UploadedFile::fake()->image('verso.jpg'))
            ->call('submit');
    }

    // ── Registo de cliente ────────────────────────────────────────────────────

    #[Test]
    public function cliente_pode_registar_com_dados_validos(): void
    {
        Storage::fake('private');

        $wizard = Livewire::test(RegisterWizard::class)
            ->set('role', 'cliente')
            ->set('name', 'Ana Cliente')
            ->set('email', 'ana@example.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->call('nextStep');

        $this->completeStep2($wizard)->assertRedirect('/cliente/dashboard');

        $this->assertDatabaseHas('users', [
            'email'      => 'ana@example.com',
            'role'       => 'cliente',
            'kyc_status' => 'pending',
        ]);

        $user = User::where('email', 'ana@example.com')->first();
        $this->assertDatabaseHas('kyc_submissions', [
            'user_id' => $user->id,
            'status'  => 'pending',
        ]);
    }

    #[Test]
    public function cliente_recebe_codigo_de_afiliado_unico(): void
    {
        Storage::fake('private');

        $wizard = Livewire::test(RegisterWizard::class)
            ->set('role', 'cliente')
            ->set('name', 'Bob Cliente')
            ->set('email', 'bob@example.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->call('nextStep');

        $this->completeStep2($wizard);

        $user = User::where('email', 'bob@example.com')->first();

        $this->assertNotNull($user->affiliate_code);
        $this->assertEquals(8, strlen($user->affiliate_code));
        $this->assertEquals(strtoupper($user->affiliate_code), $user->affiliate_code);
    }

    #[Test]
    public function cliente_nao_pode_registar_com_email_duplicado(): void
    {
        User::factory()->create(['email' => 'duplo@example.com']);

        Livewire::test(RegisterWizard::class)
            ->set('role', 'cliente')
            ->set('name', 'Duplo')
            ->set('email', 'duplo@example.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->call('nextStep')
            ->assertHasErrors('email')
            ->assertSet('step', 1);

        $this->assertEquals(1, User::where('email', 'duplo@example.com')->count());
    }

    #[Test]
    public function cliente_nao_pode_registar_sem_nome(): void
    {
        Livewire::test(RegisterWizard::class)
            ->set('role', 'cliente')
            ->set('name', '')
            ->set('email', 'semnome@example.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->call('nextStep')
            ->assertHasErrors('name')
            ->assertSet('step', 1);
    }

    #[Test]
    public function cliente_nao_pode_registar_com_senhas_diferentes(): void
    {
        Livewire::test(RegisterWizard::class)
            ->set('role', 'cliente')
            ->set('name', 'Teste')
            ->set('email', 'teste@example.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'different456')
            ->call('nextStep')
            ->assertHasErrors('password')
            ->assertSet('step', 1);
    }

    #[Test]
    public function cliente_nao_pode_registar_com_senha_curta(): void
    {
        Livewire::test(RegisterWizard::class)
            ->set('role', 'cliente')
            ->set('name', 'Short')
            ->set('email', 'short@example.com')
            ->set('password', '123')
            ->set('password_confirmation', '123')
            ->call('nextStep')
            ->assertHasErrors('password')
            ->assertSet('step', 1);
    }

    // ── Registo de freelancer ─────────────────────────────────────────────────

    #[Test]
    public function freelancer_pode_registar_com_dados_validos(): void
    {
        Storage::fake('private');

        $wizard = Livewire::test(RegisterWizard::class)
            ->set('role', 'freelancer')
            ->set('name', 'Carlos Freelancer')
            ->set('email', 'carlos@example.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->call('nextStep');

        $this->completeStep2($wizard)->assertRedirect('/freelancer/dashboard');

        $this->assertDatabaseHas('users', [
            'email'      => 'carlos@example.com',
            'role'       => 'freelancer',
            'kyc_status' => 'pending',
        ]);

        $user = User::where('email', 'carlos@example.com')->first();
        $this->assertDatabaseHas('creator_profiles', ['user_id' => $user->id]);
    }

    #[Test]
    public function freelancer_recebe_codigo_de_afiliado_unico(): void
    {
        Storage::fake('private');

        $wizard = Livewire::test(RegisterWizard::class)
            ->set('role', 'freelancer')
            ->set('name', 'Diana Freelancer')
            ->set('email', 'diana@example.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->call('nextStep');

        $this->completeStep2($wizard);

        $user = User::where('email', 'diana@example.com')->first();

        $this->assertNotNull($user->affiliate_code);
        $this->assertEquals(8, strlen($user->affiliate_code));
    }

    #[Test]
    public function dois_utilizadores_nao_partilham_codigo_de_afiliado(): void
    {
        Storage::fake('private');

        $wizardA = Livewire::test(RegisterWizard::class)
            ->set('role', 'cliente')
            ->set('name', 'User A')
            ->set('email', 'usera@example.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->call('nextStep');
        $this->completeStep2($wizardA);

        // O segundo registo tem de arrancar sem sessão — mount() redirecciona
        // logo quem já está autenticado (como o utilizador A, que o submit()
        // anterior acabou de autenticar).
        \Illuminate\Support\Facades\Auth::logout();
        $this->app['session']->flush();

        $wizardB = Livewire::test(RegisterWizard::class)
            ->set('role', 'freelancer')
            ->set('name', 'User B')
            ->set('email', 'userb@example.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->call('nextStep');
        $this->completeStep2($wizardB);

        $codeA = User::where('email', 'usera@example.com')->value('affiliate_code');
        $codeB = User::where('email', 'userb@example.com')->value('affiliate_code');

        $this->assertNotEquals($codeA, $codeB);
    }

    #[Test]
    public function role_nao_pode_ser_admin_por_registo(): void
    {
        Livewire::test(RegisterWizard::class)
            ->set('role', 'admin')
            ->set('name', 'Hacker')
            ->set('email', 'hacker@example.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->call('nextStep')
            ->assertHasErrors('role')
            ->assertSet('step', 1);

        $this->assertDatabaseMissing('users', ['email' => 'hacker@example.com']);
    }

    // ── KYC obrigatório ───────────────────────────────────────────────────────

    #[Test]
    public function nao_avanca_para_passo_2_sem_dados_validos(): void
    {
        Livewire::test(RegisterWizard::class)
            ->set('role', 'cliente')
            ->set('name', '')
            ->set('email', 'invalido')
            ->call('nextStep')
            ->assertSet('step', 1);
    }

    #[Test]
    public function nao_conclui_registo_sem_documentos(): void
    {
        $wizard = Livewire::test(RegisterWizard::class)
            ->set('role', 'cliente')
            ->set('name', 'Sem Docs')
            ->set('email', 'semdocs@example.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->call('nextStep')
            ->assertSet('step', 2);

        $wizard->call('submit')
            ->assertHasErrors(['documentFront', 'documentBack', 'documentNumber']);

        $this->assertDatabaseMissing('users', ['email' => 'semdocs@example.com']);
    }

    #[Test]
    public function nao_pode_registar_com_numero_de_documento_ja_usado(): void
    {
        Storage::fake('private');

        $wizardA = Livewire::test(RegisterWizard::class)
            ->set('role', 'cliente')
            ->set('name', 'Primeiro Dono')
            ->set('email', 'primeiro@example.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->call('nextStep');
        $this->completeStep2($wizardA, ['documentNumber' => '003456789LA042']);

        \Illuminate\Support\Facades\Auth::logout();
        $this->app['session']->flush();

        $wizardB = Livewire::test(RegisterWizard::class)
            ->set('role', 'freelancer')
            ->set('name', 'Segundo Tentando')
            ->set('email', 'segundo@example.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->call('nextStep');

        $wizardB
            ->set('documentType', 'bi')
            ->set('documentNumber', '003456789LA042')
            ->set('documentFront', UploadedFile::fake()->image('frente.jpg'))
            ->set('documentBack', UploadedFile::fake()->image('verso.jpg'))
            ->call('submit')
            ->assertHasErrors('documentNumber');

        $this->assertDatabaseMissing('users', ['email' => 'segundo@example.com']);
    }
}
