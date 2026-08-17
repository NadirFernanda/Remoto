<?php

namespace Tests\Feature;

use App\Models\FreelancerProfile;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Testes para o ficheiro de pagamento bancário exportado a partir da página
 * de Saques (admin) — cobre directamente o bug reportado onde o valor
 * exportado/mostrado não correspondia ao valor realmente solicitado pelo
 * freelancer (money_aoa() a converter Kz como se fossem BRL).
 */
class PayoutsBankExportTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        $admin = User::factory()->create([
            'role'                   => 'admin',
            'email_verified_at'      => now(),
            'status'                 => 'active',
            'two_factor_confirmed_at' => now(),
        ]);

        // Rotas de admin exigem 2FA confirmado + desafio passado na sessão
        // (middleware '2fa' — App\Http\Middleware\EnsureTwoFactorAuthenticated).
        session(['2fa_passed_at' => now()->timestamp]);

        return $admin;
    }

    private function makeFreelancerComSaque(float $valorSolicitado, bool $comConta = true): array
    {
        $freelancer = User::factory()->create([
            'role'              => 'freelancer',
            'name'              => 'BMK',
            'email_verified_at' => now(),
            'status'            => 'active',
            'kyc_status'        => 'verified',
        ]);

        FreelancerProfile::create(array_merge([
            'user_id'    => $freelancer->id,
            'kyc_status' => 'verified',
            'skills'     => [],
            'languages'  => [],
        ], $comConta ? [
            'bank_name'            => 'BAI',
            'bank_account_holder'  => 'BMK Silva',
            'bank_account_number'  => 'AO06000000000000000000000',
        ] : []));

        $wallet = Wallet::create([
            'user_id'        => $freelancer->id,
            'saldo'          => 780000,
            'saldo_pendente' => $valorSolicitado,
            'saque_minimo'   => 1000,
            'taxa_saque'     => 0,
        ]);

        $log = WalletLog::create([
            'user_id'   => $freelancer->id,
            'wallet_id' => $wallet->id,
            'valor'     => -$valorSolicitado,
            'tipo'      => 'saque_solicitado',
            'fonte'     => 'projetos',
            'descricao' => 'Saque de Projectos: Kz ' . number_format($valorSolicitado, 0, ',', '.') . ' — aguarda aprovação.',
        ]);

        return [$freelancer, $log];
    }

    #[Test]
    public function excel_exporta_o_valor_solicitado_e_nao_o_saldo_da_carteira(): void
    {
        // Regressão directa do bug: o saldo total em carteira (Kz 780.000) é
        // muito maior do que o valor realmente pedido no saque (Kz 50.000) —
        // o ficheiro tem de mostrar o valor do saque, nunca o saldo.
        [, $log] = $this->makeFreelancerComSaque(50000);

        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get(route('admin.payouts.bank-file.excel'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.ms-excel; charset=UTF-8');
        $response->assertSee('50.000,00', false);
        $response->assertSee('BAI', false);
        $response->assertSee('BMK Silva', false);
        $response->assertDontSee('780.000', false);
        $response->assertDontSee('2.075.000', false); // 50.000 × 41,5 (bug antigo do money_aoa)
    }

    #[Test]
    public function excel_assinala_freelancers_sem_conta_bancaria_registada(): void
    {
        $this->makeFreelancerComSaque(30000, comConta: false);

        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get(route('admin.payouts.bank-file.excel'));

        $response->assertStatus(200);
        $response->assertSee('SEM CONTA REGISTADA', false);
    }

    #[Test]
    public function csv_tambem_exporta_o_valor_correcto(): void
    {
        $this->makeFreelancerComSaque(75500);

        $admin = $this->makeAdmin();

        $response = $this->actingAs($admin)->get(route('admin.payouts.bank-file.csv'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('75.500,00', $response->streamedContent());
    }

    #[Test]
    public function nao_admin_nao_acede_ao_ficheiro_de_pagamento(): void
    {
        $freelancer = User::factory()->create(['role' => 'freelancer', 'email_verified_at' => now()]);

        $this->actingAs($freelancer)
            ->get(route('admin.payouts.bank-file.excel'))
            ->assertStatus(403);
    }
}
