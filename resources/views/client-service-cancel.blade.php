@extends('layouts.main')

@section('slot')
    @livewire('client.service-cancel', ['service' => $service])
@endsection
