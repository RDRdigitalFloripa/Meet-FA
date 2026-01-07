# 🧱 Estrutura Técnica – MeetFA
### Laravel 12 + Jitsi (Open Source)

---

## 1. Visão Geral da Arquitetura

O **MeetFA** é uma aplicação Laravel responsável por **governança acadêmica** das aulas ao vivo.  
A transmissão de vídeo é delegada a um **motor WebRTC** (Jitsi), enquanto o MeetFA controla:

- autenticação e autorização
- regras acadêmicas (matrícula, financeiro, turma)
- presença automática
- gravações
- auditoria e relatórios

### Componentes
- **Backend:** Laravel 12 (PHP 8.2+)
- **Frontend:** Blade + Alpine.js + Tailwind
- **Vídeo:** Jitsi Meet (self-hosted)
- **Banco:** MySQL / PostgreSQL
- **Storage:** Local / S3 compatível (R2, MinIO)
- **Fila:** sync (dev) → database/redis (prod)

---

## 2. Responsabilidades do MeetFA (Core)

O MeetFA **não transmite vídeo**. Ele:

- valida acesso do usuário
- cria e gerencia salas
- registra entrada/saída
- calcula presença
- controla gravações
- integra com SGA, Moodle e CFA-FLIX
- gera evidências acadêmicas

---

## 3. Estrutura de Pastas (Laravel)

app/
└── Domain/
└── MeetFA/
├── Models/
├── Actions/
├── Services/
├── Policies/
├── Jobs/
├── Events/
├── Listeners/
├── Providers/
├── Integrations/
│ ├── SGA/
│ ├── Moodle/
│ └── CFaflix/
└── Http/
├── Controllers/
├── Requests/
└── Resources/

database/
├── migrations/
└── seeders/

routes/
├── web.php
└── api.php

resources/
└── views/
└── meetfa/


---

## 4. Módulos Técnicos

### 4.1 Gerenciamento de Salas
- criação/agendamento
- status: draft, scheduled, live, ended
- vínculo com disciplina/turma

**Principais classes**
- MeetRoom (Model)
- CreateRoomAction
- StartRoomAction
- EndRoomAction

---

### 4.2 Controle de Acesso (Regras Acadêmicas)
- matrícula ativa
- financeiro regular
- turma correta
- janela de horário

**Classes**
- RoomPolicy
- JoinGuardService
- EvaluateJoinRulesAction

---

### 4.3 Presença
- captura join/leave
- soma de tempo conectado
- regra mínima (minutos / percentual)
- exportação para SGA

**Classes**
- MeetRoomParticipant
- ComputeAttendanceJob (fase 2)

---

### 4.4 Gravação
- controle de status
- recepção de eventos do provedor
- envio para storage/CFA-FLIX

**Classes**
- MeetRecording
- HandleRecordingReadyJob
- PublishRecordingToCFaflixAction

---

### 4.5 Auditoria e Eventos
- eventos técnicos
- logs de acesso
- evidência acadêmica

**Classes**
- MeetEvent
- RegisterMeetEventAction

---

## 5. Provedores de Vídeo (Arquitetura Plugável)

### Interface comum
VideoProviderContract
├── createRoom()
├── getJoinConfig()
├── handleWebhook()
├── startRecording()
└── stopRecording()


### Implementações
- JitsiProvider (MVP)
- LiveKitProvider (fase 2+)

### Configuração (.env)

MEET_PROVIDER=jitsi
JITSI_BASE_URL=https://meet.faculdadeanaspsead.com.br


---

## 6. Rotas do Sistema

### Web
- GET /meet
- GET /meet/{uuid}
- GET /meet/{uuid}/report

### API
- POST /api/meet/rooms/{uuid}/events
- POST /api/meet/webhook/jitsi

---

## 7. Frontend da Sala

- Embed via `external_api.js`
- Eventos capturados:
  - participantJoined
  - participantLeft
  - readyToClose
  - recordingStatusChanged

Eventos são enviados ao backend para registro.

---

## 8. Segurança

- Auth padrão Laravel
- Policies para:
  - view
  - join
  - manage
  - report
- Links assinados (`URL::temporarySignedRoute`)
- Logs auditáveis

---

## 9. Jobs e Filas

### Jobs principais
- ComputeAttendanceJob
- HandleRecordingReadyJob
- ExportReportJob

### Queue
- Dev: sync
- Prod: database ou redis

---

## 10. Infraestrutura (Open Source)

### MVP
- 1 VPS:
  - MeetFA
  - Banco
  - Jitsi (opcional separado)

### Produção recomendada
- VPS MeetFA + DB
- VPS Jitsi
- VPS Jibri (gravação)
- Storage externo (R2 / MinIO)
- Nginx ou Traefik + SSL

---

## 11. Checklist Técnico do MVP

- [ ] CRUD de salas
- [ ] Policy de acesso
- [ ] Registro de participantes
- [ ] Registro de eventos
- [ ] Embed Jitsi funcional
- [ ] Seed do provider Jitsi

---

## 12. Evolução Planejada

- Fase 2: presença consolidada + gravação completa
- Fase 3: multi-instituição + IA + LiveKit

---

## 13. Considerações Finais

O MeetFA é um **orquestrador acadêmico de aulas ao vivo**, não apenas uma ferramenta de videoconferência.  
Seu valor está no **controle, evidência e integração educacional**, mantendo custo zero em licenças.

---

