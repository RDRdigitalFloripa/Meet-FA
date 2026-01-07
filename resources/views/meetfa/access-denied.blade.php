@extends('layouts.meetfa')

@section('title', 'Acesso Negado')

@section('content')
<div class="max-w-lg mx-auto text-center py-16">
    <div class="bg-white rounded-xl shadow-lg p-8">
        <div class="w-20 h-20 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-6">
            <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>

        <h1 class="text-2xl font-bold text-gray-900 mb-2">Acesso Negado</h1>
        <p class="text-gray-600 mb-6">{{ $reason }}</p>

        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <h3 class="font-medium text-gray-900">{{ $room->title }}</h3>
            @if($room->createdBy)
                <p class="text-sm text-gray-500">Professor: {{ $room->createdBy->name }}</p>
            @endif
        </div>

        <a href="{{ route('meet.index') }}"
           class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Voltar
        </a>
    </div>
</div>
@endsection
