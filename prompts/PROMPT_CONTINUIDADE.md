# Prompt de Continuidade - MeetFA

**Projeto:** MeetFA - Sistema de Salas de Aula Virtuais  
**Cliente:** Faculdade Anasps  
**Última atualização:** 07/01/2026

---

## 🎯 Objetivo do Projeto

Desenvolver um sistema de videoconferência integrado ao ambiente acadêmico da Faculdade Anasps, permitindo aulas ao vivo com controle de presença, chat acadêmico, agente de IA para suporte aos alunos, e integração com Google Drive para armazenamento de gravações.

---

## ✅ O QUE JÁ FOI IMPLEMENTADO (MVP + Fase 2 Parcial)

### Infraestrutura
- Laravel 11 com Laravel Breeze (autenticação)
- Arquitetura Domain-Driven Light em `app/Domain/MeetFA`
- 7 tabelas no banco: `meet_providers`, `meet_rooms`, `meet_room_settings`, `meet_room_participants`, `meet_recordings`, `meet_events`, `meet_chat_messages`

### Funcionalidades Completas
1. **Autenticação**: Login/Registro com redirecionamento para `/meet`
2. **CRUD de Salas**: Criar, listar, visualizar salas de aula
3. **Jitsi Integration**: Embed do Jitsi Meet com configurações customizadas
4. **Controle de Aula**: Botões "Iniciar Aula" e "Encerrar Aula" para professores
5. **Compartilhamento**: Botão "Copiar Link" para compartilhar sala
6. **Relatórios**: Página de relatório com participantes e gravações
7. **Identidade Visual**: Logo da Faculdade Anasps no header e login
8. **Waiting Room UX**: Overlay "Aguardando Professor" para alunos (com auto-refresh)
9. **Chat Acadêmico**: Chat lateral em tempo real (polling 3s)
10. **Tutor Virtual (Mock)**: IA responde a palavras-chave básicas (@tutor)

### Usuários de Teste
- `professor@meetfa.test` / `senha123`
- `aluno@meetfa.test` / `senha123`

---

## ⏳ O QUE FALTA IMPLEMENTAR (Fase 2 Pendente)

### Prioridade Alta
1. **Google Drive Integration**
   - Configurar Service Account do Google
   - Criar Job `UploadRecordingToDrive`
   - Salvar link do Drive no banco após upload

2. **Background Virtual Padronizado**
   - Configurar Jitsi para sugerir/forçar background com marca da faculdade

### Prioridade Média
3. **Tutor Virtual Avançado**
   - Integrar com OpenAI/Gemini API
   - Permitir respostas contextuais sobre disciplinas

4. **Controle de Presença Avançado**
   - Validar tempo mínimo de permanência
   - Gerar relatório de presença para integração acadêmica

### Prioridade Baixa
5. **Servidor Jitsi Próprio**
   - Para controle total do lobby e gravações

---

## 📁 Arquivos Principais

```
app/Domain/MeetFA/
├── Actions/
│   ├── CreateRoomAction.php      # Cria sala
│   ├── StartRoomAction.php       # Inicia aula
│   ├── EndRoomAction.php         # Encerra aula
│   └── RegisterMeetEventAction.php
├── Http/Controllers/
│   ├── MeetRoomController.php    # Principal
│   ├── MeetChatController.php    # Chat
│   ├── MeetEventController.php   # Eventos API
│   └── MeetWebhookController.php # Webhooks
├── Models/
│   ├── MeetRoom.php              # Sala (principal)
│   ├── MeetChatMessage.php       # Chat
│   └── ...outros modelos
├── Providers/
│   └── JitsiProvider.php         # Config Jitsi
└── Services/
    ├── JoinGuardService.php      # Controle de acesso
    └── AiTutorService.php        # Tutor IA (mock)

resources/views/meetfa/
├── index.blade.php               # Lista de salas
├── room.blade.php                # Sala + Jitsi + Chat
├── create.blade.php              # Formulário criar sala
└── report.blade.php              # Relatório

routes/
├── web.php                       # Rotas autenticadas
└── api.php                       # Rotas API
```

---

## 🚀 Comandos para Iniciar

```bash
# Navegar até o projeto
cd e:\xampp\htdocs\Meetfa

# Limpar cache
php artisan optimize:clear

# Iniciar servidor
php artisan serve

# Acessar
http://127.0.0.1:8000/meet
```

---

## 🐛 Problemas Conhecidos

1. **Jitsi Público (meet.jit.si)**
   - Exige moderador para liberar entrada
   - Solução: Professor deve clicar "Iniciar Aula" primeiro
   - Solução definitiva: Usar servidor Jitsi próprio

2. **Chat Polling**
   - Usa polling (3s) ao invés de WebSocket
   - Para produção: Considerar Laravel Reverb/Pusher

---

## 📋 Para Continuar o Desenvolvimento

Quando retomar o projeto, use este prompt:

```
Estou continuando o desenvolvimento do MeetFA - Sistema de Salas de Aula Virtuais da Faculdade Anasps.

O projeto está em: e:\xampp\htdocs\Meetfa

Já implementamos:
- Autenticação com Laravel Breeze
- CRUD de salas com Jitsi Meet
- Chat acadêmico com Tutor IA (mock)
- Identidade visual da faculdade
- Relatórios e gravações

Próximos passos prioritários:
1. Integração com Google Drive para gravações
2. Tutor IA com OpenAI/Gemini
3. Background virtual padronizado

Leia os arquivos em docs/DOCUMENTACAO_TECNICA.md e prompts/ para contexto completo.
```

---

*Prompt de continuidade gerado em 07/01/2026*
