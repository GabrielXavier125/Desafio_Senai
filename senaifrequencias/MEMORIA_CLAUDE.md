# SENAI Frequências — Memória de Desenvolvimento para Claude

> **Para quem é este documento?**
> Este documento existe para que qualquer sessão futura do Claude Code tenha contexto completo do projeto, do que foi feito, das decisões tomadas e do estado atual — sem precisar re-explorar o código do zero.

---

## Sumário

1. [Identidade do projeto](#1-identidade-do-projeto)
2. [Stack técnica](#2-stack-técnica)
3. [Estado atual do sistema](#3-estado-atual-do-sistema)
4. [Todas as funcionalidades implementadas](#4-todas-as-funcionalidades-implementadas)
5. [Bugs encontrados e resolvidos](#5-bugs-encontrados-e-resolvidos)
6. [Decisões técnicas e seus motivos](#6-decisões-técnicas-e-seus-motivos)
7. [Arquivos críticos e o que cada um faz](#7-arquivos-críticos-e-o-que-cada-um-faz)
8. [Credenciais e configuração](#8-credenciais-e-configuração)
9. [Padrões e convenções do projeto](#9-padrões-e-convenções-do-projeto)
10. [O que ainda pode ser desenvolvido](#10-o-que-ainda-pode-ser-desenvolvido)
11. [Histórico cronológico de desenvolvimento](#11-histórico-cronológico-de-desenvolvimento)

---

## 1. Identidade do projeto

- **Nome:** SENAI Frequências
- **Tipo:** Sistema web de controle de frequência escolar
- **Contexto:** Projeto desenvolvido por um estudante de programação, para apresentação em sala de aula, com base em Laravel
- **Ambiente:** Laragon (Windows), servidor local em `c:\laragon\www\senaifrequencias`
- **Não é um repositório git** (sem versionamento)

---

## 2. Stack técnica

- **PHP 8.3** + **Laravel 13.8**
- **MySQL 8.4** (Laragon)
- **Livewire 4.3** (componentes reativos server-side)
- **Alpine.js** — versão embutida no Livewire (NÃO instalado via npm)
- **Tailwind CSS v4** (com `@theme { --color-senai: #c8161d }`)
- **Vite 8** (bundler)
- **Windows 11 / Laragon**

---

## 3. Estado atual do sistema

O sistema está **funcional e completo** para o fluxo principal. Todas as áreas (admin, professor, empresa) estão implementadas e funcionando.

### Funcionalidades prontas
- [x] Autenticação completa (login, logout, recuperar senha, alterar senha)
- [x] Middleware de role (admin / professor / empresa)
- [x] Dashboard admin com estatísticas
- [x] CRUD completo: Turmas, Alunos, Professores, Empresas, Aulas
- [x] Chamada de presença com toggle e UI otimista
- [x] Vínculo professor ↔ turma (gerenciado pelo admin)
- [x] Vínculo empresa ↔ alunos (many-to-many)
- [x] Dashboard professor com estatísticas de frequência
- [x] Dashboard empresa com busca/filtro Alpine client-side
- [x] Histórico de presença por aluno (visão empresa)
- [x] Exportação CSV (admin e professor)
- [x] Layout responsivo (mobile sidebar drawer, tabelas com scroll horizontal)
- [x] Telas de recuperar/redefinir senha redesenhadas (mesma identidade visual do login)
- [x] Modal de confirmação global (Alpine.store)
- [x] Alterar senha (todos os perfis)
- [x] Animação de transição do login (fetch + overlay + card fly-up + dashboard fade-in)
- [x] Comentários em português em todos os arquivos principais do projeto

---

## 4. Todas as funcionalidades implementadas

### Área do Administrador

#### Dashboard
- Cartões com totais: turmas, alunos ativos, professores ativos, empresas

#### Gerenciar Turmas (`/admin/turmas`)
- Listagem paginada (10/página) com busca por nome e curso
- Modal criar/editar: nome, curso, ano
- Soft delete
- Botão "Ver Aulas" → `/admin/turmas/{id}` → lista aulas da turma + exportar CSV

#### Gerenciar Alunos (`/admin/alunos`)
- Listagem paginada com busca por nome/RA e filtro por turma
- Modal criar/editar: nome, RA, turma
- Toggle ativar/desativar (aluno inativo não aparece na chamada)
- Soft delete

#### Gerenciar Professores (`/admin/professores`)
- Listagem paginada com busca
- Tabela mostra coluna "Turma" com badge azul (ou "—" se sem turma)
- Modal criar/editar: nome, email, senha, turma vinculada
- Dropdown de turmas: turmas já ocupadas (com outro professor) aparecem `disabled` com "(ocupada)"
- Ao salvar: desvincula turma anterior do professor, vincula nova turma
- Toggle ativar/desativar
- Soft delete

#### Gerenciar Empresas (`/admin/empresas`)
- Listagem paginada com busca
- Modal criar: nome, CNPJ, email e senha do login (cria User + Empresa)
- Modal editar: nome, CNPJ (sem alterar email/senha)
- Excluir empresa + excluir user vinculado
- Botão "Gerenciar Alunos" → `/admin/empresas/{id}` → VincularAlunosEmpresa

#### Vincular Alunos à Empresa
- Lista todos os alunos ativos com busca em tempo real
- Toggle para vincular/desvincular (tabela pivot `empresa_aluno`)
- Badge verde "Vinculado" quando vinculado

#### Aulas de uma Turma
- Listagem de aulas com data e descrição
- Modal criar/editar aula
- Excluir aula (cascata exclui chamadas)
- Exportar CSV da turma

### Área do Professor

#### Dashboard (`/professor/dashboard`)
- Carrega turma vinculada: `auth()->user()->turmas()->first()`
- Estatísticas: total alunos, total aulas, % presença média
- Lista de aulas com data, descrição, total presentes
- Botão "Fazer Chamada" por aula
- Botão "Exportar CSV"
- Estado vazio se professor não tiver turma vinculada

#### Chamada (`/professor/aulas/{aula}`)
- Lista alunos ativos da turma com status atual
- Toggle liga/desliga por aluno (Presente/Falta)
- **UI otimista**: Alpine muda estado visualmente imediato; `$wire.toggle()` salva em background
- `updateOrCreate` garante idempotência
- Voltar para dashboard

### Área da Empresa

#### Dashboard (`/empresa/dashboard`)
- Busca por nome/RA e filtro por turma — **tudo client-side com Alpine.js**
- Cards de alunos com nome, RA, turma, percentual de presença
- Barra de progresso colorida por percentual (verde ≥75%, amarelo ≥50%, vermelho <50%)
- Link para histórico de cada aluno

#### Histórico do Aluno (`/empresa/alunos/{aluno}/historico`)
- Valida que o aluno pertence à empresa autenticada
- Tabela com todas as aulas e status (Presente/Falta)
- Resumo: total de aulas, presenças, percentual

#### Consulta de Aulas (`/empresa/aulas`)
- Lista aulas de todas as turmas dos alunos vinculados

---

## 5. Bugs encontrados e resolvidos

### Bug 1: Toggle da chamada não funcionava
**Sintoma:** Clicar no toggle não fazia nada ou dava erro no console.
**Causa raiz:** Dois instâncias do Alpine.js em conflito:
- npm Alpine (import em `app.js`) iniciava como ES module deferido
- Livewire 4 registra `window.Alpine` e inicia Alpine internamente
- O npm Alpine sobrescrevia o `window.Alpine` do Livewire **depois** dele já ter iniciado, quebrando a integração `$wire`

**Solução:** Remover completamente `import Alpine from 'alpinejs'` e `Alpine.start()` do `app.js`. Usar o evento `document.addEventListener('alpine:init', ...)` para registrar stores.

**Arquivo:** `resources/js/app.js`

---

### Bug 2: Delay visual no toggle da chamada
**Sintoma:** Toggle funcionava mas havia delay perceptível (esperar resposta AJAX para mudar cor).
**Causa:** Alpine aguardava o Livewire para atualizar o estado.
**Solução:** UI otimista — Alpine mantém estado local `{ presente: bool }` e atualiza imediatamente no click. `$wire.toggle()` salva em segundo plano sem bloquear a UI.

---

### Bug 3: Classe `xs:` do Tailwind não funcionava
**Sintoma:** `hidden xs:inline-block` não respondia.
**Causa:** Tailwind v4 não tem breakpoint `xs:` por padrão.
**Solução:** Removido, usada outra abordagem de layout.

---

### Bug 4: Edit tool "File has not been read"
**Sintoma:** Tentativa de editar arquivo que estava apenas no resumo de contexto, não lido na sessão atual.
**Solução:** Sempre ler arquivo com `Read` antes de editar em nova sessão.

---

## 6. Decisões técnicas e seus motivos

### Alpine.js via Livewire bundle (não npm)
**Por quê:** Livewire 4 integra Alpine.js internamente. Instalar via npm cria conflito de instâncias que quebra `$wire`, `$dispatch`, etc. A única forma correta é deixar o Livewire gerenciar o Alpine.

**Como registrar código Alpine customizado:**
```js
document.addEventListener('alpine:init', () => {
    Alpine.store('nomeStore', { ... });
    Alpine.directive('nome', ...);
});
```

### `updateOrCreate` na chamada
**Por quê:** A tabela `chamadas` tem `UNIQUE(aula_id, aluno_id)`. Sem `updateOrCreate`, um segundo clique tentaria inserir duplicata e daria erro de constraint. Com `updateOrCreate`, é idempotente.

### Soft Deletes em User, Turma, Aluno
**Por quê:** Preservar integridade histórica. Se um aluno fosse hard-deleted, os registros de `chamadas` ficariam sem referência (ou seriam excluídos por cascade, perdendo histórico). Com soft delete, o aluno fica "oculto" mas os dados de frequência persistem.

**Empresa não usa soft delete:** Empresa pode ser excluída por completo sem perder histórico relevante (o histórico de chamadas fica no aluno, não na empresa).

### Dashboard da empresa: Alpine client-side em vez de Livewire
**Por quê:** O conjunto de dados (alunos da empresa) é relativamente pequeno e já carregado de uma vez. Filtrar client-side com Alpine é mais rápido (sem round-trip ao servidor) e simples de implementar.

### `Js::from()` para dados Alpine
**Por quê:** Injetar arrays PHP em Alpine via `{{ json_encode() }}` é inseguro (XSS se o dado contiver `</script>`, aspas mal-escapadas, etc.). `Js::from()` do Laravel escapa corretamente para uso em contexto JavaScript.

### Layout senai.blade.php: sidebar mobile drawer
**Padrão usado:**
- Sidebar: `class="fixed inset-y-0 left-0 z-50 ... lg:relative lg:z-auto lg:translate-x-0"`
- `:class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"`
- A classe estática `lg:translate-x-0` sempre sobrescreve o `:class` no desktop (Tailwind v4 cascade)
- Overlay `<div x-show="sidebarOpen" @click="sidebarOpen = false" class="... lg:hidden">`

### Tabelas com scroll horizontal mobile
**Padrão:** Wrapper com `overflow-x-auto` + `<table class="w-full min-w-[NNNpx]">`. O `overflow-x-auto` também cria um novo BFC que mantém o `border-radius` do container funcionando.

### Turma vinculada ao professor
**Fluxo:** Admin seleciona turma no modal do professor. Ao salvar:
1. Remove `professor_id` de todas as turmas que tinham este professor
2. Define `professor_id` na nova turma selecionada
Isso garante que um professor só pode ter uma turma por vez.

---

## 7. Arquivos críticos e o que cada um faz

| Arquivo | Responsabilidade |
|---|---|
| `resources/js/app.js` | Registra `Alpine.store('dialogo')` via `alpine:init`. Não importa Alpine nem o inicia. |
| `resources/css/app.css` | Define cor `--color-senai` para Tailwind |
| `resources/views/layouts/senai.blade.php` | Layout completo: sidebar, topbar, mobile drawer, modal de confirmação global |
| `app/Livewire/Professor/Chamada.php` | Toggle de presença com `updateOrCreate` |
| `resources/views/livewire/professor/chamada.blade.php` | UI otimista: Alpine local state + `$wire.toggle()` |
| `app/Livewire/Admin/GerenciarProfessores.php` | CRUD de professores + vínculo com turma |
| `resources/views/empresa/dashboard.blade.php` | Alpine client-side search/filter (usa `Js::from()`) |
| `resources/views/auth/forgot-password.blade.php` | HTML standalone (sem x-guest-layout), mesmo visual do login |
| `resources/views/auth/reset-password.blade.php` | HTML standalone, formulário de nova senha |
| `app/Http/Controllers/Empresa/DashboardController.php` | Carrega dados + calcula percentuais para a view empresa |

---

## 8. Credenciais e configuração

### Logins do sistema
| Perfil | E-mail | Senha |
|---|---|---|
| Administrador | admin@senai.br | 1234 |
| Professor | professor@senai.br | 1234 |

Professores e empresas adicionais são criados via painel admin.

### Banco de dados
- Host: `127.0.0.1`
- Porta: `3306`
- Database: `senaifrequencias` (ou o nome configurado no `.env`)
- User/Pass: padrão Laragon (`root` / sem senha)

### Variáveis `.env` relevantes
```
APP_NAME="SENAI Frequências"
APP_URL=http://senaifrequencias.test
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=senaifrequencias
MAIL_MAILER=log  (e-mails de recuperação vão para storage/logs)
```

---

## 9. Padrões e convenções do projeto

### Nomenclatura
- Arquivos Livewire PHP: PascalCase → `GerenciarProfessores.php`
- Arquivos Blade Livewire: kebab-case → `gerenciar-professores.blade.php`
- Rotas nomeadas: `admin.turmas.index`, `professor.dashboard`, `empresa.alunos.historico`
- Views: `admin/turmas/index.blade.php`, `livewire/admin/gerenciar-turmas.blade.php`

### Componentes Blade reutilizáveis
- `<x-sidebar-link href="..." active="...">Texto</x-sidebar-link>`
- `<x-stat-card titulo="..." valor="..." icone="..."/>`

### Mensagens flash
Sempre via `session()->flash('success', 'Mensagem.')`. A view verifica `session('success')` e exibe com auto-fade de 3s via Alpine `x-init="setTimeout(() => show = false, 3000)"`.

### Modal de confirmação global
Nunca usar `confirm()` nativo do browser. Sempre usar:
```js
@click="$store.dialogo.perguntar('Título', 'Mensagem', 'Texto botão', () => $wire.metodo(id))"
```

### Validação
Regras inline no método `save()` (não via `#[Validate]` para email pois precisa do unique dinâmico):
```php
$rules = [
    'email' => 'required|email|unique:users,email' . ($this->editingId ? ",{$this->editingId}" : ''),
];
```

---

## 10. O que ainda pode ser desenvolvido

Funcionalidades não implementadas que podem ser adicionadas:

- **Notificações**: alerta ao admin quando professor não fez chamada em X dias
- **Dashboard com gráficos**: Chart.js para visualizar frequência ao longo do tempo
- **Relatório PDF**: exportar chamada em PDF além de CSV
- **Importação em massa**: importar alunos via CSV
- **Múltiplas turmas por professor**: atualmente 1:1; poderia ser N:N
- **Chamada retroativa**: professor poder registrar chamada de datas passadas
- **Portal do aluno**: aluno ter login para ver sua própria frequência
- **Envio de e-mail real**: configurar SMTP para o "Esqueci a senha" funcionar em produção
- **Log de auditoria**: registrar quem fez o quê e quando

---

## 11. Histórico cronológico de desenvolvimento

### Fase 1: Base inicial
- Projeto iniciado com **Laravel Breeze** como scaffolding de autenticação
- Configuração do banco de dados e criação das migrações
- Criação dos Models com relacionamentos
- Implementação do middleware de roles

### Fase 2: CRUD principal
- Dashboard do admin com estatísticas
- Livewire CRUD: Turmas, Alunos, Professores, Empresas
- Tela de aulas por turma (AulasTurma)
- Vínculo aluno ↔ empresa (VincularAlunosEmpresa)

### Fase 3: Fluxo de chamada
- TurmaDashboard do professor
- Chamada com toggle
- **Bug crítico:** Alpine dual instance — resolvido removendo npm Alpine
- **Melhoria:** UI otimista para o toggle de chamada

### Fase 4: Dashboard da empresa
- Busca e filtro client-side com Alpine.js
- Uso de `Js::from()` para injetar dados PHP com segurança
- Histórico de presença por aluno

### Fase 5: Polimento mobile
- Sidebar drawer mobile (hamburger + overlay + slide)
- Tabelas com `overflow-x-auto` + min-width
- Ajustes responsivos no dashboard do professor
- Botões adaptados para mobile (texto abreviado)

### Fase 6: UX e design
- Redesign completo das telas de "Esqueci a senha" e "Redefinir senha"
- Mesmo design do login (fundo vermelho degradê, card centralizado, logo SENAI)
- Modal de confirmação global (Alpine.store) para substituir `confirm()` nativo

### Fase 7: Exportação
- ExportController com exportação CSV para admin (por turma) e professor (sua turma)

### Fase 8: Vínculo professor ↔ turma
- Adicionado `turmaId` no GerenciarProfessores
- Tabela de professores mostra turma vinculada com badge
- Dropdown de turma no modal (turmas ocupadas desabilitadas)
- Lógica de desvincular/vincular ao salvar

### Fase 9: Documentação
- `DOCUMENTACAO_APRESENTACAO.md` — para apresentação em sala (estilo beginner, 15 seções incluindo animação)
- `DOCUMENTACAO_TECNICA.md` — referência técnica completa (14 seções incluindo animação)
- `MEMORIA_CLAUDE.md` — este arquivo (contexto para Claude Code)

### Fase 10: Comentários no código
- Todos os Models comentados linha por linha em português (`User`, `Aluno`, `Turma`, `Aula`, `Chamada`, `Empresa`)
- Todas as migrations comentadas com SQL equivalente e motivo de cada escolha
- Todos os Controllers comentados (Admin, Professor, Empresa, ExportController)
- Todos os Livewire components comentados (Chamada, TurmaDashboard, GerenciarAlunos, GerenciarTurmas, GerenciarProfessores)
- `app.css` e `app.js` comentados com explicação de cada regra/função
- `routes/web.php` comentado com o que cada rota faz e gera
- View `chamada.blade.php` comentada com explicação do `wire:key`, `x-data`, UI otimista

### Fase 11: Animação de transição do login
- `resources/views/auth/login.blade.php`: intercepção do submit via `fetch()`, overlay `#F0F2F5`, animação de saída
- `resources/views/layouts/senai.blade.php`: `pageFadeIn` no `<body>`
- Detecção de sucesso/falha por URL: se `response.url` contém `/login` → falha; caso contrário → sucesso
- Card sobe com `translateY(-110vh)` + `opacity:0` em 500ms; overlay em 550ms; navega após 650ms
- Erros de rede fazem fallback para `form.submit()` (comportamento padrão)
