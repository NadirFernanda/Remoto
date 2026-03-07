<div>
    <p class="mb-6">Bem-vindo ao painel administrativo do SITE FREELANCER.</p>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white shadow rounded p-4">
            <h2 class="text-sm font-semibold text-gray-500">Usuários Totais</h2>
            <p class="text-2xl font-bold">{{ $stats['users_total'] ?? 0 }}</p>
        </div>
        <div class="bg-white shadow rounded p-4">
            <h2 class="text-sm font-semibold text-gray-500">Clientes</h2>
            <p class="text-2xl font-bold">{{ $stats['users_clients'] ?? 0 }}</p>
        </div>
        <div class="bg-white shadow rounded p-4">
            <h2 class="text-sm font-semibold text-gray-500">Freelancers</h2>
            <p class="text-2xl font-bold">{{ $stats['users_freelancers'] ?? 0 }}</p>
        </div>
        <div class="bg-white shadow rounded p-4">
            <h2 class="text-sm font-semibold text-gray-500">Admins</h2>
            <p class="text-2xl font-bold">{{ $stats['users_admins'] ?? 0 }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white shadow rounded p-4">
            <h2 class="text-sm font-semibold text-gray-500">Projetos Totais</h2>
            <p class="text-2xl font-bold">{{ $stats['services_total'] ?? 0 }}</p>
        </div>
        <div class="bg-white shadow rounded p-4">
            <h2 class="text-sm font-semibold text-gray-500">Publicados</h2>
            <p class="text-2xl font-bold">{{ $stats['services_published'] ?? 0 }}</p>
        </div>
        <div class="bg-white shadow rounded p-4">
            <h2 class="text-sm font-semibold text-gray-500">Em andamento</h2>
            <p class="text-2xl font-bold">{{ $stats['services_in_progress'] ?? 0 }}</p>
        </div>
        <div class="bg-white shadow rounded p-4">
            <h2 class="text-sm font-semibold text-gray-500">Cancelados</h2>
            <p class="text-2xl font-bold">{{ $stats['services_cancelled'] ?? 0 }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white shadow rounded p-4">
            <h2 class="text-sm font-semibold text-gray-500">Projetos Entregues</h2>
            <p class="text-2xl font-bold">{{ $stats['services_delivered'] ?? 0 }}</p>
        </div>
        <div class="bg-white shadow rounded p-4">
            <h2 class="text-sm font-semibold text-gray-500">Receita em Taxas (entregues)</h2>
            <p class="text-2xl font-bold">{{ money_aoa($stats['revenue_fees'] ?? 0) }}</p>
        </div>
        <div class="bg-white shadow rounded p-4">
            <h2 class="text-sm font-semibold text-gray-500">Próximos passos</h2>
            <ul class="list-disc pl-5 text-sm mt-1 space-y-1">
                <li>Adicionar lista de últimos projetos criados.</li>
                <li>Exibir últimos usuários cadastrados.</li>
                <li>Implementar filtros por período (7/30 dias).</li>
            </ul>
        </div>
        <div class="bg-white shadow rounded p-4">
            <h2 class="text-sm font-semibold text-gray-500">Taxa BRL → AOA</h2>
            <div class="flex items-center gap-3">
                <div id="aoa-rate-display" class="text-lg font-bold">{{ app(\App\Services\ExchangeRateService::class)->getRate() }}</div>
                <button id="refresh-aoa-btn" class="ml-auto bg-cyan-400 text-[#021018] rounded px-3 py-1 font-bold">Atualizar taxa</button>
            </div>
            <div id="aoa-rate-msg" class="text-sm text-gray-500 mt-2"></div>
        </div>
    </div>
    <div class="bg-white shadow rounded p-6 mt-8">
        <h2 class="text-lg font-bold mb-4">Últimos Usuários Cadastrados</h2>
        <table class="min-w-full text-sm">
            <thead>
                <tr class="bg-gray-100">
                    <th class="py-2 px-4 text-left">Nome</th>
                    <th class="py-2 px-4 text-left">E-mail</th>
                    <th class="py-2 px-4 text-left">Tipo</th>
                    <th class="py-2 px-4 text-left">Cadastro</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr class="border-b">
                    <td class="py-2 px-4">{{ $user->name }}</td>
                    <td class="py-2 px-4">{{ $user->email }}</td>
                    <td class="py-2 px-4">{{ ucfirst($user->role) }}</td>
                    <td class="py-2 px-4">{{ $user->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($users->isEmpty())
            <div class="text-center text-gray-500 py-4">Nenhum usuário cadastrado.</div>
        @endif
    </div>

    <script>
        document.getElementById('refresh-aoa-btn').addEventListener('click', function(){
            var btn = this; btn.disabled = true; btn.innerText = 'Atualizando...';
            fetch('/admin/refresh-aoa-rate', { method: 'POST', headers: { 'X-Requested-With':'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }, credentials: 'same-origin' })
                .then(r => r.json())
                .then(j => {
                    document.getElementById('aoa-rate-display').innerText = j.rate;
                    document.getElementById('aoa-rate-msg').innerText = 'Taxa atualizada com sucesso.';
                })
                .catch(e => { document.getElementById('aoa-rate-msg').innerText = 'Erro ao atualizar taxa.'; })
                .finally(()=>{ btn.disabled = false; btn.innerText = 'Atualizar taxa'; });
        });
    </script>
</div>
