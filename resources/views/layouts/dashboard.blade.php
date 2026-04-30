@extends('layouts.main')

@section('main-padding', 'pt-0')
@section('main-style', 'background:#f9fafb')

@section('content')
<div x-data="{ desktopCollapsed: false }"
     :class="desktopCollapsed ? 'dash-sidebar-collapsed' : ''"
     class="dash-wrapper">
    {{-- FAB (canto inferior direito) --}}
    <button id="dash-fab"
        @click="$store.sidebar.toggle(); desktopCollapsed = !desktopCollapsed"
        class="dash-sidebar-toggle"
        :aria-label="$store.sidebar.open ? 'Fechar menu lateral' : 'Abrir menu lateral'">
        <svg x-show="!$store.sidebar.open && !desktopCollapsed" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
        <svg x-show="$store.sidebar.open || desktopCollapsed" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>

    {{-- Overlay (mobile) --}}
    <div x-show="$store.sidebar.open" x-transition.opacity
         @click="$store.sidebar.close(); desktopCollapsed = false"
         class="dash-overlay" style="display:none;"></div>

    {{-- Sidebar --}}
    <aside class="dash-sidebar"
        :class="$store.sidebar.open ? 'dash-sidebar--open' : ''">
        @include('partials.dashboard-sidebar')
    </aside>

    {{-- Main content --}}
    <main class="dash-main">
        @if(session('role_redirect'))
            @php $requiredRole = session('role_redirect'); $__authUser = auth()->user(); @endphp
            <div class="mb-4 p-4 bg-amber-50 border border-amber-200 rounded-xl text-sm">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <div class="flex-1">
                        @if($requiredRole === 'cliente')
                            <p class="font-semibold text-amber-800">Esta área é exclusiva para clientes.</p>
                            <p class="text-amber-700 mt-0.5">Para criar projectos, contratar freelancers e gerir contratos, precisa de estar no <strong>Modo Cliente</strong>.</p>
                        @elseif($requiredRole === 'freelancer')
                            <p class="font-semibold text-amber-800">Esta área é exclusiva para freelancers.</p>
                            <p class="text-amber-700 mt-0.5">Para aceder ao painel de freelancer, propostas e projectos disponíveis, precisa de estar no <strong>Modo Freelancer</strong>.</p>
                        @elseif($requiredRole === 'creator')
                            <p class="font-semibold text-amber-800">Esta área é exclusiva para criadores de conteúdo.</p>
                            <p class="text-amber-700 mt-0.5">Para publicar conteúdo exclusivo e gerir assinantes, precisa de activar o seu <strong>Perfil de Criador</strong>.</p>
                        @else
                            <p class="font-semibold text-amber-800">Não tem permissão para aceder a esta área.</p>
                        @endif

                        @if(in_array($requiredRole, ['cliente', 'freelancer']) && $__authUser && $__authUser->canSwitchRole())
                            <form method="POST" action="{{ route('switch.role') }}" class="mt-2 inline">
                                @csrf
                                <input type="hidden" name="redirect_after" value="{{ request()->getPathInfo() }}">
                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold rounded-lg transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                    Mudar para Modo {{ $requiredRole === 'cliente' ? 'Cliente' : 'Freelancer' }} agora
                                </button>
                            </form>
                        @elseif($requiredRole === 'creator')
                            <a href="{{ route('creator.activate') }}" class="inline-flex items-center gap-1.5 mt-2 px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold rounded-lg transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                                Activar Perfil de Criador
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-xl text-center font-semibold text-sm">
                {{ session('error') }}
            </div>
        @endif
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-xl text-center font-semibold text-sm">
                {{ session('success') }}
            </div>
        @endif

        {{-- ── Mode-switch animated toast ─────────────────────── --}}
        @if(session('mode_switched'))
        <div x-data="{ show: true }"
             x-init="setTimeout(() => show = false, 4500)"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             style="position:fixed;top:5.5rem;left:50%;transform:translateX(-50%);z-index:9999;min-width:260px;max-width:calc(100vw - 2rem);"
             class="w-auto">
            <div class="flex items-center gap-3 px-4 py-3 rounded-2xl shadow-2xl border border-[#00baff]/30"
                 style="background:linear-gradient(135deg,#0f1a2e 0%,#0a1628 100%);color:#f1f5f9;">
                <span style="width:36px;height:36px;border-radius:10px;background:rgba(0,186,255,.18);border:1px solid rgba(0,186,255,.3);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="18" height="18" fill="none" stroke="#00baff" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                </span>
                <span class="text-sm font-semibold" style="color:#f1f5f9;">{{ session('mode_switched') }}</span>
                <button @click="show = false"
                        style="margin-left:auto;background:none;border:none;cursor:pointer;color:#64748b;padding:2px;"
                        aria-label="Fechar">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
        @endif
        @php
            $title = $dashboardTitle ?? null;
            $actions = $dashboardActions ?? null;
            $hideBackButton = $hideBackButton ?? false;
            $mainDashboardRoutes = [
                'client.dashboard', 'freelancer.dashboard', 'admin.dashboard',
                'creator.dashboard', 'dashboard', 'notifications', 'kyc.submit',
                'social.feed', 'social.creators', 'freelancers.index', 'loja.index',
                'admin.users', 'admin.financial', 'admin.disputes', 'admin.audit',
                'admin.social.moderation', 'admin.loja', 'client.briefing',
            ];
            $isMainDashboard = $hideBackButton || in_array(Route::currentRouteName(), $mainDashboardRoutes);
        @endphp

        {{-- Universal back button — shown on all sub-pages --}}
        @if(!$isMainDashboard)
            <div class="mb-3" id="dash-back-btn" style="display:none;">
                <a href="javascript:history.back()"
                   class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-800 font-medium transition group">
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-700 transition" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Voltar
                </a>
            </div>
            <script>
                (function() {
                    if (window.history.length > 1) {
                        var btn = document.getElementById('dash-back-btn');
                        if (btn) btn.style.display = '';
                    }
                })();
            </script>
        @endif

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
            @php $renderedTitle = $title ?? trim($__env->yieldContent('dashboard-title')) ?? ''; @endphp
            @if($renderedTitle)
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">{!! $renderedTitle !!}</h1>
            @endif
            <div class="flex items-center gap-3 flex-wrap">
                @if(!empty($actions))
                    {!! $actions !!}
                @elseif(View::hasSection('dashboard-actions'))
                    @yield('dashboard-actions')
                @endif
            </div>
        </div>

        {{-- Render either the Livewire slot content or the blade section content. --}}
        @php $slotContent = (isset($slot) ? trim((string) $slot) : ''); @endphp
        @if($slotContent !== '')
            {!! $slot !!}
        @else
            @yield('dashboard-content')
        @endif
    </main>
</div>
@endsection
