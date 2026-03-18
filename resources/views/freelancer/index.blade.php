@extends('layouts.main')

@section('content')
<div class="pub-page" style="padding-top:0">
    <div class="pub-container" style="padding-top:1.25rem;padding-bottom:3rem;">
        <div class="pub-hero" style="margin-bottom:2rem;">
            <div class="pub-hero-label">Profissionais</div>
            <h1 class="pub-hero-title">Freelancers Qualificados</h1>
            <p class="pub-hero-sub">Encontre profissionais talentosos para o seu projecto.</p>
        </div>
        @livewire('freelancer.listing')
        @livewire('client.send-proposal')
        @livewire('freelancer.preview')
    </div>
</div>
@endsection
