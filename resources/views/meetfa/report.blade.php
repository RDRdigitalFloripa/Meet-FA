@extends('layouts.meetfa')

@section('title', 'Relatório - ' . $room->title)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Relatório da Aula</h1>
            <p class="text-gray-600 mt-1">{{ $room->title }}</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('meet.show', $room->uuid) }}"
               class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition">
                Voltar à Sala
            </a>
            <button onclick="window.print()"
                    class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition">
                Exportar PDF
            </button>
        </div>
    </div>

    <!-- Room Info Card -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Informações da Aula</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-500">Status</p>
                <p class="font-medium text-gray-900">
                    @switch($room->status)
                        @case('live')
                            <span class="text-red-600">Ao Vivo</span>
                            @break
                        @case('ended')
                            <span class="text-gray-600">Encerrada</span>
                            @break
                        @case('scheduled')
                            <span class="text-blue-600">Agendada</span>
                            @break
                        @default
                            <span class="text-yellow-600">Rascunho</span>
                    @endswitch
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Início</p>
                <p class="font-medium text-gray-900">
                    {{ $room->starts_at ? $room->starts_at->format('d/m/Y H:i') : '-' }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Término</p>
                <p class="font-medium text-gray-900">
                    {{ $room->ends_at ? $room->ends_at->format('d/m/Y H:i') : '-' }}
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Total de Participantes</p>
                <p class="font-medium text-gray-900">{{ $participants->count() }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Presença Mínima</p>
                <p class="font-medium text-gray-900">
                    @if($room->min_presence_minutes > 0)
                        {{ $room->min_presence_minutes }} minutos
                    @elseif($room->min_presence_percent > 0)
                        {{ $room->min_presence_percent }}%
                    @else
                        Não definida
                    @endif
                </p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Gravações</p>
                <p class="font-medium text-gray-900">{{ $room->recordings->count() }}</p>
            </div>
        </div>
    </div>

    <!-- Recordings Section -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Gravações da Aula</h2>
        </div>

        @if($room->recordings->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data/Hora</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duração</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($room->recordings as $recording)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $recording->started_at ? $recording->started_at->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $recording->duration_seconds ? gmdate("H:i:s", $recording->duration_seconds) : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($recording->status === 'ready')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Pronta</span>
                                    @elseif($recording->status === 'processing')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Processando</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ $recording->status }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($recording->public_url)
                                        <a href="{{ $recording->public_url }}" target="_blank" class="text-primary-600 hover:text-primary-900 mr-3">Assistir</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <p>Nenhuma gravação disponível para esta aula.</p>
                <p class="text-xs mt-1">As gravações aparecem aqui automaticamente após o processamento.</p>
            </div>
        @endif
    </div>

    <!-- Participants Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Lista de Participantes</h2>
        </div>

        @if($participants->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Participante
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Primeira Entrada
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Última Saída
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tempo Total
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($participants as $participant)
                            @php
                                $totalMinutes = floor($participant->total_seconds / 60);
                                $isPresent = $room->min_presence_minutes > 0
                                    ? $totalMinutes >= $room->min_presence_minutes
                                    : true;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-primary-700 font-medium text-sm">
                                                {{ substr($participant->user?->name ?? 'U', 0, 1) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">
                                                {{ $participant->user?->name ?? 'Usuário #' . $participant->user_id }}
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                {{ $participant->user?->email ?? '' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $participant->first_join ? \Carbon\Carbon::parse($participant->first_join)->format('H:i:s') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $participant->last_leave ? \Carbon\Carbon::parse($participant->last_leave)->format('H:i:s') : 'Conectado' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @php
                                        $hours = floor($participant->total_seconds / 3600);
                                        $minutes = floor(($participant->total_seconds % 3600) / 60);
                                        $seconds = $participant->total_seconds % 60;
                                    @endphp
                                    @if($hours > 0)
                                        {{ $hours }}h {{ $minutes }}m
                                    @elseif($minutes > 0)
                                        {{ $minutes }}m {{ $seconds }}s
                                    @else
                                        {{ $seconds }}s
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($isPresent)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Presente
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Ausente
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12 text-gray-500">
                <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <p>Nenhum participante registrado ainda.</p>
            </div>
        @endif
    </div>

    <!-- Event Log -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="text-lg font-semibold text-gray-900">Log de Eventos</h2>
        </div>

        @if($room->events->count() > 0)
            <div class="max-h-64 overflow-y-auto">
                <div class="divide-y divide-gray-100">
                    @foreach($room->events->sortByDesc('occurred_at')->take(50) as $event)
                        <div class="px-6 py-3 flex items-center justify-between hover:bg-gray-50">
                            <div class="flex items-center gap-3">
                                @switch($event->event_type)
                                    @case('join')
                                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                        <span class="text-sm text-gray-600">Entrada</span>
                                        @break
                                    @case('leave')
                                        <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                                        <span class="text-sm text-gray-600">Saída</span>
                                        @break
                                    @case('room_created')
                                        <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                        <span class="text-sm text-gray-600">Sala criada</span>
                                        @break
                                    @case('room_ended')
                                        <span class="w-2 h-2 bg-gray-500 rounded-full"></span>
                                        <span class="text-sm text-gray-600">Sala encerrada</span>
                                        @break
                                    @default
                                        <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                                        <span class="text-sm text-gray-600">{{ $event->event_type }}</span>
                                @endswitch

                                @if($event->user)
                                    <span class="text-sm text-gray-900">{{ $event->user->name }}</span>
                                @endif
                            </div>

                            <span class="text-xs text-gray-500">
                                {{ $event->occurred_at->format('d/m/Y H:i:s') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <p>Nenhum evento registrado.</p>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    @media print {
        header, footer, button, a { display: none !important; }
        .shadow-lg { box-shadow: none !important; }
    }
</style>
@endpush
@endsection
