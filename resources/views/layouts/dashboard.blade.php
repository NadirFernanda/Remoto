@extends('layouts.main')

@section('content')
<div class="flex min-h-screen bg-gray-50">
    <aside class="w-64 bg-white border-r border-gray-100 flex flex-col shadow-sm" style="min-height:100vh">
        @include('partials.dashboard-sidebar')
    </aside>

    <main class="flex-1 p-8">
        <div class="bg-white rounded-2xl p-6 shadow-md">
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

        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold">{!! $title ?? trim($__env->yieldContent('dashboard-title')) ?? 'Painel' !!}</h1>
            <div class="flex items-center gap-3">
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
