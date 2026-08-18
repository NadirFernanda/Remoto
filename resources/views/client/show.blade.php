@extends(auth()->check() ? 'layouts.dashboard' : 'layouts.main')

@if(auth()->check())

@section('dashboard-title', $user->name)

@section('dashboard-content')
@include('client._profile-body', ['showBackLink' => false])
@endsection

@else

@section('main-padding', 'pt-0')
@section('main-style', 'background:#0d1424')

@section('content')
@include('client._profile-body', ['showBackLink' => true])
@endsection

@endif
