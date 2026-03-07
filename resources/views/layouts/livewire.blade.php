@extends('layouts.main')

@section('content')
<div class="min-h-full bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6">
        {{ $slot }}
    </div>
</div>
@endsection
