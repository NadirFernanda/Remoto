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
