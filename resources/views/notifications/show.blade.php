@extends('layouts.main')

@section('content')
@php
    $senderName = $notification->sender_name && $notification->sender_name !== 'Administração'
        ? $notification->sender_name
        : null;
    $initial = strtoupper(substr($senderName ?? 'A', 0, 1));
    $supportRoute = auth()->user()?->activeRole() === 'freelancer'
        ? route('freelancer.support')
        : route('client.support');
@endphp

<div style="min-height:100%;background:#f8fafc;padding:2.5rem 1rem;">
<div style="max-width:680px;margin:0 auto;">

    {{-- Breadcrumb --}}
    <a href="{{ url()->previous() }}" style="display:inline-flex;align-items:center;gap:.4rem;font-size:.85rem;color:#64748b;text-decoration:none;margin-bottom:1.75rem;font-weight:500;" onmouseover="this.style.color='#0070ff'" onmouseout="this.style.color='#64748b'">
        <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        Voltar às notificações
    </a>

    {{-- Card principal --}}
    <div style="background:#fff;border-radius:20px;box-shadow:0 4px 24px rgba(0,0,0,.07);overflow:hidden;">

        {{-- Header azul escuro --}}
        <div style="background:linear-gradient(135deg,#0052cc 0%,#0a1228 100%);padding:2rem 2rem 1.75rem;">
            {{-- Badge --}}
            <div style="display:inline-flex;align-items:center;gap:.5rem;background:rgba(255,255,255,.12);border:1px solid rgba(255,255,255,.2);border-radius:50px;padding:.3rem .9rem;margin-bottom:1.25rem;">
                <svg style="width:13px;height:13px;color:#00baff;flex-shrink:0;" fill="none" stroke="#00baff" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span style="font-size:.75rem;font-weight:600;color:rgba(255,255,255,.9);letter-spacing:.02em;">
                    @if($senderName) Mensagem do administrador {{ $senderName }} @else Mensagem da Administração @endif
                </span>
            </div>

            {{-- Título --}}
            <h1 style="font-size:1.5rem;font-weight:800;color:#fff;margin:0 0 1.25rem;line-height:1.3;">
                {{ $notification->title ?: 'Mensagem do Admin' }}
            </h1>

            {{-- Avatar + nome + data --}}
            <div style="display:flex;align-items:center;gap:.75rem;">
                <div style="width:38px;height:38px;border-radius:50%;background:linear-gradient(135deg,#00baff,#0070ff);display:flex;align-items:center;justify-content:center;font-size:1rem;font-weight:800;color:#fff;flex-shrink:0;box-shadow:0 2px 8px rgba(0,186,255,.4);">
                    {{ $initial }}
                </div>
                <div>
                    <div style="font-size:.9rem;font-weight:700;color:#fff;">{{ $senderName ?? 'Administração 24 Horas' }}</div>
                    <div style="font-size:.75rem;color:rgba(255,255,255,.55);margin-top:.1rem;">{{ $notification->created_at->format('d/m/Y \à\s H:i') }}</div>
                </div>
            </div>
        </div>

        {{-- Corpo da mensagem --}}
        <div style="padding:2rem;">
            <p style="font-size:1rem;color:#334155;line-height:1.75;margin:0;white-space:pre-line;">{{ $notification->message }}</p>
        </div>

        {{-- Footer com CTA de suporte --}}
        <div style="border-top:1px solid #f1f5f9;padding:1.25rem 2rem;background:#f8fafc;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.75rem;">
            <span style="font-size:.8rem;color:#94a3b8;">Tem dúvidas sobre esta mensagem?</span>
            <a href="{{ $supportRoute }}" style="display:inline-flex;align-items:center;gap:.5rem;font-size:.82rem;font-weight:600;color:#0070ff;text-decoration:none;border:1px solid #bfdbfe;border-radius:8px;padding:.4rem .9rem;background:#eff6ff;" onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
                <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                Contactar Suporte
            </a>
        </div>
    </div>

</div>
</div>
@endsection

