# 📘 PRD – MeetFA
### Plataforma Institucional de Aulas ao Vivo da Faculdade Anasps

---

## 1. Visão Geral do Produto

**MeetFA** é uma plataforma própria de aulas ao vivo, integrada ao ecossistema acadêmico da Faculdade Anasps (SGA, Moodle, CFA-FLIX), criada para garantir **controle acadêmico, presença automática, evidências educacionais e identidade institucional**, utilizando **tecnologias open-source e gratuitas**.

O MeetFA não concorre com Google Meet ou Zoom como ferramenta corporativa, mas se posiciona como uma **plataforma educacional oficial**, focada em governança acadêmica, compliance e integração sistêmica.

---

## 2. Objetivos do Produto

### 2.1 Objetivo Principal
Centralizar e oficializar todas as aulas síncronas da instituição, garantindo:
- Controle de acesso por matrícula
- Registro automático de presença
- Gravação vinculada à disciplina
- Evidências acadêmicas confiáveis (auditoria/MEC)

### 2.2 Objetivos Secundários
- Reduzir dependência de ferramentas externas
- Padronizar aulas ao vivo
- Fortalecer identidade institucional
- Criar base para futura plataforma white-label

---

## 3. Público-Alvo

- 🎓 Alunos (graduação, pós-graduação e cursos livres)
- 👨‍🏫 Professores
- 🏫 Coordenação Acadêmica
- 🧑‍💼 Secretaria e Gestão Educacional
- 🖥 TI e Auditoria

---

## 4. Problemas Atuais

| Problema | Impacto |
|--------|--------|
| Links externos (Meet/Zoom) | Falta de controle institucional |
| Presença manual | Erros, retrabalho e fraudes |
| Aluno inadimplente em aula | Quebra de regra acadêmica |
| Gravações dispersas | Dificuldade de acesso e rastreio |
| Falta de evidência | Risco regulatório |
| Ferramenta genérica | Pouca identidade institucional |

---

## 5. Proposta de Valor do MeetFA

- Sala de aula vinculada à disciplina
- Acesso apenas para alunos autorizados
- Presença automática baseada em tempo mínimo
- Gravação institucional centralizada
- Relatórios acadêmicos completos
- Interface com identidade visual da Faculdade

---

## 6. Escopo do MVP (Fase 1)

### 6.1 Autenticação e Acesso
- Login único (SSO com SGA)
- Validações automáticas:
  - matrícula ativa
  - turma correta
  - situação financeira regular
- Links temporários e assinados

---

### 6.2 Sala de Aula Virtual
- Uma sala por:
  - disciplina
  - turma
  - professor
- Abertura automática conforme calendário acadêmico
- Encerramento automático da sessão

---

### 6.3 Presença Automática
- Registro de:
  - horário de entrada
  - horário de saída
  - tempo total conectado
- Regra configurável de presença mínima (ex: 75%)
- Exportação automática para o SGA

---

### 6.4 Gravação de Aula
- Gravação automática via motor de vídeo
- Armazenamento institucional
- Vinculação com:
  - disciplina
  - professor
  - data da aula
- Integração com CFA-FLIX

---

### 6.5 Relatórios
- Lista de presença por aula
- Histórico de participação do aluno
- Logs de acesso (IP, horário, duração)
- Exportação em PDF e CSV

---

## 7. Requisitos Não Funcionais

- Compatível com navegadores modernos
- Acesso via desktop e mobile
- Escalabilidade horizontal
- Alta disponibilidade em horários de aula
- Conformidade com LGPD
- Logs auditáveis

---

## 8. Arquitetura Técnica (100% Gratuita)

### 8.1 Backend
- PHP 8.2+
- Laravel 12
- Banco MySQL ou PostgreSQL

### 8.2 Frontend
- Blade
- Alpine.js
- Tailwind CSS

### 8.3 Vídeo (Open Source)
- Jitsi Meet (self-hosted)
- WebRTC no navegador
- Gravação via Jibri

### 8.4 Armazenamento
- Servidor próprio
- ou S3 compatível (Cloudflare R2 / MinIO)

---

## 9. Roadmap do Produto

### 🚀 Fase 1 – MVP Institucional (0–3 meses)
**Objetivo:** substituir ferramentas externas e organizar aulas ao vivo

Entregas:
- Sala por disciplina
- Integração com SGA/Moodle
- Presença automática
- Gravação básica
- Relatórios essenciais

---

### ⚙️ Fase 2 – Inteligência Acadêmica (4–8 meses)
**Objetivo:** ampliar controle e valor educacional

Entregas:
- Regras avançadas de presença
- Indicadores de engajamento
- Relatórios MEC-friendly
- Integração profunda com CFA-FLIX
- Histórico acadêmico do aluno

---

### 🌐 Fase 3 – Plataforma Estratégica (9–18 meses)
**Objetivo:** transformar o MeetFA em ativo institucional

Entregas:
- Multi-instituição (white-label)
- Salas híbridas (presencial + online)
- Recursos de IA (transcrição, resumo)
- Ecossistema de plugins educacionais

---

## 10. Métricas de Sucesso

- % de aulas realizadas via MeetFA
- Redução de retrabalho de presença
- Tempo médio de permanência do aluno
- Uso das gravações
- Satisfação docente
- Conformidade em auditorias

---

## 11. Riscos e Mitigações

| Risco | Mitigação |
|-----|----------|
| Pico de acessos simultâneos | Planejamento de VPS |
| Internet instável do aluno | WebRTC adaptativo |
| Resistência docente | Treinamento e onboarding |
| Gravações grandes | Storage escalável |

---

## 12. Considerações Finais

O MeetFA é um projeto estratégico que fortalece a autonomia tecnológica da Faculdade Anasps, melhora a governança acadêmica e cria uma base sólida para inovação educacional, sem dependência de soluções pagas ou proprietárias.

---
