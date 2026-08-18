@if(auth()->check())
@extends('layouts.dashboard')

@section('dashboard-title', $user->name)

@section('dashboard-content')
@include('freelancer._profile-body', ['showBackLink' => false])
@endsection

@else
@extends('layouts.main')

@section('main-padding', 'pt-0')
@section('main-style', 'background:#0d1424')

@section('content')
@include('freelancer._profile-body', ['showBackLink' => true])
@endsection

@endif
