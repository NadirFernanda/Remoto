<?php

namespace Tests\Feature;

use App\Livewire\FreelancerSearch;
use App\Models\FreelancerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Os atalhos do mega-menu "Contratar" e "Encontrar trabalho" no cabeçalho
 * ligavam a filtros que nunca podiam devolver resultados: `skill=design`,
 * `skill=web` etc. não correspondem a nenhuma habilidade real gravada
 * (que usa nomes como "UI/UX Design", "Marketing Digital"), e o parâmetro
 * `location` nem sequer existia como propriedade vinculável no componente
 * — era sempre ignorado. Estes testes verificam a correcção: os mesmos
 * valores agora usados nos links do cabeçalho devolvem resultados reais.
 */
class HeaderQuickFiltersTest extends TestCase
{
    use RefreshDatabase;

    private function makeFreelancer(array $skills, ?string $location = null): User
    {
        $user = User::factory()->create(['role' => 'freelancer', 'location' => $location, 'kyc_status' => 'verified']);
        FreelancerProfile::create([
            'user_id' => $user->id,
            'skills'  => $skills,
        ]);

        return $user;
    }

    #[Test]
    public function atalho_dev_de_websites_encontra_freelancer_com_laravel(): void
    {
        $this->makeFreelancer(['Laravel', 'PHP']);
        $this->makeFreelancer(['Marketing Digital']); // não deve aparecer

        // Mesmo valor que o link "Dev. de Websites" do cabeçalho usa.
        Livewire::test(FreelancerSearch::class, ['skill' => 'WordPress,Laravel,PHP,Node.js,React,HTML,CSS'])
            ->assertViewHas('freelancers', fn ($freelancers) => $freelancers->total() === 1);
    }

    #[Test]
    public function atalho_designers_graficos_encontra_freelancer_com_figma(): void
    {
        $this->makeFreelancer(['Figma', 'UI/UX Design']);
        $this->makeFreelancer(['Flutter']); // não deve aparecer

        Livewire::test(FreelancerSearch::class, ['skill' => 'UI/UX Design,Figma,Adobe Photoshop,Adobe Illustrator'])
            ->assertViewHas('freelancers', fn ($freelancers) => $freelancers->total() === 1);
    }

    #[Test]
    public function propriedade_location_existe_e_esta_no_queryString(): void
    {
        // A propriedade $location não existia — o parâmetro ?location=X dos
        // links "Luanda"/"Benguela"/etc. do cabeçalho era silenciosamente
        // ignorado pelo Livewire (nenhuma propriedade pública para o receber).
        // O filtro em si usa ILIKE (só suportado em Postgres, não em SQLite),
        // por isso não é exercido aqui em runtime — verificado manualmente
        // contra a base de dados de produção. Este teste é uma rede de
        // segurança estrutural: falha se a propriedade for removida de novo.
        $component = new FreelancerSearch();

        $this->assertTrue(property_exists($component, 'location'));

        $queryString = (function () {
            return $this->queryString;
        })->call($component);
        $this->assertArrayHasKey('location', $queryString);
    }

    #[Test]
    public function pagina_de_projectos_sem_palavra_chave_continua_a_funcionar(): void
    {
        // O filtro "q" em si usa ILIKE (só suportado em Postgres, não em
        // SQLite) por isso não pode ser exercido com um valor preenchido
        // neste ambiente de testes — verificado manualmente contra a base
        // de dados de produção. Isto só confirma que a rota, agora com um
        // ramo condicional novo no controller, continua a funcionar no
        // caminho sem filtro.
        $response = $this->get(route('public.projects'));

        $response->assertOk();
    }
}
