<div class="mb-8">
    <h2 class="font-semibold text-xl mb-2 text-[#222]">Notificações Recentes</h2>
    <div class="overflow-x-auto">
        <table class="orders-table">
            <thead>
                <tr>
                    <th class="py-2 px-4">Mensagem</th>
                    <th class="py-2 px-4">Data</th>
                </tr>
            </thead>
            <tbody>
                @foreach($notifications->take(3) as $notification)
                    <tr class="border-b">
                        <td class="py-2 px-4">{{ $notification->message }}</td>
                        <td class="py-2 px-4">{{ $notification->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @endforeach
                @if($notifications->count() === 0)
                    <tr><td colspan="2" class="text-center py-4 text-[#888]">Nenhuma notificação recente.</td></tr>
                @endif
            </tbody>
        </table>
        <div class="mt-4 text-right">
            <a href="{{ route('freelancer.notifications') }}" class="btn-nowrap">Mais notificações</a>
        </div>
    </div>
</div>
