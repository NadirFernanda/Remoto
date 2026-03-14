<div>
    <ul>
        @forelse($notifications as $notification)
            <li class="mb-3 flex items-start">
                <span class="inline-block w-2 h-2 rounded-full mt-2 mr-3 {{
                    $notification->type === 'success' ? 'bg-green-500' :
                    ($notification->type === 'warning' ? 'bg-yellow-500' :
                    ($notification->type === 'info' ? 'bg-blue-500' : 'bg-gray-400'))
                }}"></span>
                <div>
                    <div class="font-medium {{
                        $notification->type === 'success' ? 'text-green-700' :
                        ($notification->type === 'warning' ? 'text-yellow-700' :
                        ($notification->type === 'info' ? 'text-blue-700' : 'text-gray-700'))
                    }}">
                        {{ $notification->message }}
                    </div>
                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</div>
                </div>
            </li>
        @empty
            <li class="text-gray-500">Nenhuma notificação encontrada.</li>
        @endforelse
    </ul>
</div>
