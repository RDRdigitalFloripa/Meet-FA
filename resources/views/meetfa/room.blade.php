@extends('layouts.meetfa')

@section('title', $room->title)



@section('content')
<div x-data="meetRoom()" x-init="init()">
    <!-- Room Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900">{{ $room->title }}</h1>

                @if($room->status === 'live')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                        <span class="w-2 h-2 bg-red-500 rounded-full mr-2 animate-pulse"></span>
                        AO VIVO
                    </span>
                @endif
            </div>

            <div class="mt-2 flex items-center gap-3">
                @if($room->createdBy)
                    <p class="text-gray-600">Professor: {{ $room->createdBy->name }}</p>
                @endif
                
                <button @click="copyLink()" 
                        class="text-sm flex items-center gap-1 text-blue-600 hover:text-blue-800 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                    <span x-text="copyButtonText"></span>
                </button>
            </div>
        </div>

        <div class="flex items-center gap-3">
            @if($room->created_by_user_id === auth()->id())
                @if($room->status !== 'live')
                    <button @click="startRoom()"
                            :disabled="loading"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition disabled:opacity-50">
                        <span x-show="!loading">Iniciar Aula</span>
                        <span x-show="loading" x-cloak>Aguarde...</span>
                    </button>
                @else
                    <button @click="endRoom()"
                            :disabled="loading"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition disabled:opacity-50">
                        <span x-show="!loading">Encerrar Aula</span>
                        <span x-show="loading" x-cloak>Aguarde...</span>
                    </button>
                @endif

                <a href="{{ route('meet.report', $room->uuid) }}"
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition">
                    Relatório
                </a>
            @endif
        </div>
    </div>

    <!-- Main Content Area with Chat Sidebar -->
    <div class="flex gap-4 h-[calc(100vh-200px)] min-h-[500px]">
        <!-- Jitsi Container -->
        <div id="jitsi-container" class="flex-1 shadow-xl rounded-xl overflow-hidden bg-[#1e1e1e] relative">
            
            @if($room->status === 'scheduled' && $room->created_by_user_id !== auth()->id())
                <!-- Waiting for Professor Overlay -->
                <div class="flex items-center justify-center h-full absolute inset-0 bg-gradient-to-br from-blue-900 to-blue-700 z-10">
                    <div class="text-center text-white px-8">
                        <div class="w-20 h-20 mx-auto mb-6 bg-white/10 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-white animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold mb-2">Aguardando Professor</h2>
                        <p class="text-blue-200 mb-4">A aula ainda não foi iniciada pelo professor.</p>
                        <p class="text-blue-300 text-sm">Esta página será atualizada automaticamente quando a aula começar.</p>
                        
                        <div class="mt-6 flex items-center justify-center gap-2 text-blue-200 text-sm">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Verificando status...
                        </div>
                    </div>
                </div>
                
                <script>
                    // Auto-refresh every 10 seconds to check if professor started
                    setTimeout(() => window.location.reload(), 10000);
                </script>
            @else
                <div x-show="!jitsiReady" class="flex items-center justify-center h-full absolute inset-0">
                    <div class="text-center text-white">
                        <svg class="animate-spin h-12 w-12 mx-auto mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p>Carregando sala...</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Chat Sidebar -->
        <div class="w-80 bg-white rounded-xl shadow-xl flex flex-col border border-gray-100" 
             x-data="chatSystem()" 
             x-init="initChat()">
            
            <div class="p-4 border-b border-gray-100 bg-gray-50 rounded-t-xl flex justify-between items-center">
                <h3 class="font-semibold text-gray-700">Chat da Turma</h3>
                <div class="flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                    <span class="text-xs text-gray-500">Online</span>
                </div>
            </div>

            <!-- Messages Area -->
            <div class="flex-1 overflow-y-auto p-4 space-y-4" id="chat-messages">
                <template x-for="msg in messages" :key="msg.id">
                    <div :class="{'flex justify-end': msg.is_me, 'flex justify-start': !msg.is_me}">
                        <div :class="{
                                'bg-blue-100 text-blue-900 rounded-br-none': msg.is_me && !msg.is_ai, 
                                'bg-gray-100 text-gray-900 rounded-bl-none': !msg.is_me && !msg.is_ai,
                                'bg-purple-100 text-purple-900 border border-purple-200': msg.is_ai
                             }" 
                             class="max-w-[85%] rounded-lg px-3 py-2 text-sm shadow-sm relative group">
                            
                            <div class="flex justify-between items-center gap-2 mb-1">
                                <span class="font-bold text-xs" x-text="msg.user_name"></span>
                                <span class="text-[10px] opacity-70" x-text="msg.time"></span>
                            </div>
                            
                            <p x-text="msg.message" class="whitespace-pre-wrap"></p>
                        </div>
                    </div>
                </template>
                
                <div x-show="messages.length === 0" class="text-center text-gray-400 text-sm py-10">
                    <p>Nenhuma mensagem ainda.</p>
                    <p class="text-xs mt-2">Dúvidas? Pergunte ao @Tutor!</p>
                </div>
            </div>

            <!-- Input Area -->
            <div class="p-3 border-t border-gray-100 bg-white rounded-b-xl">
                <form @submit.prevent="sendMessage" class="flex gap-2">
                    <input type="text" 
                           x-model="newMessage" 
                           placeholder="Digite sua mensagem..." 
                           class="flex-1 text-sm rounded-lg border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 transition"
                           :disabled="sending">
                    <button type="submit" 
                            class="bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 transition disabled:opacity-50"
                            :disabled="!newMessage.trim() || sending">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Participants Info -->
    <div class="mt-6 bg-white rounded-xl shadow p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <span x-text="participantCount + ' participante(s)'"></span>
            </div>

            <div class="text-sm text-gray-500" x-show="connectionTime > 0" x-cloak>
                Conectado há <span x-text="formatDuration(connectionTime)"></span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Jitsi External API -->
