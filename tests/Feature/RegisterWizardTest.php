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

    private static int $documentCounter = 0;

    private function completeStep2($wizard, array $overrides = [])
    {
        return $wizard
            ->assertSet('step', 2)
            ->set('documentType', $overrides['documentType'] ?? 'bi')
            ->set('documentNumber', $overrides['documentNumber'] ?? $this->validBiNumber())
            ->set('documentFront', UploadedFile::fake()->image('frente.jpg'))
            ->set('documentBack', UploadedFile::fake()->image('verso.jpg'))
            ->call('submit');
    }

    /** Gera um número de BI único (cada chamada avança o contador) no formato oficial: 9 dígitos + 2 letras + 3 dígitos. */
    private function validBiNumber(): string
    {
        self::$documentCounter++;

        return sprintf('%09d', self::$documentCounter).'LA'.sprintf('%03d', self::$documentCounter % 1000);
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

    // ── Formato do número de BI ──────────────────────────────────────────────

    #[Test]
    public function bi_com_formato_invalido_e_rejeitado(): void
    {
        Storage::fake('private');

        $wizard = Livewire::test(RegisterWizard::class)
            ->set('role', 'cliente')
            ->set('name', 'Formato Errado')
            ->set('email', 'formatoerrado@example.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->call('nextStep');

        $wizard
            ->set('documentType', 'bi')
            ->set('documentNumber', '12345') // não tem o formato 9 dígitos + 2 letras + 3 dígitos
            ->set('documentFront', UploadedFile::fake()->image('frente.jpg'))
            ->set('documentBack', UploadedFile::fake()->image('verso.jpg'))
            ->call('submit')
            ->assertHasErrors('documentNumber');

        $this->assertDatabaseMissing('users', ['email' => 'formatoerrado@example.com']);
    }

    #[Test]
    public function bi_com_formato_valido_em_minusculas_e_normalizado_e_aceite(): void
    {
        Storage::fake('private');

        $wizard = Livewire::test(RegisterWizard::class)
            ->set('role', 'cliente')
            ->set('name', 'Minusculas')
            ->set('email', 'minusculas@example.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->call('nextStep');

        $wizard
            ->set('documentType', 'bi')
            ->set('documentNumber', '003456789la042') // letras em minúsculas
            ->set('documentFront', UploadedFile::fake()->image('frente.jpg'))
            ->set('documentBack', UploadedFile::fake()->image('verso.jpg'))
            ->call('submit')
            ->assertHasNoErrors('documentNumber')
            ->assertRedirect('/cliente/dashboard');

        $this->assertDatabaseHas('kyc_submissions', ['document_number' => '003456789LA042']);
    }

    #[Test]
    public function passaporte_nao_exige_o_formato_do_bi(): void
    {
        Storage::fake('private');

        $wizard = Livewire::test(RegisterWizard::class)
            ->set('role', 'cliente')
            ->set('name', 'Estrangeiro')
            ->set('email', 'estrangeiro@example.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->call('nextStep');

        $wizard
            ->set('documentType', 'passport')
            ->set('documentNumber', 'N1234567') // formato de passaporte, não o do BI
            ->set('documentFront', UploadedFile::fake()->image('frente.jpg'))
            ->set('documentBack', UploadedFile::fake()->image('verso.jpg'))
            ->call('submit')
            ->assertHasNoErrors('documentNumber')
            ->assertRedirect('/cliente/dashboard');
    }

    // ── Reforços anti-fraude ─────────────────────────────────────────────────

    #[Test]
    public function mesma_foto_de_documento_nao_pode_ser_reusada_com_outro_numero(): void
    {
        Storage::fake('private');
        $fotoPartilhada = UploadedFile::fake()->image('frente.jpg');

        $wizardA = Livewire::test(RegisterWizard::class)
            ->set('role', 'cliente')
            ->set('name', 'Dono da Foto')
            ->set('email', 'donodafoto@example.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->call('nextStep');

        $wizardA
            ->set('documentType', 'bi')
            ->set('documentNumber', $this->validBiNumber())
            ->set('documentFront', $fotoPartilhada)
            ->set('documentBack', UploadedFile::fake()->image('verso.jpg'))
            ->call('submit')
            ->assertRedirect('/cliente/dashboard');

        \Illuminate\Support\Facades\Auth::logout();
        $this->app['session']->flush();

        $wizardB = Livewire::test(RegisterWizard::class)
            ->set('role', 'freelancer')
            ->set('name', 'Foto Reciclada')
            ->set('email', 'fotoreciclada@example.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->call('nextStep');

        $wizardB
            ->set('documentType', 'bi')
            ->set('documentNumber', $this->validBiNumber()) // número diferente, mesma foto
            ->set('documentFront', $fotoPartilhada)
            ->set('documentBack', UploadedFile::fake()->image('verso2.jpg'))
            ->call('submit')
            ->assertHasErrors('documentFront');

        $this->assertDatabaseMissing('users', ['email' => 'fotoreciclada@example.com']);
    }

    #[Test]
    public function demasiadas_tentativas_de_submissao_sao_bloqueadas(): void
    {
        Storage::fake('private');

        $wizard = Livewire::test(RegisterWizard::class)
            ->set('role', 'cliente')
            ->set('name', 'Muitas Tentativas')
            ->set('email', 'muitastentativas@example.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->call('nextStep');

        // 5 tentativas consomem o limite (falham por faltar o número de documento)
        for ($i = 0; $i < 5; $i++) {
            $wizard->call('submit');
        }

        // a 6ª tentativa é bloqueada pelo limite, independentemente dos dados
        $wizard->call('submit');

        $mensagens = $wizard->errors()->get('documentNumber');
        $this->assertTrue(
            collect($mensagens)->contains(fn ($m) => str_contains($m, 'Demasiadas tentativas')),
            'Esperava a mensagem de limite de tentativas entre os erros de documentNumber.'
        );
    }
}
