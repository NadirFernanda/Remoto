<div>
    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    {{-- Header --}}
    <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem;">
        <div>
            <h2 style="font-size:1.25rem;font-weight:800;color:#0f172a;margin:0;">Minhas Publicações</h2>
            <p style="font-size:.82rem;color:#64748b;margin:.2rem 0 0;">Gere todo o seu conteúdo publicado</p>
        </div>
        <a href="{{ route('social.create') }}"
           style="display:inline-flex;align-items:center;gap:.45rem;background:#00baff;color:#fff;font-weight:700;font-size:.8rem;padding:.55rem 1.1rem;border-radius:10px;text-decoration:none;transition:background .17s;"
           onmouseover="this.style.background='#0099d4'" onmouseout="this.style.background='#00baff'">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Nova publicação
        </a>
    </div>

    {{-- Filter tabs --}}
    <div style="display:flex;gap:.4rem;margin-bottom:1.25rem;background:#f8fafc;border-radius:12px;padding:.3rem;width:fit-content;">
        @foreach(['all' => 'Todas', 'active' => 'Activas', 'archived' => 'Arquivadas'] as $val => $label)
            <button wire:click="$set('filter', '{{ $val }}')"
                    style="padding:.38rem .9rem;border-radius:9px;font-size:.78rem;font-weight:600;border:none;cursor:pointer;transition:all .15s;
                           {{ $filter === $val ? 'background:#fff;color:#00baff;box-shadow:0 1px 4px rgba(0,0,0,.08);' : 'background:transparent;color:#64748b;' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Posts list --}}
    @forelse($posts as $post)
        @php
            $typeIcons = [
                'text'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"/>',
                'image'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>',
                'video'  => '<path stroke-linecap="round" d="M15.75 10.5l4.72-4.72a.75.75 0 011.28.53v11.38a.75.75 0 01-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 002.25-2.25v-9a2.25 2.25 0 00-2.25-2.25h-9A2.25 2.25 0 002.25 7.5v9a2.25 2.25 0 002.25 2.25z"/>',
                'audio'  => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 9l10.5-3m0 6.553v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 11-.99-3.467l2.31-.66a2.25 2.25 0 001.632-2.163zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 01-1.632 2.163l-1.32.377a1.803 1.803 0 01-.99-3.467l2.31-.66A2.25 2.25 0 009 15.553z"/>',
                'link'   => '<path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/>',
            ];
            $icon = $typeIcons[$post->type] ?? $typeIcons['text'];
            $isActive = $post->status === 'active';
            $isArchived = $post->status === 'archived';
            $firstMedia = $post->media->first();
            $likesCount = $post->likes->count();
            $commentsCount = $post->comments->count();
        @endphp

        <div style="background:#fff;border:1.5px solid {{ $isArchived ? '#f1f5f9' : '#eef2f7' }};border-radius:14px;padding:1rem 1.1rem;margin-bottom:.7rem;display:flex;align-items:flex-start;gap:.9rem;opacity:{{ $isArchived ? '.65' : '1' }};">

            {{-- Type icon / thumbnail --}}
            <div style="flex-shrink:0;width:44px;height:44px;border-radius:10px;background:#f0f9ff;display:flex;align-items:center;justify-content:center;overflow:hidden;">
                @if($firstMedia && in_array($post->type, ['image']))
                    <img src="{{ Storage::url($firstMedia->path) }}" style="width:44px;height:44px;object-fit:cover;" loading="lazy">
                @else
                    <svg width="20" height="20" fill="none" stroke="#00baff" stroke-width="1.8" viewBox="0 0 24 24">
                        {!! $icon !!}
                    </svg>
                @endif
            </div>

            {{-- Content --}}
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;">
                    <span style="font-size:.67rem;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;">
                        {{ ucfirst($post->type) }}
                    </span>
                    @if($post->visibility === 'followers')
                        <span style="font-size:.63rem;background:#fef3c7;color:#b45309;border-radius:6px;padding:.1rem .4rem;font-weight:600;">Só seguidores</span>
                    @endif
                    <span style="font-size:.63rem;border-radius:6px;padding:.1rem .4rem;font-weight:600;
                                 {{ $isActive ? 'background:#dcfce7;color:#16a34a;' : 'background:#f1f5f9;color:#64748b;' }}">
                        {{ $isActive ? 'Activa' : 'Arquivada' }}
                    </span>
                </div>
                <p style="font-size:.83rem;color:#334155;margin:.2rem 0 .35rem;line-height:1.45;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">
                    {{ $post->content ?: ($post->link_title ?: '(sem texto)') }}
                </p>
                <div style="display:flex;align-items:center;gap:1rem;font-size:.72rem;color:#94a3b8;">
                    <span>{{ $post->created_at->diffForHumans() }}</span>
                    <span style="display:flex;align-items:center;gap:.25rem;">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                        {{ $likesCount }}
                    </span>
                    <span style="display:flex;align-items:center;gap:.25rem;">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z"/></svg>
                        {{ $commentsCount }}
                    </span>
                    <span style="display:flex;align-items:center;gap:.25rem;">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $post->views_count ?? 0 }}
                    </span>
                </div>
            </div>

            {{-- Actions --}}
            <div style="flex-shrink:0;display:flex;align-items:center;gap:.4rem;">
                {{-- Toggle archive/activate --}}
                <button wire:click="toggleStatus({{ $post->id }})"
                        title="{{ $isActive ? 'Arquivar' : 'Reactivar' }}"
                        style="width:32px;height:32px;border-radius:8px;border:1.5px solid #e2e8f0;background:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#64748b;transition:all .15s;"
                        onmouseover="this.style.borderColor='#00baff';this.style.color='#00baff'" onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#64748b'">
                    @if($isActive)
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                    @else
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                    @endif
                </button>

                {{-- Delete --}}
                @if($confirmDeleteId === $post->id)
                    <button wire:click="deletePost({{ $post->id }})"
                            style="height:32px;padding:0 .75rem;border-radius:8px;border:none;background:#ef4444;color:#fff;font-size:.72rem;font-weight:700;cursor:pointer;">
                        Confirmar
                    </button>
                    <button wire:click="$set('confirmDeleteId', null)"
                            style="height:32px;padding:0 .65rem;border-radius:8px;border:1.5px solid #e2e8f0;background:#fff;font-size:.72rem;color:#64748b;cursor:pointer;">
                        Cancelar
                    </button>
                @else
                    <button wire:click="$set('confirmDeleteId', {{ $post->id }})"
                            title="Eliminar"
                            style="width:32px;height:32px;border-radius:8px;border:1.5px solid #e2e8f0;background:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;color:#64748b;transition:all .15s;"
                            onmouseover="this.style.borderColor='#ef4444';this.style.color='#ef4444'" onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#64748b'">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                    </button>
                @endif
            </div>
        </div>
    @empty
        <div style="text-align:center;padding:4rem 1rem;">
            <svg width="48" height="48" fill="none" stroke="#d1d5db" stroke-width="1.3" viewBox="0 0 24 24" style="margin:0 auto .75rem;display:block;">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
            </svg>
            <p style="font-size:.95rem;font-weight:700;color:#475569;margin:0;">Ainda não tens publicações</p>
            <p style="font-size:.82rem;color:#94a3b8;margin:.35rem 0 1.25rem;">Começa a criar conteúdo para o teu público</p>
            <a href="{{ route('social.create') }}"
               style="display:inline-flex;align-items:center;gap:.4rem;background:#00baff;color:#fff;font-weight:700;font-size:.8rem;padding:.55rem 1.1rem;border-radius:10px;text-decoration:none;">
                + Nova publicação
            </a>
        </div>
    @endforelse

    <div style="margin-top:1.5rem;">
        {{ $posts->links() }}
    </div>
</div>
