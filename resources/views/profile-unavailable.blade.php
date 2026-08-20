@extends(auth()->check() ? 'layouts.dashboard' : 'layouts.main')

@if(auth()->check())

@section('dashboard-title', 'Perfil indisponível')

@section('dashboard-content')
@include('partials.profile-unavailable-body')
@endsection

@else

@section('main-padding', 'pt-0')
@section('main-style', 'background:#0d1424')

@section('content')
@include('partials.profile-unavailable-body')
@endsection

@endif
