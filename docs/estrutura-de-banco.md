# 🗄️ Estrutura de Banco – MeetFA (MVP + Evolução)
> Modelo relacional sugerido para o MeetFA, pensando em integração com SGA/Moodle e motor de vídeo (Jitsi/LiveKit).
>
> **Foco do MVP:** salas, participação/presença, gravações e logs auditáveis.

---

## 1) Visão Geral (Entidades)

- **meet_rooms**: sala/aula ao vivo (por turma/disciplina/data)
- **meet_room_participants**: participação por usuário (entrada/saída/tempo)
- **meet_attendances**: presença consolidada (regra mínima, status)
- **meet_recordings**: gravações geradas (arquivo/URL, duração, status)
- **meet_events**: eventos técnicos recebidos do provedor (join/leave/recording)
- **meet_room_settings**: configurações da sala (mudo inicial, lobby, etc)
- **meet_providers**: provedores de vídeo (Jitsi, LiveKit) e config
- **meet_access_tokens**: links assinados/tokens (opcional)
- **meet_reports**: snapshots/exportações (opcional)

> ✅ **MVP mínimo real:** `meet_rooms`, `meet_room_participants`, `meet_recordings`, `meet_events`

---

## 2) Convenções

- Chaves primárias: `id` (bigint unsigned)
- UUID público para sala: `uuid` (string/uuid indexado)
- Soft deletes (opcional): `deleted_at`
- Auditoria: `created_at`, `updated_at`
- Multi-tenant (opcional futuro): `tenant_id` (indexado)
- Integração SGA/Moodle:
  - `course_id`, `class_id`, `discipline_id` (ids internos do SGA)
  - `moodle_course_id`, `moodle_cm_id` (opcional)

---

## 3) Tabelas (MVP)

### 3.1 `meet_providers`
Cadastro de provedores e configuração base.

**Campos**
- `id` bigint PK
- `name` varchar(50) (ex: `jitsi`, `livekit`)
- `base_url` varchar(255) (ex: `https://meet.fa.edu.br`)
- `config` json (chaves, opções, etc.)
- `is_active` tinyint(1)
- timestamps

**Índices**
- UNIQUE(`name`)
- INDEX(`is_active`)

---

### 3.2 `meet_rooms`
Representa uma sessão de aula ao vivo.

**Campos**
- `id` bigint PK
- `uuid` char(36) / uuid (public id)
- `provider_id` bigint FK -> meet_providers.id
- `title` varchar(150) (ex: "Aula 01 - Introdução")
- `room_name` varchar(180) (nome interno no provedor, ex: slug/uuid)
- `starts_at` datetime nullable
- `ends_at` datetime nullable
- `status` enum/varchar(30) (draft|scheduled|live|ended|canceled)
- `created_by_user_id` bigint nullable (professor/admin)
- **Integração acadêmica**
  - `course_id` bigint nullable
  - `class_id` bigint nullable
  - `discipline_id` bigint nullable
  - `lesson_id` bigint nullable (aula no SGA, se existir)
- **Políticas**
  - `min_presence_minutes` int default 0
  - `min_presence_percent` int default 0
  - `requires_financial_ok` tinyint(1) default 1
  - `requires_enrollment` tinyint(1) default 1
- `metadata` json nullable
- timestamps

**FKs**
- FK(`provider_id`) -> meet_providers.id

**Índices**
- UNIQUE(`uuid`)
- INDEX(`provider_id`, `status`)
- INDEX(`course_id`, `class_id`, `discipline_id`)
- INDEX(`starts_at`)

---

### 3.3 `meet_room_settings`
Config por sala (override do provedor).

**Campos**
- `id` bigint PK
- `room_id` bigint FK -> meet_rooms.id
- `enable_lobby` tinyint(1) default 0
- `mute_on_start` tinyint(1) default 1
- `camera_off_on_start` tinyint(1) default 0
- `allow_recording` tinyint(1) default 1
- `max_participants` int nullable
- `settings` json nullable (extras)
- timestamps

**FKs**
- FK(`room_id`) -> meet_rooms.id ON DELETE CASCADE

**Índices**
- UNIQUE(`room_id`)

---

### 3.4 `meet_room_participants`
Participação individual por sessão (várias entradas por usuário podem acontecer).

**Campos**
- `id` bigint PK
- `room_id` bigint FK -> meet_rooms.id
- `user_id` bigint (id do usuário no sistema/SGA)
- `role` enum/varchar(20) (student|teacher|moderator|admin)
- `joined_at` datetime
- `left_at` datetime nullable
- `duration_seconds` int default 0
- `ip_address` varchar(45) nullable
- `user_agent` varchar(255) nullable
- `provider_participant_id` varchar(120) nullable
- `provider_payload` json nullable
- timestamps

**FKs**
- FK(`room_id`) -> meet_rooms.id ON DELETE CASCADE

**Índices**
- INDEX(`room_id`, `user_id`)
- INDEX(`user_id`)
- INDEX(`joined_at`)

> Nota: se você quiser consolidar por usuário (1 linha por usuário/sala), pode usar UNIQUE(`room_id`,`user_id`) e atualizar entradas/saídas na mesma linha.

---

