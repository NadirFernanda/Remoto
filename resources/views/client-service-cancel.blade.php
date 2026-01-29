@extends('layouts.app')

@section('content')
    @livewire('client.service-cancel', ['service' => $service])
@endsection
