<div class="light-page min-h-screen pt-8 pb-16">
<div class="max-w-7xl mx-auto px-4">

    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Gestão da Loja</h1>
            <p class="text-sm text-gray-500 mt-1">Moderação, aprovação e estatísticas de infoprodutos</p>
        </div>
        <a href="{{ route('loja.index') }}" target="_blank"
            class="inline-flex items-center gap-1.5 text-sm text-[#00baff] hover:underline">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            Ver Loja pública
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <div class="bg-white rounded-2xl border shadow-sm p-4 text-center">
            <div class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Total de produtos</div>
        </div>
        <div class="bg-white rounded-2xl border shadow-sm p-4 text-center">
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['em_moderacao'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Em moderação</div>
        </div>
        <div class="bg-white rounded-2xl border shadow-sm p-4 text-center">
            <div class="text-2xl font-bold text-green-600">{{ $stats['ativos'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Ativos</div>
        </div>
        <div class="bg-white rounded-2xl border shadow-sm p-4 text-center">
            <div class="text-2xl font-bold text-[#00baff]">{{ $stats['vendas_hoje'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Vendas hoje</div>
        </div>
        <div class="bg-white rounded-2xl border shadow-sm p-4 text-center">
            <div class="text-2xl font-bold text-purple-600">Kz {{ number_format($stats['receita_total'], 0, ',', '.') }}</div>
            <div class="text-xs text-gray-500 mt-1">Receita total (comissões)</div>
        </div>
        <div class="bg-white rounded-2xl border shadow-sm p-4 text-center">
            <div class="text-2xl font-bold text-amber-600">{{ $stats['patrocinios_ativos'] }}</div>
            <div class="text-xs text-gray-500 mt-1">Patrocínios ativos</div>
        </div>
    </div>

    {{-- Feedback --}}
    @if($feedback)
    <div class="mb-6 px-4 py-3 rounded-xl bg-green-100 text-green-700 text-sm font-medium">{{ $feedback }}</div>
    @endif

    {{-- Filters --}}
    <div class="flex flex-wrap gap-3 mb-6">
        <input wire:model.live.debounce.400ms="busca" type="text" placeholder="Buscar por título ou freelancer..."
            class="border rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-[#00baff] w-72">
        <select wire:model.live="filtroStatus" class="border rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-[#00baff]">
            <option value="">Todos os status</option>
            <option value="em_moderacao">Em Moderação</option>
            <option value="ativo">Ativo</option>
            <option value="inativo">Inativo</option>
            <option value="rascunho">Rascunho</option>
        </select>
        <select wire:model.live="filtroTipo" class="border rounded-lg px-3 py-2 text-sm focus:ring-1 focus:ring-[#00baff]">
            <option value="">Todos os tipos</option>
            <option value="ebook">E-book</option>
            <option value="audio">Áudio</option>
            <option value="literatura_digital">Literatura Digital</option>
            <option value="outro">Outro</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Produto</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Freelancer</th>
                        <th class="text-left px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Tipo</th>
                        <th class="text-right px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Preço</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Vendas</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Status</th>
                        <th class="text-center px-4 py-3 font-semibold text-gray-600 text-xs uppercase tracking-wide">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($produtos as $produto)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-4">
                            <div class="font-medium text-gray-900 max-w-xs truncate">{{ $produto->titulo }}</div>
                            <div class="text-xs text-gray-400 mt-0.5">{{ $produto->created_at->format('d/m/Y') }}</div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="text-gray-700">{{ $produto->freelancer->name }}</div>
                            <div class="text-xs text-gray-400">{{ $produto->freelancer->email }}</div>
                        </td>
                        <td class="px-4 py-4">
                            <span class="px-2 py-1 rounded-full text-xs bg-[#00baff]/10 text-[#00baff]">
                                {{ $produto->tipoLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-right font-semibold text-gray-800">
                            Kz {{ number_format($produto->preco, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-4 text-center text-gray-600">
                            {{ $produto->compras_count }}
                        </td>
                        <td class="px-4 py-4 text-center">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium
                                @if($produto->status === 'ativo') bg-green-100 text-green-700
                                @elseif($produto->status === 'em_moderacao') bg-yellow-100 text-yellow-700
                                @elseif($produto->status === 'inativo') bg-red-100 text-red-700
                                @else bg-gray-100 text-gray-600 @endif">
                                {{ $produto->statusLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center justify-center gap-2">
                                @if($produto->status === 'em_moderacao')
                                <button wire:click="aprovar({{ $produto->id }})"
                                    class="px-3 py-1.5 text-xs font-medium rounded-lg bg-green-100 text-green-700 hover:bg-green-200 transition">
                                    Aprovar
                                </button>
                                <button wire:click="rejeitar({{ $produto->id }})"
                                    class="px-3 py-1.5 text-xs font-medium rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition">
                                    Rejeitar
                                </button>
                                @elseif($produto->status === 'ativo')
                                <button wire:click="rejeitar({{ $produto->id }})"
                                    class="px-3 py-1.5 text-xs font-medium rounded-lg bg-red-100 text-red-700 hover:bg-red-200 transition">
                                    Desativar
                                </button>
                                <a href="{{ route('loja.show', $produto->slug) }}" target="_blank"
                                    class="px-3 py-1.5 text-xs font-medium rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-200 transition">
                                    Ver
                                </a>
                                @elseif($produto->status === 'inativo')
                                <button wire:click="aprovar({{ $produto->id }})"
                                    class="px-3 py-1.5 text-xs font-medium rounded-lg bg-green-100 text-green-700 hover:bg-green-200 transition">
                                    Reativar
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center text-gray-400">
                            Nenhum produto encontrado
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($produtos->hasPages())
        <div class="px-5 py-4 border-t">
            {{ $produtos->links() }}
        </div>
        @endif
    </div>

</div>
</div>