<script src="{{ $jitsiConfig['domain'] ? 'https://' . $jitsiConfig['domain'] . '/external_api.js' : 'https://meet.jit.si/external_api.js' }}"></script>

<script>
function chatSystem() {
    return {
        messages: [],
        newMessage: '',
        sending: false,
        pollingInterval: null,

        initChat() {
            this.fetchMessages();
            this.pollingInterval = setInterval(() => {
                this.fetchMessages();
            }, 3000); // Poll every 3 seconds
        },

        async fetchMessages() {
            try {
                const response = await fetch('{{ route("meet.chat.index", $room->uuid) }}');
                if (response.ok) {
                    const data = await response.json();
                    
                    const shouldScroll = this.messages.length !== data.length;
                    this.messages = data;

                    if (shouldScroll) {
                        this.$nextTick(() => {
                            const container = document.getElementById('chat-messages');
                            container.scrollTop = container.scrollHeight;
                        });
                    }
                }
            } catch (error) {
                console.error('Chat error:', error);
            }
        },

        async sendMessage() {
            if (!this.newMessage.trim()) return;

            this.sending = true;
            try {
                const response = await fetch('{{ route("meet.chat.store", $room->uuid) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ message: this.newMessage })
                });

                if (response.ok) {
                    this.newMessage = '';
                    this.fetchMessages(); 
                }
            } catch (error) {
                console.error('Send error:', error);
            }
            this.sending = false;
        }
    }
}

function meetRoom() {
    return {
        jitsiReady: false,
        loading: false,
        api: null,
        participantCount: 0,
        connectionTime: 0,
        connectionTimer: null,
        config: @json($jitsiConfig),
        room: @json($room),

        init() {
            this.loadJitsi();
        },

        loadJitsi() {
            const domain = this.config.domain;
            const options = {
                roomName: this.config.roomName,
                width: '100%',
                height: '100%',
                parentNode: document.querySelector('#jitsi-container'),
                userInfo: this.config.userInfo,
                configOverwrite: this.config.configOverwrite,
                interfaceConfigOverwrite: this.config.interfaceConfigOverwrite,
            };

            this.api = new JitsiMeetExternalAPI(domain, options);

            this.api.addListener('videoConferenceJoined', () => {
                this.jitsiReady = true;
                this.startConnectionTimer();
                // this.sendEvent('join'); 
            });

            this.api.addListener('videoConferenceLeft', () => {
                this.stopConnectionTimer();
            });

            this.api.addListener('participantJoined', (participant) => {
                this.participantCount++;
            });

            this.api.addListener('participantLeft', (participant) => {
                this.participantCount = Math.max(0, this.participantCount - 1);
            });

            this.api.addListener('readyToClose', () => {
                window.location.href = '{{ route("meet.index") }}';
            });
            
            this.api.addListener('recordingStatusChanged', (status) => {
                this.sendEvent(status.on ? 'recording_started' : 'recording_stopped', { status });
            });
        },

        async sendEvent(eventType, payload = {}) {
            try {
                const response = await fetch(this.config.meetfa.events_endpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        event_type: eventType,
                        user_id: this.config.meetfa.user_id,
                        payload: payload,
                    }),
                });
            } catch (error) {
                console.error('Failed to send event:', error);
            }
        },

        async startRoom() {
            this.loading = true;
            try {
                const response = await fetch('{{ route("meet.start", $room->uuid) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });
                const data = await response.json();
                if (data.success) {
                    window.location.reload();
                }
            } catch (error) {
                console.error('Failed to start room:', error);
            }
            this.loading = false;
        },

        async endRoom() {
            if (!confirm('Tem certeza que deseja encerrar a aula?')) return;

            this.loading = true;
            try {
                const response = await fetch('{{ route("meet.end", $room->uuid) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });
                const data = await response.json();
                if (data.success) {
                    this.api.dispose();
                    window.location.href = '{{ route("meet.index") }}';
                }
            } catch (error) {
                console.error('Failed to end room:', error);
            }
            this.loading = false;
        },

        copyLink() {
            const url = window.location.href;
            navigator.clipboard.writeText(url).then(() => {
                const originalText = this.copyButtonText;
                this.copyButtonText = 'Copiado!';
                setTimeout(() => {
                    this.copyButtonText = originalText;
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy: ', err);
            });
        },

        copyButtonText: 'Copiar Link',

        startConnectionTimer() {
            this.connectionTimer = setInterval(() => {
                this.connectionTime++;
            }, 1000);
        },

        stopConnectionTimer() {
            if (this.connectionTimer) {
                clearInterval(this.connectionTimer);
            }
        },

        formatDuration(seconds) {
            const hrs = Math.floor(seconds / 3600);
            const mins = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;

            if (hrs > 0) {
                return `${hrs}h ${mins}m ${secs}s`;
            } else if (mins > 0) {
                return `${mins}m ${secs}s`;
            }
            return `${secs}s`;
        },
    }
}
</script>
@endpush
