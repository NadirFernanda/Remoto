<?php

namespace Tests\Feature;

use App\Livewire\Admin\Users;
use App\Models\CreatorSubscription;
use App\Models\Service;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Admin > Utilizadores > Eliminar — pensado para limpar contas de teste,
 * nunca para apagar utilizadores com actividade real na plataforma. Só o
 * Admin Master pode usar, e só funciona em contas sem saldo, projectos ou
 * assinaturas associadas.
 */
class AdminDeleteUserTest extends TestCase
{
    use RefreshDatabase;

    private function makeMasterAdmin(): User
    {
        $admin = User::factory()->create([
            'role'                    => 'admin',
            'admin_role'              => null,
            'two_factor_confirmed_at' => now(),
        ]);
        session(['2fa_passed_at' => now()->timestamp]);

        return $admin;
    }

    #[Test]
    public function master_elimina_conta_de_teste_sem_actividade(): void
    {
        $master = $this->makeMasterAdmin();
        $testUser = User::factory()->create(['role' => 'cliente']);

        Livewire::actingAs($master)
            ->test(Users::class)
            ->call('deleteUser', $testUser->id)
            ->assertSet('search', ''); // componente continua utilizável (não rebentou)

        $this->assertDatabaseMissing('users', ['id' => $testUser->id]);
    }

    #[Test]
    public function admin_sub_role_nao_pode_eliminar(): void
    {
        $admin = User::factory()->create([
            'role'                    => 'admin',
            'admin_role'              => 'suporte',
            'two_factor_confirmed_at' => now(),
        ]);
        session(['2fa_passed_at' => now()->timestamp]);
        $testUser = User::factory()->create(['role' => 'cliente']);

        try {
            Livewire::actingAs($admin)
                ->test(Users::class)
                ->call('deleteUser', $testUser->id);
        } catch (\Throwable $e) {
            // abort_if(403) esperado — o essencial é que o utilizador sobreviva.
        }

        $this->assertDatabaseHas('users', ['id' => $testUser->id]);
    }

    #[Test]
    public function master_nao_pode_eliminar_a_propria_conta(): void
    {
        $master = $this->makeMasterAdmin();

        Livewire::actingAs($master)
            ->test(Users::class)
            ->call('deleteUser', $master->id);

        $this->assertDatabaseHas('users', ['id' => $master->id]);
    }

    #[Test]
    public function master_nao_pode_eliminar_outro_admin(): void
    {
        $master = $this->makeMasterAdmin();
        $otherAdmin = User::factory()->create(['role' => 'admin', 'admin_role' => 'suporte']);

        Livewire::actingAs($master)
            ->test(Users::class)
            ->call('deleteUser', $otherAdmin->id);

        $this->assertDatabaseHas('users', ['id' => $otherAdmin->id]);
    }

    #[Test]
    public function nao_elimina_utilizador_com_saldo_em_carteira(): void
    {
        $master = $this->makeMasterAdmin();
        $freelancer = User::factory()->create(['role' => 'freelancer']);
        Wallet::create(['user_id' => $freelancer->id, 'saldo' => 5000, 'saldo_pendente' => 0, 'saque_minimo' => 1000, 'taxa_saque' => 0]);

        Livewire::actingAs($master)
            ->test(Users::class)
            ->call('deleteUser', $freelancer->id);

        $this->assertDatabaseHas('users', ['id' => $freelancer->id]);
    }

    #[Test]
    public function nao_elimina_utilizador_com_projecto_associado(): void
    {
        $master = $this->makeMasterAdmin();
        $client = User::factory()->create(['role' => 'cliente']);
        Service::create([
            'cliente_id'    => $client->id,
            'titulo'        => 'Projecto Teste',
            'briefing'      => 'x',
            'valor'         => 5000,
            'taxa'          => 500,
            'valor_liquido' => 4500,
            'status'        => 'published',
        ]);

        Livewire::actingAs($master)
            ->test(Users::class)
            ->call('deleteUser', $client->id);

        $this->assertDatabaseHas('users', ['id' => $client->id]);
    }

    #[Test]
    public function nao_elimina_utilizador_com_assinatura_associada(): void
    {
        $master = $this->makeMasterAdmin();
        $creator = User::factory()->create(['role' => 'creator']);
        $subscriber = User::factory()->create(['role' => 'cliente']);
        CreatorSubscription::create([
            'subscriber_id' => $subscriber->id,
            'creator_id'    => $creator->id,
            'amount'        => 3000,
            'platform_fee'  => 300,
            'net_amount'    => 2700,
            'status'        => 'active',
            'starts_at'     => now(),
            'expires_at'    => now()->addMonth(),
        ]);

        Livewire::actingAs($master)
            ->test(Users::class)
            ->call('deleteUser', $subscriber->id);

        $this->assertDatabaseHas('users', ['id' => $subscriber->id]);
    }
}
