<?php

namespace Tests\Feature;

use App\Livewire\Loja\PurchaseCheckout;
use App\Livewire\Social\SubscriptionCheckout;
use App\Models\CreatorProfile;
use App\Models\Infoproduto;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Só quem ganha dinheiro na plataforma (freelancer, criador) tem saldo em
 * carteira — o cliente só gasta, nunca gera saldo. A opção "Saldo" nos
 * checkouts de compra de infoproduto e assinatura de criador não pode
 * aparecer nem ser utilizável quando o utilizador está em modo cliente.
 */
class ClientCannotPayWithWalletTest extends TestCase
{
    use RefreshDatabase;

    private function makeInfoproduto(): Infoproduto
    {
        $freelancer = User::factory()->create(['role' => 'freelancer']);

        return Infoproduto::create([
            'freelancer_id' => $freelancer->id,
            'titulo'        => 'Produto de Teste',
            'descricao'     => 'x',
            'tipo'          => 'ebook',
            'preco'         => 5000,
            'slug'          => 'produto-de-teste',
            'status'        => 'ativo',
        ]);
    }

    #[Test]
    public function cliente_nao_ve_opcao_de_pagar_com_saldo_ao_comprar_infoproduto(): void
    {
        $produto = $this->makeInfoproduto();
        $client  = User::factory()->create(['role' => 'cliente']);

        Livewire::actingAs($client)
            ->test(PurchaseCheckout::class, ['produto' => $produto])
            ->assertSet('payment_method', 'express')
            ->assertDontSee('Saldo');
    }

    #[Test]
    public function cliente_nao_consegue_pagar_infoproduto_com_saldo_mesmo_forcando_o_metodo(): void
    {
        $produto = $this->makeInfoproduto();
        $client  = User::factory()->create(['role' => 'cliente']);
        Wallet::create(['user_id' => $client->id, 'saldo' => 999999, 'saldo_pendente' => 0, 'saque_minimo' => 1000, 'taxa_saque' => 0]);

        Livewire::actingAs($client)
            ->test(PurchaseCheckout::class, ['produto' => $produto])
            ->set('payment_method', 'wallet') // simula bypass da UI
            ->call('chargeWallet')
            ->assertSet('error', 'Pagamento com saldo de carteira não está disponível no modo cliente.');

        $this->assertDatabaseMissing('infoproduto_compras', ['infoproduto_id' => $produto->id, 'comprador_id' => $client->id]);
    }

    #[Test]
    public function freelancer_continua_a_ver_e_a_poder_usar_a_opcao_de_saldo_ao_comprar(): void
    {
        $produto    = $this->makeInfoproduto();
        $freelancer = User::factory()->create(['role' => 'freelancer']);

        Livewire::actingAs($freelancer)
            ->test(PurchaseCheckout::class, ['produto' => $produto])
            ->assertSet('payment_method', 'wallet')
            ->assertSee('Saldo');
    }

    #[Test]
    public function freelancer_dono_do_produto_e_redireccionado_com_mensagem_clara_em_vez_de_403(): void
    {
        $freelancer = User::factory()->create(['role' => 'freelancer']);
        $produto = Infoproduto::create([
            'freelancer_id' => $freelancer->id,
            'titulo'        => 'Produto Próprio',
            'descricao'     => 'x',
            'tipo'          => 'ebook',
            'preco'         => 5000,
            'slug'          => 'produto-proprio',
            'status'        => 'ativo',
        ]);

        Livewire::actingAs($freelancer)
            ->test(PurchaseCheckout::class, ['produto' => $produto])
            ->assertRedirect(route('loja.show', $produto->slug));

        $this->assertSame('Não pode comprar o seu próprio produto.', session('error_loja'));
    }

    #[Test]
    public function cliente_nao_ve_opcao_de_pagar_com_saldo_ao_assinar_criador(): void
    {
        $creator = User::factory()->create(['role' => 'creator', 'has_creator_profile' => true]);
        CreatorProfile::create(['user_id' => $creator->id, 'subscription_price' => 3000]);
        $client = User::factory()->create(['role' => 'cliente']);

        Livewire::actingAs($client)
            ->test(SubscriptionCheckout::class, ['user' => $creator])
            ->assertSet('payment_method', 'express')
            ->assertDontSee('Saldo');
    }
}
