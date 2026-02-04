@extends('layouts.livewire')

@section('slot')
    @livewire('client.service-cancel', ['service' => $service])
@endsection
