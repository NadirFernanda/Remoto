<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\Models\Service;
use App\Models\Infoproduto;
use App\Models\SocialPost;
use App\Models\User;
use Carbon\Carbon;

class CacheWarm extends Command
{
    protected $signature   = 'cache:warm';
    protected $description = 'Pré-aquece o cache de aplicação após deploy (cold start prevention)';

    public function handle(): int
    {
        $this->info('🔥 A aquecer cache...');

        // ── 1. Projetos públicos (primeiras 3 páginas) ────────────────────────
        $this->task('Projetos públicos (pág. 1-3)', function () {
            for ($page = 1; $page <= 3; $page++) {
                $key = 'public_projects:' . md5(serialize(['page' => $page]));
                Cache::remember($key, 180, fn () =>
                    Service::where('status', 'published')
                        ->orderByDesc('created_at')
                        ->paginate(12, ['*'], 'page', $page)
                );
            }
        });

        // ── 2. Loja Vitrine (primeira página, ordenação padrão) ───────────────
        $this->task('Loja Vitrine (pág. 1)', function () {
            $today = Carbon::today()->toDateString();
            $key   = 'loja_vitrine:' . md5('||recente|p1');
            Cache::remember($key, 120, fn () =>
                Infoproduto::where('status', 'ativo')
                    ->with(['freelancer:id,name,profile_photo'])
                    ->withExists([
                        'patrocinios as patrocinado' => fn ($q) => $q
                            ->where('status', 'ativo')
                            ->where('data_inicio', '<=', $today)
                            ->where('data_fim', '>=', $today),
                    ])
                    ->orderByRaw(
                        "EXISTS (SELECT 1 FROM infoproduto_patrocinios ip
                                 WHERE ip.infoproduto_id = infoprodutos.id
                                   AND ip.status = 'ativo'
                                   AND ip.data_inicio <= ?
                                   AND ip.data_fim >= ?) DESC",
                        [$today, $today]
                    )
                    ->orderByDesc('created_at')
                    ->paginate(12)
            );
        });

        // ── 3. Feed social público (primeiras 2 páginas) ─────────────────────
        $this->task('Feed social público (pág. 1-2)', function () {
            for ($page = 1; $page <= 2; $page++) {
                Cache::remember("social_guest_feed_p{$page}", 120, fn () =>
                    SocialPost::with([
                        'user.freelancerProfile',
                        'media',
                        'likes',
                        'comments.user',
                    ])->active()
                     ->where('visibility', 'public')
                     ->latest()
                     ->paginate(10, ['*'], 'page', $page)
                );
            }
        });

        // ── 4. Rating médio dos top 20 freelancers mais activos ──────────────
        $this->task('Ratings dos top freelancers', function () {
            User::where('role', 'freelancer')
                ->whereHas('freelancerProfile')
                ->orderByDesc('updated_at')
                ->limit(20)
                ->get(['id'])
                ->each(function ($user) {
                    Cache::remember("user_avg_rating:{$user->id}", 600, fn () =>
                        (float) round(
                            \App\Models\Review::where('target_id', $user->id)->avg('rating') ?? 0,
                            1
                        )
                    );
                });
        });

        $this->info('✅ Cache aquecido com sucesso!');
        return self::SUCCESS;
    }

    /** Executa uma tarefa com output de estado. */
    private function task(string $label, callable $fn): void
    {
        $this->output->write("  → {$label}... ");
        try {
            $fn();
            $this->line('<fg=green>OK</>');
        } catch (\Throwable $e) {
            $this->line('<fg=yellow>SKIP (' . $e->getMessage() . ')</>');
        }
    }
}
