@extends('layouts.main')

@section('content')
<div class="pub-page">
    <div class="pub-container" style="padding-top:2.5rem;padding-bottom:3rem;">
        <div class="pub-hero" style="margin-bottom:2rem;">
            <div class="pub-hero-label">Profissionais</div>
            <h1 class="pub-hero-title">Freelancers Qualificados</h1>
            <p class="pub-hero-sub">Encontre profissionais talentosos para o seu projeto.</p>
        </div>
        @livewire('freelancer.listing')
        @livewire('client.send-proposal')
        @livewire('freelancer.preview')
    </div>
</div>
@endsection
