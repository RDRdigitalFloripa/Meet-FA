@extends('layouts.meetfa')

@section('title', 'Salas de Aula')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Salas de Aula</h1>
            <p class="text-gray-600 mt-1">Gerencie suas aulas ao vivo</p>
        </div>

        @auth
            <a href="{{ route('meet.create') }}"
               class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition shadow-lg shadow-primary-600/30">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nova Sala
            </a>
        @endauth
    </div>

    <!-- Rooms Grid -->
    @if($rooms->count() > 0)
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($rooms as $room)
                <div class="glass-card rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition group">
                    <!-- Status Badge -->
                    <div class="p-4 border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            @switch($room->status)
                                @case('live')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        <span class="w-2 h-2 bg-red-500 rounded-full mr-2 animate-pulse"></span>
                                        AO VIVO
                                    </span>
                                    @break
                                @case('scheduled')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        Agendada
                                    </span>
                                    @break
                                @case('ended')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                        Encerrada
                                    </span>
                                    @break
                                @default
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                        Rascunho
                                    </span>
                            @endswitch

                            @if($room->created_by_user_id === auth()->id())
                                <span class="text-xs text-gray-500">Sua sala</span>
                            @endif
                        </div>
                    </div>

                    <!-- Room Info -->
                    <div class="p-4">
                        <h3 class="font-semibold text-lg text-gray-900 group-hover:text-primary-600 transition">
                            {{ $room->title }}
                        </h3>

                        @if($room->starts_at)
                            <p class="text-sm text-gray-500 mt-2 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $room->starts_at->format('d/m/Y H:i') }}
                            </p>
                        @endif

                        @if($room->createdBy)
                            <p class="text-sm text-gray-500 mt-1 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                {{ $room->createdBy->name }}
                            </p>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="px-4 pb-4 flex gap-2">
                        <a href="{{ route('meet.show', $room->uuid) }}"
                           class="flex-1 text-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition text-sm">
                            @if($room->status === 'live')
                                Entrar
                            @else
                                Ver Sala
                            @endif
                        </a>

                        @if($room->created_by_user_id === auth()->id())
                            <a href="{{ route('meet.report', $room->uuid) }}"
                               class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition text-sm">
                                Relatório
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $rooms->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-16 bg-white rounded-xl shadow">
            <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Nenhuma sala encontrada</h3>
            <p class="mt-2 text-gray-500">Crie sua primeira sala de aula para começar.</p>

            @auth
                <a href="{{ route('meet.create') }}"
                   class="mt-6 inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition">
                    Criar Sala
                </a>
            @endauth
        </div>
    @endif
</div>
@endsection