### 3.5 `meet_attendances` (opcional no MVP, recomendado na Fase 2)
Presença consolidada com regra acadêmica.

**Campos**
- `id` bigint PK
- `room_id` bigint FK -> meet_rooms.id
- `user_id` bigint
- `total_seconds` int default 0
- `presence_status` enum/varchar(20) (present|absent|partial|excused)
- `rule_applied` json nullable (minutos, %, etc.)
- `computed_at` datetime nullable
- timestamps

**FKs**
- FK(`room_id`) -> meet_rooms.id ON DELETE CASCADE

**Índices**
- UNIQUE(`room_id`, `user_id`)
- INDEX(`presence_status`)

---

### 3.6 `meet_recordings`
Gravações associadas à sala.

**Campos**
- `id` bigint PK
- `room_id` bigint FK -> meet_rooms.id
- `provider_recording_id` varchar(120) nullable
- `status` enum/varchar(30) (queued|recording|processing|ready|failed)
- `started_at` datetime nullable
- `ended_at` datetime nullable
- `duration_seconds` int default 0
- **Destino/Storage**
  - `storage_disk` varchar(50) nullable (local|s3|r2|minio|vimeo)
  - `storage_path` varchar(255) nullable
  - `public_url` varchar(500) nullable
- `checksum` varchar(128) nullable
- `metadata` json nullable
- timestamps

**FKs**
- FK(`room_id`) -> meet_rooms.id ON DELETE CASCADE

**Índices**
- INDEX(`room_id`, `status`)
- INDEX(`provider_recording_id`)

---

### 3.7 `meet_events`
Eventos técnicos/auditáveis vindos do provedor e do sistema.

**Campos**
- `id` bigint PK
- `room_id` bigint FK -> meet_rooms.id
- `event_type` varchar(60) (join|leave|recording_started|recording_ready|room_created|room_ended|error)
- `user_id` bigint nullable
- `provider` varchar(50) (jitsi|livekit)
- `provider_event_id` varchar(120) nullable
- `payload` json nullable
- `occurred_at` datetime
- timestamps

**FKs**
- FK(`room_id`) -> meet_rooms.id ON DELETE CASCADE

**Índices**
- INDEX(`room_id`, `event_type`)
- INDEX(`user_id`)
- INDEX(`occurred_at`)

---

## 4) Tabelas para Controle de Acesso (Opcional / Segurança)

### 4.1 `meet_access_tokens`
Tokens de acesso para links assinados / convidados / tempo limitado.

**Campos**
- `id` bigint PK
- `room_id` bigint FK
- `token` char(64) UNIQUE (hash)
- `user_id` bigint nullable
- `expires_at` datetime
- `used_at` datetime nullable
- `metadata` json nullable
- timestamps

**FKs**
- FK(`room_id`) -> meet_rooms.id ON DELETE CASCADE

**Índices**
- UNIQUE(`token`)
- INDEX(`expires_at`)
- INDEX(`room_id`, `user_id`)

---

## 5) Relacionamentos (Resumo)

- `meet_providers (1) -> (N) meet_rooms`
- `meet_rooms (1) -> (1) meet_room_settings`
- `meet_rooms (1) -> (N) meet_room_participants`
- `meet_rooms (1) -> (N) meet_recordings`
- `meet_rooms (1) -> (N) meet_events`
- `meet_rooms (1) -> (N) meet_access_tokens`
- `meet_rooms (1) -> (N) meet_attendances`

---

## 6) Regras de Negócio (Banco + App)

### 6.1 Integridade
- `meet_room_settings.room_id` único (1:1)
- Participação:
  - MVP: permite múltiplos registros por usuário (multi-join)
  - Consolidado: `meet_attendances` garante 1 por user/sala

### 6.2 Presença
- Presença mínima por:
  - minutos (min_presence_minutes)
  - percentual (min_presence_percent)
- Cálculo a partir de `meet_room_participants`

### 6.3 Gravação
- Uma sala pode ter 0..N gravações
- Estados controlados por `status`

---

## 7) Extensões Futuras (Fase 2/3)

### 7.1 Multi-tenant / White-label
Adicionar `tenant_id` em:
- meet_rooms, meet_recordings, meet_events, meet_access_tokens, meet_attendances

### 7.2 Transcrição/IA
- `meet_transcripts`
  - room_id, provider, text, segments(json), language, status

### 7.3 Conteúdo pós-aula
- `meet_resources`
  - room_id, type(material|link|quiz), title, url, metadata

---

## 8) Sugestão de Nomes de Migrations (Laravel)

1. `create_meet_providers_table`
2. `create_meet_rooms_table`
3. `create_meet_room_settings_table`
4. `create_meet_room_participants_table`
5. `create_meet_recordings_table`
6. `create_meet_events_table`
7. `create_meet_access_tokens_table` (opcional)
8. `create_meet_attendances_table` (fase 2)

---

## 9) MVP Recomendada (mínimo para rodar)

- ✅ meet_providers
- ✅ meet_rooms
- ✅ meet_room_participants
- ✅ meet_recordings
- ✅ meet_events
- (Opcional) meet_room_settings

---

Se quiser, eu posso gerar as **migrations Laravel 12** dessas tabelas (com índices e FKs) e um **Seeder** criando o provider `jitsi`.
