@extends('layouts.main')

@section('main-padding', 'pt-[70px]')
@section('main-style', 'background:#f9fafb')

@section('content')
<div x-data="{ sidebarOpen: false, desktopCollapsed: false }"
     @toggle-sidebar.window="sidebarOpen = !sidebarOpen"
     :class="desktopCollapsed ? 'dash-sidebar-collapsed' : ''"
     class="dash-wrapper">
    {{-- Sidebar toggle — desktop only (colapsa/expande o sidebar lateral) --}}
    <button @click="desktopCollapsed = !desktopCollapsed"
        class="dash-sidebar-toggle"
        :aria-label="desktopCollapsed ? 'Expandir menu lateral' : 'Colapsar menu lateral'">
        <svg x-show="!desktopCollapsed" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
        <svg x-show="desktopCollapsed" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>

    {{-- Overlay (mobile) — fecha sidebar ao clicar fora --}}
    <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
        class="dash-overlay" style="display:none;"></div>

    {{-- Sidebar --}}
    <aside class="dash-sidebar"
        :class="sidebarOpen ? 'dash-sidebar--open' : ''">
        @include('partials.dashboard-sidebar')
    </aside>

    {{-- Main content --}}
    <main class="dash-main">
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

        {{-- Render header: prefer layout variables passed by Livewire, otherwise blade sections. --}}
        @php
            $title = $dashboardTitle ?? null;
            $actions = $dashboardActions ?? null;
        @endphp

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-800">{!! $title ?? trim($__env->yieldContent('dashboard-title')) ?? 'Painel' !!}</h1>
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
