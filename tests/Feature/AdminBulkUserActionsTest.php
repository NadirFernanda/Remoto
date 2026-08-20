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
 * Selecção múltipla na tabela Admin > Utilizadores, para agir sobre várias
 * contas de uma vez (ex.: limpar várias contas de teste), reutilizando as
 * mesmas regras de segurança da eliminação individual.
 */
class AdminBulkUserActionsTest extends TestCase
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
    public function seleccionar_tudo_na_pagina_marca_todos_excepto_a_propria_conta(): void
    {
        $master = $this->makeMasterAdmin();
        $u1 = User::factory()->create(['role' => 'cliente']);
        $u2 = User::factory()->create(['role' => 'freelancer']);

        $ids = Livewire::actingAs($master)
            ->test(Users::class)
            ->set('selectPage', true)
            ->get('selected');

        $this->assertContains($u1->id, $ids);
        $this->assertContains($u2->id, $ids);
        $this->assertNotContains($master->id, $ids);
    }

    #[Test]
    public function bulk_delete_elimina_contas_limpas_e_ignora_as_com_actividade(): void
    {
        $master = $this->makeMasterAdmin();

        $limpo = User::factory()->create(['role' => 'cliente']);

        $comSaldo = User::factory()->create(['role' => 'freelancer']);
        Wallet::create(['user_id' => $comSaldo->id, 'saldo' => 5000, 'saldo_pendente' => 0, 'saque_minimo' => 1000, 'taxa_saque' => 0]);

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
            ->set('selected', [$limpo->id, $comSaldo->id, $comProjecto->id])
            ->call('bulkDeleteSelected');

        $this->assertDatabaseMissing('users', ['id' => $limpo->id]);
        $this->assertDatabaseHas('users', ['id' => $comSaldo->id]);
        $this->assertDatabaseHas('users', ['id' => $comProjecto->id]);
    }

    #[Test]
    public function bulk_delete_por_admin_nao_master_nao_elimina_ninguem(): void
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
                ->call('bulkDeleteSelected');
        } catch (\Throwable $e) {
            // abort_if(403) esperado.
        }

        $this->assertDatabaseHas('users', ['id' => $testUser->id]);
    }

    #[Test]
    public function bulk_suspend_suspende_os_seleccionados_e_ignora_admins(): void
    {
        $master = $this->makeMasterAdmin();
        $u1 = User::factory()->create(['role' => 'cliente', 'is_suspended' => false]);
        $outroAdmin = User::factory()->create(['role' => 'admin', 'admin_role' => 'suporte', 'is_suspended' => false]);

        Livewire::actingAs($master)
            ->test(Users::class)
            ->set('selected', [$u1->id, $outroAdmin->id])
            ->call('bulkSuspendSelected');

        $this->assertDatabaseHas('users', ['id' => $u1->id, 'is_suspended' => true]);
        $this->assertDatabaseHas('users', ['id' => $outroAdmin->id, 'is_suspended' => false]);
    }

    #[Test]
    public function bulk_delete_limpa_a_seleccao_depois_de_correr(): void
    {
        $master = $this->makeMasterAdmin();
        $limpo = User::factory()->create(['role' => 'cliente']);

        Livewire::actingAs($master)
            ->test(Users::class)
            ->set('selected', [$limpo->id])
            ->call('bulkDeleteSelected')
            ->assertSet('selected', [])
            ->assertSet('selectPage', false);
    }
}
