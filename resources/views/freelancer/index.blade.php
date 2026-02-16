@extends('layouts.main')

@section('content')
<div class="max-w-6xl mx-auto py-8">
    <h1 class="text-2xl font-semibold mb-4">Freelancers</h1>
    <div class="bg-white p-4 rounded-lg shadow">
        @livewire('freelancer.listing')
    </div>
    @livewire('client.send-proposal')
    @livewire('freelancer.preview')
</div>
@endsection
