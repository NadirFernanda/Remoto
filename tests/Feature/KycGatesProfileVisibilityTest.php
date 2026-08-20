<?php

namespace Tests\Feature;

use App\Livewire\Freelancer\ClientSearch;
use App\Livewire\Freelancer\Listing;
use App\Livewire\FreelancerSearch;
use App\Livewire\Social\CreatorProfile;
use App\Livewire\Social\CreatorSearch;
use App\Models\CreatorProfile as CreatorProfileModel;
use App\Models\FreelancerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * O perfil de um utilizador (freelancer, cliente ou criador) só fica
 * público/pesquisável depois de o KYC ser aprovado. Antes disso, não
 * aparece em nenhum directório/pesquisa, e a página de perfil directa
 * mostra um aviso em vez dos dados — excepto para o próprio dono da
 * conta (pré-visualização) ou um admin.
 */
class KycGatesProfileVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private function makeFreelancer(string $kyc): User
    {
        $user = User::factory()->create(['role' => 'freelancer', 'kyc_status' => $kyc]);
        FreelancerProfile::create(['user_id' => $user->id, 'headline' => 'Dev']);

        return $user;
    }

    #[Test]
    public function directorio_de_freelancers_esconde_nao_verificados(): void
    {
        $verificado    = $this->makeFreelancer('verified');
        $naoVerificado = $this->makeFreelancer('pending');

        Livewire::test(Listing::class)
            ->assertSee($verificado->name)
            ->assertDontSee($naoVerificado->name);
    }

    #[Test]
    public function pesquisa_de_freelancers_esconde_nao_verificados(): void
    {
        $verificado    = $this->makeFreelancer('verified');
        $naoVerificado = $this->makeFreelancer('pending');

        Livewire::test(FreelancerSearch::class)
            ->assertSee($verificado->name)
            ->assertDontSee($naoVerificado->name);
    }

    #[Test]
    public function buscar_clientes_esconde_nao_verificados(): void
    {
        $freelancer    = User::factory()->create(['role' => 'freelancer']);
        $verificado    = User::factory()->create(['role' => 'cliente', 'kyc_status' => 'verified', 'name' => 'Cliente Verificado']);
        $naoVerificado = User::factory()->create(['role' => 'cliente', 'kyc_status' => 'pending', 'name' => 'Cliente Pendente']);

        Livewire::actingAs($freelancer)
            ->test(ClientSearch::class)
            ->assertSee('Cliente Verificado')
            ->assertDontSee('Cliente Pendente');
    }

    #[Test]
    public function buscar_criadores_esconde_nao_verificados(): void
    {
        $viewer = User::factory()->create(['role' => 'freelancer']);

        $verificado = User::factory()->create(['role' => 'creator', 'kyc_status' => 'verified', 'name' => 'Criador Verificado']);
        CreatorProfileModel::create(['user_id' => $verificado->id]);

        $naoVerificado = User::factory()->create(['role' => 'creator', 'kyc_status' => 'pending', 'name' => 'Criador Pendente']);
        CreatorProfileModel::create(['user_id' => $naoVerificado->id]);

        Livewire::actingAs($viewer)
            ->test(CreatorSearch::class)
            ->assertSee('Criador Verificado')
            ->assertDontSee('Criador Pendente');
    }

    #[Test]
    public function perfil_de_freelancer_nao_verificado_mostra_aviso_a_visitante(): void
    {
        $freelancer = $this->makeFreelancer('pending');

        $response = $this->get(route('freelancer.show', $freelancer));

        $response->assertOk();
        $response->assertSee('Perfil ainda não disponível');
        $response->assertDontSee($freelancer->name);
    }

    #[Test]
    public function dono_ve_o_proprio_perfil_mesmo_sem_kyc(): void
    {
        $freelancer = $this->makeFreelancer('pending');

        $response = $this->actingAs($freelancer)->get(route('freelancer.show', $freelancer));

        $response->assertOk();
        $response->assertSee($freelancer->name);
        $response->assertDontSee('Perfil ainda não disponível');
    }

    #[Test]
    public function admin_ve_perfil_de_freelancer_nao_verificado(): void
    {
        $freelancer = $this->makeFreelancer('pending');
        $admin      = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('freelancer.show', $freelancer));

        $response->assertOk();
        $response->assertSee($freelancer->name);
    }

    #[Test]
    public function perfil_de_cliente_nao_verificado_mostra_aviso(): void
    {
        $client = User::factory()->create(['role' => 'cliente', 'kyc_status' => 'pending']);

        $response = $this->get(route('client.public', $client));

        $response->assertOk();
        $response->assertSee('Perfil ainda não disponível');
        $response->assertDontSee($client->name);
    }

    #[Test]
    public function perfil_de_criador_nao_verificado_redirecciona_outros_utilizadores(): void
    {
        $creator = User::factory()->create(['role' => 'creator', 'kyc_status' => 'pending']);
        CreatorProfileModel::create(['user_id' => $creator->id]);
        $viewer = User::factory()->create(['role' => 'cliente']);

        Livewire::actingAs($viewer)
            ->test(CreatorProfile::class, ['user' => $creator])
            ->assertRedirect(route('social.creators'));
    }
}
