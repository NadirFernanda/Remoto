@extends('layouts.main')

@section('content')
<div class="light-page min-h-screen pt-8 pb-12">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-gray-800 mb-2">Freelancers</h1>
            <p class="text-gray-500">Encontre profissionais qualificados para o seu projeto.</p>
        </div>
        @livewire('freelancer.listing')
        @livewire('client.send-proposal')
        @livewire('freelancer.preview')
    </div>
</div>
@endsection
