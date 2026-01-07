# MeetFA - Documentação Técnica

**Versão:** 1.0 MVP  
**Data:** 07/01/2026  
**Projeto:** Sistema de Salas de Aula Virtuais para Faculdade Anasps

---

## 1. Visão Geral

O **MeetFA** é um módulo de videoconferência integrado ao sistema acadêmico da Faculdade Anasps. Permite que professores criem e gerenciem salas de aula virtuais, com controle de presença, gravações e chat acadêmico.

### 1.1 Tecnologias Utilizadas

| Tecnologia | Versão | Finalidade |
|------------|--------|------------|
| Laravel | 11.x | Framework Backend |
| PHP | 8.2+ | Linguagem |
| MySQL | 8.0+ | Banco de Dados |
| Jitsi Meet | Público | Videoconferência |
| Tailwind CSS | 3.x (CDN) | Estilização |
| Alpine.js | 3.x | Interatividade Frontend |
| Laravel Breeze | 2.x | Autenticação |

---

## 2. Arquitetura

### 2.1 Estrutura de Diretórios

```
app/
├── Domain/
│   └── MeetFA/
│       ├── Actions/           # Ações de negócio
│       │   ├── CreateRoomAction.php
│       │   ├── StartRoomAction.php
│       │   ├── EndRoomAction.php
│       │   └── RegisterMeetEventAction.php
│       ├── Http/
│       │   └── Controllers/
│       │       ├── MeetRoomController.php
│       │       ├── MeetChatController.php
│       │       ├── MeetEventController.php
│       │       └── MeetWebhookController.php
│       ├── Models/            # Eloquent Models
│       │   ├── MeetProvider.php
│       │   ├── MeetRoom.php
│       │   ├── MeetRoomSetting.php
│       │   ├── MeetRoomParticipant.php
│       │   ├── MeetRecording.php
│       │   ├── MeetEvent.php
│       │   └── MeetChatMessage.php
│       ├── Policies/
│       │   └── RoomPolicy.php
│       ├── Providers/
│       │   ├── VideoProviderContract.php
│       │   └── JitsiProvider.php
│       └── Services/
│           ├── JoinGuardService.php
│           └── AiTutorService.php
```

### 2.2 Banco de Dados

| Tabela | Descrição |
|--------|-----------|
| `meet_providers` | Provedores de vídeo (Jitsi, etc.) |
| `meet_rooms` | Salas de aula virtuais |
| `meet_room_settings` | Configurações de cada sala |
| `meet_room_participants` | Participantes e presenças |
| `meet_recordings` | Gravações das aulas |
| `meet_events` | Log de eventos (join, leave, etc.) |
| `meet_chat_messages` | Mensagens do chat acadêmico |

---

## 3. Rotas

### 3.1 Rotas Web (Autenticadas)

| Método | URI | Controller | Descrição |
|--------|-----|------------|-----------|
| GET | `/meet` | `MeetRoomController@index` | Lista de salas |
| GET | `/meet/create` | `MeetRoomController@create` | Formulário de criação |
| POST | `/meet` | `MeetRoomController@store` | Criar sala |
| GET | `/meet/{uuid}` | `MeetRoomController@show` | Visualizar/Entrar na sala |
| GET | `/meet/{uuid}/report` | `MeetRoomController@report` | Relatório da sala |
| POST | `/meet/{uuid}/start` | `MeetRoomController@start` | Iniciar aula (AJAX) |
| POST | `/meet/{uuid}/end` | `MeetRoomController@end` | Encerrar aula (AJAX) |
| GET | `/meet/{uuid}/chat` | `MeetChatController@index` | Listar mensagens |
| POST | `/meet/{uuid}/chat` | `MeetChatController@store` | Enviar mensagem |

### 3.2 Rotas API

| Método | URI | Controller | Descrição |
|--------|-----|------------|-----------|
| POST | `/api/meet/{uuid}/events` | `MeetEventController@store` | Registrar eventos |
| POST | `/api/webhooks/jitsi` | `MeetWebhookController@jitsi` | Webhook Jitsi |

---

## 4. Fluxos Principais

### 4.1 Fluxo do Professor

1. Professor faz login → Redirecionado para `/meet`
2. Clica em "+ Nova Sala" → Preenche formulário
3. Sala criada com status `scheduled`
4. Professor acessa a sala e clica em "Iniciar Aula"
5. Status muda para `live`, Jitsi é carregado
6. Ao finalizar, clica em "Encerrar Aula"
7. Status muda para `ended`, relatório disponível

### 4.2 Fluxo do Aluno

1. Aluno faz login → Redirecionado para `/meet`
2. Visualiza lista de salas disponíveis
3. Clica na sala desejada
4. Se sala `scheduled`: Vê overlay "Aguardando Professor"
5. Se sala `live`: Entra no Jitsi + Chat lateral
6. Pode usar chat para tirar dúvidas com @tutor

---

## 5. Funcionalidades Implementadas

### 5.1 MVP (Fase 1) ✅

- [x] Autenticação com Laravel Breeze
- [x] CRUD de Salas de Aula
- [x] Integração com Jitsi Meet
- [x] Controle de início/fim de aula
- [x] Relatório de participação
- [x] Listagem de gravações
- [x] Botão "Copiar Link" para compartilhar
- [x] Overlay "Aguardando Professor" para alunos
- [x] Identidade visual (Logo da Faculdade)

### 5.2 Fase 2 (Parcialmente Implementado)

- [x] Chat acadêmico lateral
- [x] Agente IA (Tutor Virtual - mock)
- [ ] Integração Google Drive para gravações
- [ ] Background virtual padronizado

---

## 6. Configuração do Ambiente

### 6.1 Variáveis de Ambiente

```env
# Banco de Dados
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=meetfa
DB_USERNAME=root
DB_PASSWORD=

# Aplicação
APP_URL=http://127.0.0.1:8000
```

### 6.2 Comandos de Setup

```bash
# Instalar dependências
composer install
npm install

# Configurar banco
php artisan migrate

# Seed do provedor Jitsi
php artisan db:seed --class=MeetProviderSeeder

# Iniciar servidor
php artisan serve
```

---

## 7. Usuários de Teste

| Email | Senha | Perfil |
|-------|-------|--------|
| `professor@meetfa.test` | `senha123` | Professor |
| `aluno@meetfa.test` | `senha123` | Aluno |

---

## 8. Próximos Passos

1. **Google Drive Integration**: Upload automático de gravações
2. **Refinamento do AI Tutor**: Integrar com OpenAI/Gemini
3. **Controle de Presença Avançado**: Validar tempo mínimo
4. **Integração Acadêmica**: Vincular com cursos e disciplinas
5. **Servidor Jitsi Próprio**: Para controle total do lobby

---

*Documentação gerada em 07/01/2026 às 00:13*
