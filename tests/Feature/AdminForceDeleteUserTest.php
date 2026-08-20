<?php

namespace Tests\Feature;

use App\Livewire\Admin\Users;
use App\Models\Service;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * "Forçar eliminação" — ignora deliberadamente a rede de segurança de
 * saldo/projectos/assinaturas. Só para o Admin Master; ainda assim nunca
 * elimina a própria conta nem outro admin.
 */
class AdminForceDeleteUserTest extends TestCase
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
    public function forcar_elimina_utilizador_com_saldo(): void
    {
        $master     = $this->makeMasterAdmin();
        $freelancer = User::factory()->create(['role' => 'freelancer']);
        Wallet::create(['user_id' => $freelancer->id, 'saldo' => 31450, 'saldo_pendente' => 0, 'saque_minimo' => 1000, 'taxa_saque' => 0]);

        Livewire::actingAs($master)
            ->test(Users::class)
            ->call('deleteUser', $freelancer->id, true);

        $this->assertDatabaseMissing('users', ['id' => $freelancer->id]);
    }

    #[Test]
    public function forcar_ainda_recusa_eliminar_a_propria_conta(): void
    {
        $master = $this->makeMasterAdmin();

        Livewire::actingAs($master)
            ->test(Users::class)
            ->call('deleteUser', $master->id, true);

        $this->assertDatabaseHas('users', ['id' => $master->id]);
    }

    #[Test]
    public function forcar_ainda_recusa_eliminar_outro_admin(): void
    {
        $master     = $this->makeMasterAdmin();
        $otherAdmin = User::factory()->create(['role' => 'admin', 'admin_role' => 'suporte']);

        Livewire::actingAs($master)
            ->test(Users::class)
            ->call('deleteUser', $otherAdmin->id, true);

        $this->assertDatabaseHas('users', ['id' => $otherAdmin->id]);
    }

    #[Test]
    public function bulk_forcar_elimina_apesar_de_saldo_e_projectos(): void
    {
        $master = $this->makeMasterAdmin();

        $comSaldo = User::factory()->create(['role' => 'freelancer']);
        Wallet::create(['user_id' => $comSaldo->id, 'saldo' => 11000, 'saldo_pendente' => 0, 'saque_minimo' => 1000, 'taxa_saque' => 0]);

        $comProjecto = User::factory()->create(['role' => 'cliente']);
        Service::create([
            'cliente_id'    => $comProjecto->id,
            'titulo'        => 'Projecto',
            'briefing'      => 'x',
            'valor'         => 5000,
            'taxa'          => 500,
            'valor_liquido' => 4500,
            'status'        => 'published',
        ]);

        Livewire::actingAs($master)
            ->test(Users::class)
            ->set('selected', [$comSaldo->id, $comProjecto->id])
            ->call('bulkDeleteSelected', true);

        $this->assertDatabaseMissing('users', ['id' => $comSaldo->id]);
        $this->assertDatabaseMissing('users', ['id' => $comProjecto->id]);
    }

    #[Test]
    public function bulk_forcar_por_admin_nao_master_nao_elimina_ninguem(): void
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
                ->set('selected', [$testUser->id])
                ->call('bulkDeleteSelected', true);
        } catch (\Throwable $e) {
            // abort_if(403) esperado.
        }

        $this->assertDatabaseHas('users', ['id' => $testUser->id]);
    }
}
