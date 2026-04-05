<?php

namespace Tests\Feature;

use App\Models\Dispute;
use App\Models\DisputeMessage;
use App\Models\FreelancerProfile;
use App\Models\Review;
use App\Models\Service;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Testes para Review (bidirecional) e Dispute (mediação).
 */
class ReviewAndDisputeTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function makeUsers(): array
    {
        $client = User::factory()->create(['role' => 'cliente', 'status' => 'active']);
        $freelancer = User::factory()->create(['role' => 'freelancer', 'status' => 'active', 'kyc_status' => 'verified']);

        FreelancerProfile::create([
            'user_id'    => $freelancer->id,
            'kyc_status' => 'verified',
            'skills'     => [],
            'languages'  => [],
        ]);

        Wallet::create(['user_id' => $client->id,     'saldo' => 50000, 'saldo_pendente' => 50000, 'saque_minimo' => 1000, 'taxa_saque' => 0]);
        Wallet::create(['user_id' => $freelancer->id, 'saldo' => 0,     'saldo_pendente' => 0,     'saque_minimo' => 1000, 'taxa_saque' => 0]);

        return [$client, $freelancer];
    }

    private function makeCompletedService(User $client, User $freelancer, float $valor = 10000): Service
    {
        return Service::create([
            'cliente_id'    => $client->id,
            'freelancer_id' => $freelancer->id,
            'titulo'        => 'Serviço de Teste',
            'briefing'      => 'Briefing',
            'valor'         => $valor,
            'taxa'          => round($valor * 0.10, 2),
            'valor_liquido' => round($valor * 0.90, 2),
            'status'        => 'completed',
        ]);
    }

    // ── Review ────────────────────────────────────────────────────────────────

    #[Test]
    public function cliente_pode_avaliar_freelancer(): void
    {
        [$client, $freelancer] = $this->makeUsers();
        $service = $this->makeCompletedService($client, $freelancer);

        $review = Review::create([
            'author_id'  => $client->id,
            'target_id'  => $freelancer->id,
            'service_id' => $service->id,
            'rating'     => 5,
            'comment'    => 'Excelente trabalho!',
        ]);

        $this->assertDatabaseHas('reviews', [
            'author_id' => $client->id,
            'target_id' => $freelancer->id,
            'rating'    => 5,
        ]);
        $this->assertEquals($client->id, $review->author->id);
        $this->assertEquals($freelancer->id, $review->target->id);
    }

    #[Test]
    public function freelancer_pode_avaliar_cliente(): void
    {
        [$client, $freelancer] = $this->makeUsers();
        $service = $this->makeCompletedService($client, $freelancer);

        $review = Review::create([
            'author_id'  => $freelancer->id,
            'target_id'  => $client->id,
            'service_id' => $service->id,
            'rating'     => 4,
            'comment'    => 'Cliente colaborativo.',
        ]);

        $this->assertDatabaseHas('reviews', [
            'author_id' => $freelancer->id,
            'target_id' => $client->id,
            'rating'    => 4,
        ]);
    }

    #[Test]
    public function review_pertence_ao_servico_correto(): void
    {
        [$client, $freelancer] = $this->makeUsers();
        $service = $this->makeCompletedService($client, $freelancer);

        $review = Review::create([
            'author_id'  => $client->id,
            'target_id'  => $freelancer->id,
            'service_id' => $service->id,
            'rating'     => 3,
        ]);

        $this->assertEquals($service->id, $review->service->id);
    }

    #[Test]
    public function rating_deve_ser_entre_1_e_5(): void
    {
        [$client, $freelancer] = $this->makeUsers();
        $service = $this->makeCompletedService($client, $freelancer);

        foreach ([1, 2, 3, 4, 5] as $rating) {
            $review = Review::create([
                'author_id'  => $client->id,
                'target_id'  => $freelancer->id,
                'service_id' => $service->id,
                'rating'     => $rating,
            ]);
            $this->assertBetween($rating, 1, 5, (int) $review->rating);
            $review->delete(); // soft-delete para poder repetir o loop
        }
    }

    #[Test]
    public function review_soft_delete_nao_remove_registo(): void
    {
        [$client, $freelancer] = $this->makeUsers();
        $service = $this->makeCompletedService($client, $freelancer);

        $review = Review::create([
            'author_id'  => $client->id,
            'target_id'  => $freelancer->id,
            'service_id' => $service->id,
            'rating'     => 5,
        ]);

        $review->delete();

        $this->assertSoftDeleted('reviews', ['id' => $review->id]);
        $this->assertNull(Review::find($review->id));
        $this->assertNotNull(Review::withTrashed()->find($review->id));
    }

    // ── Dispute ───────────────────────────────────────────────────────────────

    #[Test]
    public function dispute_pode_ser_aberta_pelo_cliente(): void
    {
        [$client, $freelancer] = $this->makeUsers();
        $service = $this->makeCompletedService($client, $freelancer);

        $dispute = Dispute::create([
            'service_id'  => $service->id,
            'opened_by'   => $client->id,
            'reason'      => 'qualidade',
            'description' => 'O trabalho entregue não corresponde ao briefing.',
            'status'      => 'aberta',
        ]);

        $this->assertDatabaseHas('disputes', [
            'service_id' => $service->id,
            'status'     => 'aberta',
            'reason'     => 'qualidade',
        ]);
        $this->assertEquals($client->id, $dispute->opener->id);
    }

    #[Test]
    public function dispute_pode_ser_aberta_pelo_freelancer(): void
    {
        [$client, $freelancer] = $this->makeUsers();
        $service = $this->makeCompletedService($client, $freelancer);

        $dispute = Dispute::create([
            'service_id'  => $service->id,
            'opened_by'   => $freelancer->id,
            'reason'      => 'nao_pagamento',
            'description' => 'Ainda não recebi o pagamento.',
            'status'      => 'aberta',
        ]);

        $this->assertEquals($freelancer->id, $dispute->opener->id);
        $this->assertEquals('nao_pagamento', $dispute->reason);
    }

    #[Test]
    public function dispute_tem_razoes_validas(): void
    {
        $this->assertArrayHasKey('atraso',        Dispute::$reasons);
        $this->assertArrayHasKey('qualidade',     Dispute::$reasons);
        $this->assertArrayHasKey('nao_pagamento', Dispute::$reasons);
        $this->assertArrayHasKey('outro',         Dispute::$reasons);
    }

    #[Test]
    public function admin_pode_adicionar_nota_e_mudar_status(): void
    {
        [$client, $freelancer] = $this->makeUsers();
        $service = $this->makeCompletedService($client, $freelancer);

        $dispute = Dispute::create([
            'service_id'  => $service->id,
            'opened_by'   => $client->id,
            'reason'      => 'atraso',
            'description' => 'Entregou fora do prazo.',
            'status'      => 'aberta',
        ]);

        $dispute->update([
            'status'     => 'em_mediacao',
            'admin_note' => 'A analisar o caso.',
        ]);

        $this->assertDatabaseHas('disputes', [
            'id'         => $dispute->id,
            'status'     => 'em_mediacao',
            'admin_note' => 'A analisar o caso.',
        ]);
    }

    #[Test]
    public function dispute_pode_ter_mensagens(): void
    {
        [$client, $freelancer] = $this->makeUsers();
        $service = $this->makeCompletedService($client, $freelancer);

        $dispute = Dispute::create([
            'service_id'  => $service->id,
            'opened_by'   => $client->id,
            'reason'      => 'outro',
            'description' => 'Outro motivo.',
            'status'      => 'aberta',
        ]);

        DisputeMessage::create([
            'dispute_id' => $dispute->id,
            'user_id'    => $client->id,
            'message'    => 'Primeira mensagem da disputa.',
        ]);
        DisputeMessage::create([
            'dispute_id' => $dispute->id,
            'user_id'    => $freelancer->id,
            'message'    => 'Resposta do freelancer.',
        ]);

        $this->assertEquals(2, $dispute->messages()->count());
        $this->assertEquals('Primeira mensagem da disputa.', $dispute->messages->first()->message);
    }

    #[Test]
    public function dispute_soft_delete_nao_apaga_registo(): void
    {
        [$client, $freelancer] = $this->makeUsers();
        $service = $this->makeCompletedService($client, $freelancer);

        $dispute = Dispute::create([
            'service_id'  => $service->id,
            'opened_by'   => $client->id,
            'reason'      => 'outro',
            'description' => 'Teste.',
            'status'      => 'aberta',
        ]);

        $dispute->delete();

        $this->assertSoftDeleted('disputes', ['id' => $dispute->id]);
    }

    // ── Helpers internos ──────────────────────────────────────────────────────

    private function assertBetween(int $expected, int $min, int $max, int $actual): void
    {
        $this->assertGreaterThanOrEqual($min, $actual);
        $this->assertLessThanOrEqual($max, $actual);
    }
}
