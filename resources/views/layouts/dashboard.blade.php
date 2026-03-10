@extends('layouts.main')

@section('content')
<div x-data="{ sidebarOpen: false }" class="dash-wrapper">
    {{-- Mobile sidebar toggle --}}
    <button @click="sidebarOpen = !sidebarOpen"
        class="dash-sidebar-toggle"
        aria-label="Abrir menu lateral">
        <svg x-show="!sidebarOpen" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
        <svg x-show="sidebarOpen" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
    </button>

    {{-- Overlay --}}
    <div x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false"
        class="dash-overlay" style="display:none;"></div>

    {{-- Sidebar --}}
    <aside class="dash-sidebar"
        :class="sidebarOpen ? 'dash-sidebar--open' : ''"
        @click.away="sidebarOpen = false">
        @include('partials.dashboard-sidebar')
    </aside>

    {{-- Main content --}}
    <main class="dash-main">
        <div class="bg-white rounded-2xl p-4 sm:p-6 shadow-md">
        @if(session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded shadow text-center font-semibold">
                {{ session('error') }}
            </div>
        @endif
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded shadow text-center font-semibold">
                {{ session('success') }}
            </div>
        @endif

        {{-- Render header: prefer layout variables passed by Livewire, otherwise blade sections. --}}
        @php
            $title = $dashboardTitle ?? null;
            $actions = $dashboardActions ?? null;
        @endphp

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
            <h1 class="text-xl sm:text-2xl font-bold">{!! $title ?? trim($__env->yieldContent('dashboard-title')) ?? 'Painel' !!}</h1>
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

        </div>
    </main>
</div>
@endsection
