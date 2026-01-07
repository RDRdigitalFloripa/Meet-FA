Você é um engenheiro de software sênior, especialista em Laravel 12, arquitetura limpa e sistemas educacionais.

Estamos iniciando o desenvolvimento de um sistema chamado **MeetFA**, uma plataforma institucional de aulas ao vivo para uma faculdade, integrada a um SGA e usando **Jitsi (open-source)** como motor de videoconferência.

⚠️ IMPORTANTE
- NÃO é um clone do Google Meet.
- O MeetFA é um ORQUESTRADOR acadêmico (regras, presença, gravação, auditoria).
- O vídeo é responsabilidade do Jitsi (WebRTC).
- Tudo deve seguir boas práticas de Laravel 12.

---

## STACK OBRIGATÓRIA
- PHP 8.2+
- Laravel 12
- MySQL
- Blade + Alpine.js + Tailwind
- Jitsi Meet (self-hosted)
- Arquitetura limpa (Domain-driven light)
- Código claro, comentado e organizado

---

## OBJETIVO DO MVP
Criar um MVP funcional que permita:
1. Criar salas de aula ao vivo
2. Controlar acesso por regras acadêmicas
3. Registrar entrada/saída dos usuários
4. Calcular tempo conectado
5. Registrar eventos (join/leave)
6. Integrar uma sala Jitsi via embed
7. Preparar base para gravação e relatórios

---

## ESTRUTURA DE PASTAS (OBRIGATÓRIA)
Use esta estrutura:

app/
 └── Domain/
     └── MeetFA/
         ├── Models/
         ├── Actions/
         ├── Services/
         ├── Policies/
         ├── Jobs/
         ├── Events/
         ├── Providers/
         ├── Integrations/
         │   ├── SGA/
         │   ├── Moodle/
         │   └── CFaflix/
         └── Http/
             ├── Controllers/
             ├── Requests/
             └── Resources/

---

## MODELO DE DADOS (MVP)
Crie migrations para as seguintes tabelas:
- meet_providers
- meet_rooms
- meet_room_settings (opcional)
- meet_room_participants
- meet_recordings
- meet_events

Use BIGINT, FKs, índices e timestamps corretamente.

---

## ENTIDADES PRINCIPAIS
- MeetRoom
- MeetRoomParticipant
- MeetRecording
- MeetEvent
- MeetProvider

---

## REGRAS DE NEGÓCIO
- Apenas usuários autorizados podem entrar na sala
- Usuário pode ter múltiplos joins/leaves (registrar tudo)
- Presença será calculada posteriormente a partir dos registros
- Sala possui status (draft, scheduled, live, ended)
- UUID público para acesso à sala

---

## ROTAS INICIAIS
WEB:
- GET /meet
- GET /meet/{uuid}

API:
- POST /api/meet/rooms/{uuid}/events
- POST /api/meet/webhook/jitsi

---

## FRONTEND DA SALA
- Criar view Blade `resources/views/meetfa/room.blade.php`
- Integrar Jitsi usando `external_api.js`
- Capturar eventos:
  - participantJoined
  - participantLeft
  - readyToClose
- Enviar esses eventos para o backend via fetch/AJAX

---

## PROVEDOR DE VÍDEO
Crie uma interface `VideoProviderContract` e uma implementação:
- JitsiProvider

O sistema deve ser preparado para adicionar LiveKit no futuro sem refatorar o core.

---

## SEGURANÇA
- Use Policies do Laravel
- Use UUID público
- Prepare suporte a links assinados
- Logs auditáveis via meet_events

---

## ENTREGÁVEIS ESPERADOS
1. Migrations completas
2. Models com relacionamentos
3. Controllers básicos
4. Rotas configuradas
5. View da sala com Jitsi embed
6. Registro de participantes e eventos
7. Seeder inicial criando o provider "jitsi"

---

## FORMA DE TRABALHO
- Gere código real, não pseudocódigo
- Explique decisões quando necessário
- Não simplifique regras importantes
- Priorize clareza, organização e extensibilidade

Comece criando:
1️⃣ As migrations
2️⃣ Os models
3️⃣ O seeder do provider Jitsi
Depois avance para controllers, rotas e view da sala.
