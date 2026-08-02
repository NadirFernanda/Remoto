<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Infoproduto extends Model
{
    protected $fillable = [
        'freelancer_id',
        'titulo',
        'descricao',
        'tipo',
        'preco',
        'capa_path',
        'arquivo_path',
        'slug',
        'status',
        'vendas_count',
    ];

    protected $casts = [
        'preco' => 'float',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model): void {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->titulo) . '-' . Str::random(6);
            }
        });
    }

    // ─── Relationships ──────────────────────────────────────────────

    public function freelancer()
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    public function patrocinios()
    {
        return $this->hasMany(InfoprodutoPatrocinio::class);
    }

    public function compras()
    {
        return $this->hasMany(InfoprodutoCompra::class);
    }

    // ─── Helpers ────────────────────────────────────────────────────

    public function patrocinioAtivo(): ?InfoprodutoPatrocinio
    {
        return $this->patrocinios()
            ->where('status', 'ativo')
            ->where('data_inicio', '<=', Carbon::today())
            ->where('data_fim', '>=', Carbon::today())
            ->first();
    }

    public function isPatrocinado(): bool
    {
        return $this->patrocinioAtivo() !== null;
    }

    public function jaCompradoPor(int $userId): bool
    {
        return $this->compras()->where('comprador_id', $userId)->exists();
    }

    public function tipoLabel(): string
    {
        return match ($this->tipo) {
            'ebook'            => 'E-book',
            'audio'            => 'Áudio',
            'literatura_digital' => 'Literatura Digital',
            default            => 'Outro',
        };
    }

    /** Cor de destaque por tipo, para badges/ícones/acentos visuais na Loja. */
    public function tipoColor(): string
    {
        return match ($this->tipo) {
            'ebook'              => '#3b82f6',
            'audio'              => '#a855f7',
            'literatura_digital' => '#f59e0b',
            default              => '#64748b',
        };
    }

    /** Path SVG (atributo "d") do ícone representativo do tipo, para placeholders de capa. */
    public function tipoIcon(): string
    {
        return match ($this->tipo) {
            'ebook'              => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253',
            'audio'              => 'M9 19V6l12-2v13M9 19a3 3 0 11-6 0 3 3 0 016 0zm12-2a3 3 0 11-6 0 3 3 0 016 0z',
            'literatura_digital' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            default              => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'rascunho'    => 'Rascunho',
            'em_moderacao' => 'Em Moderação',
            'ativo'        => 'Ativo',
            'inativo'      => 'Inativo',
            default        => ucfirst($this->status),
        };
    }
}
