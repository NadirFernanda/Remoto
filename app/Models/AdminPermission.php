<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminPermission extends Model
{
    protected $fillable = ['user_id', 'module', 'access'];

    // All platform modules available for permission assignment
    public const MODULES = [
        'dashboard'   => 'Dashboard',
        'users'       => 'Gestão de Utilizadores',
        'financial'   => 'Financeiro',
        'commissions' => 'Comissões',
        'payouts'     => 'Saques',
        'categories'  => 'Categorias',
        'fees'        => 'Taxas',
        'disputes'    => 'Disputas',
        'refunds'     => 'Reembolsos',
        'audit'       => 'Auditoria / Logs',
        'social'      => 'Moderação Social',
        'loja'        => 'Loja / Infoprodutos',
        'suporte'     => 'Suporte / Notificações',
        'settings'    => 'Configurações do Sistema',
    ];

    // Role-based default permissions (master gets full access automatically)
    public const ROLE_DEFAULTS = [
        'financeiro' => [
            'dashboard'   => 'read',
            'financial'   => 'full',
            'commissions' => 'full',
            'payouts'     => 'full',
            'categories'  => 'write',
            'fees'        => 'full',
            'audit'       => 'read',
            'refunds'     => 'write',
        ],
        'gestor' => [
            'dashboard'   => 'read',
            'users'       => 'write',
            'disputes'    => 'full',
            'refunds'     => 'write',
            'audit'       => 'read',
            'social'      => 'write',
            'suporte'     => 'full',
            'loja'        => 'write',
        ],
        'suporte' => [
            'dashboard'   => 'read',
            'users'       => 'read',
            'disputes'    => 'write',
            'refunds'     => 'read',
            'social'      => 'read',
            'suporte'     => 'full',
        ],
        'analista' => [
            'dashboard'   => 'full',
            'audit'       => 'full',
            'financial'   => 'read',
            'commissions' => 'read',
            'payouts'     => 'read',
            'users'       => 'read',
        ],
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function canRead(): bool
    {
        return in_array($this->access, ['read', 'write', 'full'], true);
    }

    public function canWrite(): bool
    {
        return in_array($this->access, ['write', 'full'], true);
    }
}
