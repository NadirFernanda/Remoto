<div x-data x-init="setInterval(() => $wire.$refresh(), 30000)" class="relative inline-flex">
    <a href="{{ route('chat.inbox') }}"
       title="Mensagens"
       style="display:flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08);color:#e2e8f0;transition:background .15s,border-color .15s;text-decoration:none;"
       onmouseover="this.style.background='rgba(0,186,255,.13)';this.style.borderColor='rgba(0,186,255,.3)'" onmouseout="this.style.background='rgba(255,255,255,.06)';this.style.borderColor='rgba(255,255,255,.08)'">
        <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>
    </a>
    @if($unread > 0)
        <span style="position:absolute;top:-4px;right:-4px;min-width:16px;height:16px;background:#ef4444;border-radius:999px;font-size:.65rem;font-weight:700;color:#fff;display:flex;align-items:center;justify-content:center;padding:0 3px;border:2px solid var(--site-header-bg, #0f1a2e);line-height:1;">
            {{ $unread > 9 ? '9+' : $unread }}
        </span>
    @endif
</div>
