@extends('layouts.meetfa')

@section('title', 'Nova Sala')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h1 class="text-xl font-bold text-gray-900">Nova Sala de Aula</h1>
            <p class="text-sm text-gray-500 mt-1">Preencha os dados para criar uma nova sala</p>
        </div>

        <form action="{{ route('meet.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                    Título da Aula *
                </label>
                <input type="text"
                       name="title"
                       id="title"
                       value="{{ old('title') }}"
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition"
                       placeholder="Ex: Aula 01 - Introdução ao Direito Administrativo">
                @error('title')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Date/Time -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="starts_at" class="block text-sm font-medium text-gray-700 mb-1">
                        Início (opcional)
                    </label>
                    <input type="datetime-local"
                           name="starts_at"
                           id="starts_at"
                           value="{{ old('starts_at') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                    @error('starts_at')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="ends_at" class="block text-sm font-medium text-gray-700 mb-1">
                        Término (opcional)
                    </label>
                    <input type="datetime-local"
                           name="ends_at"
                           id="ends_at"
                           value="{{ old('ends_at') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                    @error('ends_at')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Presence Rules -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="min_presence_minutes" class="block text-sm font-medium text-gray-700 mb-1">
                        Presença mínima (minutos)
                    </label>
                    <input type="number"
                           name="min_presence_minutes"
                           id="min_presence_minutes"
                           value="{{ old('min_presence_minutes', 0) }}"
                           min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                    <p class="text-gray-500 text-xs mt-1">Tempo mínimo para considerar presença</p>
                </div>

                <div>
                    <label for="min_presence_percent" class="block text-sm font-medium text-gray-700 mb-1">
                        Presença mínima (%)
                    </label>
                    <input type="number"
                           name="min_presence_percent"
                           id="min_presence_percent"
                           value="{{ old('min_presence_percent', 0) }}"
                           min="0"
                           max="100"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition">
                    <p class="text-gray-500 text-xs mt-1">Percentual mínimo do tempo total</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('meet.index') }}"
                   class="px-4 py-2 text-gray-700 hover:text-gray-900 font-medium transition">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition shadow-lg shadow-primary-600/30">
                    Criar Sala
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
