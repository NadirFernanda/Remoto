<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Os perfis públicos de cliente (/clientes/{user}) e freelancer
 * (/freelancers/{user}) são acessíveis a visitantes não autenticados
 * (para partilha/SEO), por isso usam layouts.main. Mas quando visitados
 * por um utilizador já autenticado, devem aparecer dentro da shell do
 * dashboard (com a barra lateral) em vez de o "ejectar" para o layout
 * público — sem isso, o utilizador perde a navegação a meio da sessão.
 *
 * Os utilizadores cujo perfil é visitado nestes testes têm sempre
 * kyc_status 'verified' — um perfil não verificado mostra um aviso em
 * vez do conteúdo (ver KycGatesProfileVisibilityTest), o que não é o
 * que estes testes de layout/avatar/duplicação querem exercitar.
 */
class PublicProfileDashboardShellTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function visitante_ve_perfil_de_cliente_sem_barra_lateral_mas_com_botao_voltar(): void
    {
        $client = User::factory()->create(['role' => 'cliente', 'kyc_status' => 'verified']);

        $response = $this->get(route('client.public', $client));

        $response->assertOk();
        $response->assertDontSee('dash-sidebar', false);
        $response->assertSee('history.back()', false);
    }

    #[Test]
    public function utilizador_autenticado_ve_perfil_de_cliente_com_barra_lateral(): void
    {
        $viewer = User::factory()->create(['role' => 'freelancer']);
        $client = User::factory()->create(['role' => 'cliente', 'kyc_status' => 'verified']);

        $response = $this->actingAs($viewer)->get(route('client.public', $client));

        $response->assertOk();
        $response->assertSee('dash-sidebar', false);
        $response->assertSee($client->name);
    }

    #[Test]
    public function avatar_do_perfil_de_cliente_e_circular(): void
    {
        $client = User::factory()->create(['role' => 'cliente', 'kyc_status' => 'verified']);

        $response = $this->get(route('client.public', $client));

        $response->assertOk();
        $response->assertSee('w-20 h-20 sm:w-24 sm:h-24 rounded-full', false);
    }

    #[Test]
    public function visitante_ve_perfil_de_freelancer_sem_barra_lateral(): void
    {
        $freelancer = User::factory()->create(['role' => 'freelancer', 'kyc_status' => 'verified']);

        $response = $this->get(route('freelancer.show', $freelancer));

        $response->assertOk();
        $response->assertDontSee('dash-sidebar', false);
    }

    #[Test]
    public function utilizador_autenticado_ve_perfil_de_freelancer_com_barra_lateral(): void
    {
        $viewer     = User::factory()->create(['role' => 'cliente']);
        $freelancer = User::factory()->create(['role' => 'freelancer', 'kyc_status' => 'verified']);

        $response = $this->actingAs($viewer)->get(route('freelancer.show', $freelancer));

        $response->assertOk();
        $response->assertSee('dash-sidebar', false);
        $response->assertSee($freelancer->name);
    }

    #[Test]
    public function avatar_do_perfil_de_freelancer_e_circular(): void
    {
        $freelancer = User::factory()->create(['role' => 'freelancer', 'kyc_status' => 'verified']);

        $response = $this->get(route('freelancer.show', $freelancer));

        $response->assertOk();
        $response->assertSee('w-20 h-20 sm:w-24 sm:h-24 rounded-full', false);
    }

    /**
     * Regressão: @extends(...) dentro de @if/@else no MESMO ficheiro faz o
     * Blade renderizar os dois layouts em sequência (cabeçalho/rodapé
     * duplicados), porque a directiva @extends é localizada durante a
     * compilação, não em tempo de execução — não importa que só um dos
     * ramos corra de facto. Corrigido para @extends(condição ? 'a' : 'b')
     * — uma única directiva, uma única árvore de layout.
     */
    #[Test]
    public function perfil_de_cliente_nao_duplica_cabecalho_ou_rodape(): void
    {
        $client = User::factory()->create(['role' => 'cliente', 'kyc_status' => 'verified']);

        $guestHtml = $this->get(route('client.public', $client))->assertOk()->getContent();
        $this->assertSame(1, substr_count($guestHtml, 'class="site-header'));
        $this->assertSame(1, substr_count($guestHtml, 'class="hp-footer"'));

        $viewer = User::factory()->create(['role' => 'freelancer']);
        $authHtml = $this->actingAs($viewer)->get(route('client.public', $client))->assertOk()->getContent();
        $this->assertSame(1, substr_count($authHtml, 'class="site-header'));
        $this->assertSame(1, substr_count($authHtml, 'class="hp-footer"'));
    }

    #[Test]
    public function perfil_de_freelancer_nao_duplica_cabecalho_ou_rodape(): void
    {
        $freelancer = User::factory()->create(['role' => 'freelancer', 'kyc_status' => 'verified']);

        $guestHtml = $this->get(route('freelancer.show', $freelancer))->assertOk()->getContent();
        $this->assertSame(1, substr_count($guestHtml, 'class="site-header'));
        $this->assertSame(1, substr_count($guestHtml, 'class="hp-footer"'));

        $viewer = User::factory()->create(['role' => 'cliente']);
        $authHtml = $this->actingAs($viewer)->get(route('freelancer.show', $freelancer))->assertOk()->getContent();
        $this->assertSame(1, substr_count($authHtml, 'class="site-header'));
        $this->assertSame(1, substr_count($authHtml, 'class="hp-footer"'));
    }
}
