# SENAI Frequências — Documentação Técnica do Projeto

> **Sistema de Controle de Frequência Escolar**
> Escola SENAI Luiz Varga

---

## Sumário

1. [Visão Geral do Projeto](#1-visão-geral-do-projeto)
2. [Objetivo](#2-objetivo)
3. [Stack Tecnológica](#3-stack-tecnológica)
4. [Estrutura de Usuários e Perfis](#4-estrutura-de-usuários-e-perfis)
5. [Banco de Dados](#5-banco-de-dados)
6. [Arquitetura da Aplicação](#6-arquitetura-da-aplicação)
7. [Funcionalidades por Módulo](#7-funcionalidades-por-módulo)
8. [Fluxos Principais](#8-fluxos-principais)
9. [Interface e Identidade Visual](#9-interface-e-identidade-visual)
10. [Decisões Técnicas Relevantes](#10-decisões-técnicas-relevantes)
11. [Credenciais Padrão](#11-credenciais-padrão)

---

## 1. Visão Geral do Projeto

O **SENAI Frequências** é uma aplicação web desenvolvida para digitalizar e centralizar o controle de presença dos alunos da Escola SENAI Luiz Varga. O sistema substitui as tradicionais listas de chamada em papel, permitindo que professores registrem presenças em tempo real diretamente no navegador — incluindo pelo celular durante a aula.

Empresas parceiras que possuem alunos em regime de aprendizagem têm acesso somente-leitura para acompanhar a frequência de seus aprendizes, sem precisar contatar a escola individualmente.

O projeto foi desenvolvido a partir de uma base Laravel gerada pelo **Laravel Breeze** (autenticação), sendo toda a lógica de negócio, interface e banco de dados construídos do zero ao longo do desenvolvimento.

---

## 2. Objetivo

| Problema | Solução |
|---|---|
| Chamadas feitas em papel, sujeitas a perda | Registro digital persistente no banco de dados |
| Sem visibilidade consolidada de frequência | Dashboard com estatísticas em tempo real |
| Empresas precisam ligar para saber de faltas | Portal próprio com acesso controlado |
| Sem histórico acessível por aula | Cada aula tem seu registro individual de chamada |

---

## 3. Stack Tecnológica

| Camada | Tecnologia | Versão |
|---|---|---|
| **Framework backend** | Laravel | 13.8 |
| **Linguagem** | PHP | 8.3 |
| **Banco de dados** | MySQL | 8.4 |
| **Componentes reativos** | Livewire | 4.3 |
| **Reatividade frontend** | Alpine.js | v3 (bundled no Livewire) |
| **CSS / Estilização** | Tailwind CSS | v4 |
| **Build de assets** | Vite | 8 |
| **Ambiente de desenvolvimento** | Laragon | — |
| **Fonte** | Inter (Google/Bunny Fonts) | — |

### Por que Livewire?

O Livewire permite construir interfaces reativas (como o toggle de chamada, buscas em tempo real e modais) sem precisar escrever uma API REST separada ou um frontend em Vue/React. O componente PHP processa a lógica no servidor e o Livewire sincroniza apenas o que mudou no DOM via AJAX, tornando o desenvolvimento mais rápido e o código mais coeso.

---

## 4. Estrutura de Usuários e Perfis

O sistema possui **três perfis de acesso** controlados pelo campo `role` na tabela `users`:

```
users.role = 'admin'     → Administrador / Diretor
users.role = 'professor' → Professor
users.role = 'empresa'   → Empresa parceira (somente leitura)
```

O redirecionamento após login é automático, com base no perfil:

```
admin     → /admin/dashboard
professor → /professor/dashboard
empresa   → /empresa/dashboard
```

O controle de acesso é feito por um **middleware customizado** (`role`) registrado no `bootstrap/app.php`, aplicado em todas as rotas dos grupos `admin`, `professor` e `empresa`. O registro de novos usuários pelo formulário público está desabilitado — toda criação de conta é feita pelo administrador.

---

## 5. Banco de Dados

### Diagrama de Relacionamentos

```
users (id, name, email, password, role, active, deleted_at)
  │
  ├── turmas (id, nome, curso, ano, professor_id → users)
  │     │
  │     └── alunos (id, nome, ra, turma_id → turmas, active, deleted_at)
  │           │
  │           ├── empresa_aluno (empresa_id, aluno_id)  ← pivot
  │           │
  │           └── chamadas (id, aula_id, aluno_id, status, updated_at)
  │
  ├── empresas (id, nome, cnpj, user_id → users)
  │     │
  │     └── empresa_aluno (empresa_id, aluno_id)  ← pivot
  │
  └── aulas (id, turma_id → turmas, data, descricao)
        │
        └── chamadas (id, aula_id, aluno_id, status, updated_at)
```

### Descrição das Tabelas

#### `users`
| Coluna | Tipo | Descrição |
|---|---|---|
| `id` | bigint PK | Identificador |
| `name` | string | Nome completo |
| `email` | string unique | E-mail de acesso |
| `password` | string (hashed) | Senha (bcrypt) |
| `role` | enum | `admin`, `professor`, `empresa` |
| `active` | boolean | Se pode fazer login |
| `deleted_at` | timestamp | Soft delete |

#### `turmas`
| Coluna | Tipo | Descrição |
|---|---|---|
| `id` | bigint PK | Identificador |
| `nome` | string | Nome da turma (ex: "Técnico em TI 2026") |
| `curso` | string | Nome do curso |
| `ano` | year | Ano letivo |
| `professor_id` | FK → users | Professor responsável (nullable) |

#### `alunos`
| Coluna | Tipo | Descrição |
|---|---|---|
| `id` | bigint PK | Identificador |
| `nome` | string | Nome completo do aluno |
| `ra` | string unique | Registro do Aluno |
| `turma_id` | FK → turmas | Turma matriculada |
| `active` | boolean | Se participa das chamadas |
| `deleted_at` | timestamp | Soft delete |

#### `empresas`
| Coluna | Tipo | Descrição |
|---|---|---|
| `id` | bigint PK | Identificador |
| `nome` | string | Razão social |
| `cnpj` | string unique | CNPJ formatado |
| `user_id` | FK → users | Conta de acesso vinculada |

#### `empresa_aluno` (pivot)
| Coluna | Tipo | Descrição |
|---|---|---|
| `empresa_id` | FK → empresas | Empresa |
| `aluno_id` | FK → alunos | Aluno vinculado |

Permite que uma empresa acompanhe múltiplos alunos e um aluno possa estar vinculado a mais de uma empresa.

#### `aulas`
| Coluna | Tipo | Descrição |
|---|---|---|
| `id` | bigint PK | Identificador |
| `turma_id` | FK → turmas | Turma da aula |
| `data` | date | Data da aula |
| `descricao` | text | Conteúdo ministrado |

#### `chamadas`
| Coluna | Tipo | Descrição |
|---|---|---|
| `id` | bigint PK | Identificador |
| `aula_id` | FK → aulas | Aula correspondente |
| `aluno_id` | FK → alunos | Aluno |
| `status` | enum | `presente` ou `falta` |
| `updated_at` | timestamp | Última atualização |

A combinação `(aula_id, aluno_id)` possui constraint `UNIQUE`, garantindo um único registro por aluno por aula.

### Soft Deletes

Os modelos `User`, `Turma` e `Aluno` utilizam **soft delete** (campo `deleted_at`). Isso significa que ao "excluir" um professor, turma ou aluno, o registro não é removido fisicamente do banco — apenas marcado como deletado. O histórico de chamadas é preservado.

A tabela `Empresa` **não utiliza** soft delete — a exclusão é física, removendo também o usuário de acesso vinculado.

---

## 6. Arquitetura da Aplicação

### Estrutura de Pastas Relevante

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/         → DashboardController, TurmasController, AlunosController,
│   │   │                     ProfessoresController, EmpresasController, ExportController
│   │   ├── Professor/     → DashboardController (turma + chamada)
│   │   └── Empresa/       → DashboardController (frequências + histórico)
│   └── Middleware/        → EnsureRole (controle por perfil)
├── Livewire/
│   ├── Admin/             → GerenciarTurmas, GerenciarAlunos, GerenciarProfessores,
│   │                         GerenciarEmpresas, AulasTurma, VincularAlunosEmpresa
│   └── Professor/         → TurmaDashboard, Chamada
└── Models/                → User, Turma, Aluno, Empresa, Aula, Chamada

resources/views/
├── layouts/senai.blade.php      → Layout principal (sidebar + topbar)
├── auth/                        → Login, forgot-password, reset-password
├── admin/                       → Pages que embarcam componentes Livewire
├── professor/                   → Dashboard e chamada do professor
├── empresa/                     → Dashboard, aulas e histórico da empresa
└── livewire/                    → Views dos componentes Livewire
```

### Padrão de Componentes Livewire

Os componentes Livewire são usados para todas as funcionalidades com interação em tempo real (sem recarregar a página):

- **CRUD com modal** — abrir/fechar modal, salvar, validar
- **Busca com debounce** — filtro de tabelas com `wire:model.live.debounce.300ms`
- **Toggle de status** — ativar/desativar com confirmação
- **Chamada** — toggle presente/falta com persistência imediata

Páginas estáticas (dashboards informativos, histórico) são renderizadas por controllers tradicionais e views Blade.

### Integração Alpine.js + Livewire

O Alpine.js (v3) é gerenciado pelo próprio Livewire v4, que o embute em seu bundle. Para funcionalidades puramente de UI (diálogo de confirmação global, sidebar mobile, filtros client-side), foi utilizado Alpine via:

- `Alpine.store('dialogo', {...})` — estado global do modal de confirmação
- `x-data="{ presente: ... }"` — estado local para UI otimista no toggle da chamada
- `x-data="{ sidebarOpen: false }"` — controle da sidebar em dispositivos móveis

---

## 7. Funcionalidades por Módulo

### 7.1 Autenticação

| Funcionalidade | Detalhe |
|---|---|
| Login | E-mail + senha, opção "Lembrar-me" |
| Logout | Encerra sessão e redireciona para login |
| Recuperação de senha | Envia link por e-mail (requer SMTP configurado) |
| Redefinição de senha | Token + nova senha via link recebido |
| Alteração de senha | Disponível para todos os perfis no menu lateral |
| Proteção de rotas | Middleware `auth` + middleware `role` por grupo |

### 7.2 Módulo Administrador

#### Dashboard
Painel inicial com visão geral: total de turmas, alunos, professores e empresas cadastradas.

#### Gerenciar Turmas
- Listagem paginada com busca por nome ou curso
- Cadastro e edição via modal (nome, curso, ano letivo, professor responsável)
- Exclusão com confirmação (afeta alunos vinculados — aviso explícito)
- Link para visualizar aulas da turma

#### Gerenciar Alunos
- Listagem com busca por nome/RA e filtro por turma
- Cadastro com nome, RA e turma
- Ativação/desativação (aluno inativo não aparece nas chamadas)
- Exclusão com soft delete

#### Gerenciar Professores
- Listagem com busca
- Cadastro com nome, e-mail e senha inicial
- Edição de dados (senha opcional — manter em branco para não alterar)
- Ativação/desativação de acesso
- Exclusão com soft delete

#### Gerenciar Empresas
- Listagem com busca por nome ou CNPJ
- Cadastro cria automaticamente um usuário de acesso com senha padrão `senai@empresa`
- Edição de nome, CNPJ e e-mail
- Ativação/desativação do acesso da empresa
- Exclusão física (remove usuário vinculado e todos os vínculos com alunos)

#### Vincular Alunos à Empresa
- Tela dedicada com lista de todos os alunos ativos
- Checkboxes para vincular/desvincular
- Busca em tempo real por nome
- Salva todos os vínculos de uma vez

#### Aulas por Turma (visão admin)
- Filtro de período (data de/até)
- Tabela com data, conteúdo, presentes, faltas e % de frequência por aula
- Exportação para CSV de toda a turma

### 7.3 Módulo Professor

#### Dashboard da Turma
- Cards de resumo: total de alunos, total de aulas, % de presença geral
- Lista cronológica de aulas com mini barra de progresso por aula
- Botão "Nova Aula" → modal com data e descrição do conteúdo
- Edição e exclusão de aulas (exclusão remove todos os registros de chamada)
- Link direto para abrir a chamada de cada aula
- Exportação para CSV

#### Chamada (Registro de Presença)
- Acesso via `/professor/aulas/{id}`
- Lista todos os alunos ativos da turma
- Toggle switch por aluno: **verde** = presente, **cinza** = falta
- Clique no toggle atualiza visualmente de forma **imediata** (UI otimista via Alpine) e salva no banco em background (Livewire)
- Contador de presentes e faltas no topo com barra de progresso percentual
- Chamada pode ser revisada/alterada a qualquer momento

### 7.4 Módulo Empresa

#### Dashboard de Frequências
- Grid de cards com todos os alunos vinculados à empresa
- Busca em tempo real por nome ou RA (Alpine, sem requisição ao servidor)
- Filtro por turma (dropdown com turmas presentes)
- Cada card leva ao histórico individual do aluno

#### Histórico do Aluno
- Visualização aula a aula do histórico de presença do aluno
- Separado por turma e período

#### Aulas
- Lista de aulas da turma a que o aluno pertence
- Dados de frequência agregados por aula (visão somente leitura)

---

## 8. Fluxos Principais

### Fluxo 1: Registro de Chamada pelo Professor

```
1. Professor faz login → redireciona para /professor/dashboard
2. Vê o resumo da turma e o histórico de aulas
3. Clica em "Nova Aula" → preenche data e conteúdo → confirma
   → Sistema cria o registro em `aulas` e redireciona para a chamada
4. Na tela de chamada (/professor/aulas/{id}):
   → Cada aluno aparece com status padrão "Falta"
   → Professor clica no toggle de cada aluno presente
   → Toggle muda de cor imediatamente (Alpine)
   → Livewire salva/atualiza o registro em `chamadas` via AJAX (updateOrCreate)
5. Percentual de presença atualiza após cada toggle
6. Professor pode voltar e corrigir a qualquer momento
```

### Fluxo 2: Empresa Consultando Frequência

```
1. Empresa faz login → redireciona para /empresa/dashboard
2. Vê grid com todos os alunos vinculados
3. Usa a barra de busca para filtrar por nome/RA ou filtra por turma
4. Clica no card de um aluno → vê histórico completo aula a aula
5. Pode também acessar "/empresa/aulas" para ver frequência por aula
```

### Fluxo 3: Admin Cadastrando Nova Empresa

```
1. Admin acessa Empresas → clica em "Nova Empresa"
2. Preenche: razão social, CNPJ, e-mail de acesso
3. Sistema cria:
   - Registro em `empresas`
   - Usuário em `users` com role='empresa', senha padrão 'senai@empresa'
4. Admin acessa "Vincular alunos" (ícone de corrente na tabela)
5. Marca os alunos que trabalham nessa empresa → salva
6. Empresa já pode fazer login e ver as frequências
```

### Fluxo 4: Exclusão com Confirmação Estilizada

```
Todos os botões de exclusão (professores, alunos, turmas, empresas, aulas)
seguem o mesmo padrão:

1. Usuário clica no ícone de lixeira
2. Alpine abre o modal de confirmação global (Alpine.store 'dialogo')
   → Exibe título, mensagem de aviso e botão de ação customizados
3. Usuário confirma → callback executa $wire.excluir(id) / $wire.deletar(id)
4. Livewire processa a exclusão no servidor
5. Flash message aparece por 3 segundos confirmando a ação
```

### Fluxo 5: Recuperação de Senha

```
1. Na tela de login, clica em "Esqueceu a senha?"
2. Digita o e-mail institucional → clica em "Enviar link de recuperação"
3. Sistema envia e-mail com link único e temporário (requer SMTP)
4. Usuário clica no link → tela "Nova senha"
5. Define nova senha + confirmação → "Redefinir senha"
6. Redirecionado para login com a nova senha
```

---

## 9. Interface e Identidade Visual

### Identidade SENAI

| Elemento | Valor |
|---|---|
| Cor primária (vermelho SENAI) | `#E30613` — classe `bg-senai`, `text-senai` |
| Sidebar | `#141414` (preto profundo) |
| Fundo da aplicação | `#F0F2F5` (cinza claro) |
| Fonte | **Inter** (400, 500, 600, 700, 800, 900) |

### Layout Principal

O layout é composto por:
- **Sidebar fixa** (256px) no lado esquerdo — escura, com navegação e dados do usuário
- **Topbar** (56px) — branca com borda inferior vermelha SENAI
- **Área de conteúdo** — cinza claro com padding, rolagem vertical independente

### Responsividade (Mobile)

O sistema foi construído primariamente para desktop, mas é utilizável em dispositivos móveis:

- **Sidebar**: em mobile vira um menu lateral deslizante (drawer), acionado por botão hamburguer no topbar. Fecha com toque no overlay, botão X ou tecla Esc.
- **Tabelas**: scroll horizontal com `overflow-x-auto` + largura mínima, evitando layout quebrado
- **Cards e grids**: já usam classes `grid-cols-1 sm:grid-cols-2 lg:grid-cols-3` desde o início
- **Chamada**: layout em linha que se adapta a telas de ~375px

### Componentes de UI Notáveis

**Modal de confirmação global**
Substituiu os `window.confirm()` padrão do browser. Um único modal reutilizável controlado pelo Alpine Store (`$store.dialogo`) é compartilhado por todos os botões de exclusão da aplicação. Inclui: barra vermelha SENAI, ícone de lixeira, título e mensagem dinâmicos, botão de confirmação com texto customizável.

**Toggle switch de chamada**
O switch usa UI otimista: ao clicar, o Alpine inverte o estado visual imediatamente (`presente = !presente`), enquanto o Livewire salva no banco em background. O usuário percebe resposta instantânea sem aguardar o servidor.

**Flash messages**
Notificações de sucesso aparecem por 3 segundos e somem automaticamente via Alpine (`setTimeout(() => show = false, 3000)`), sem intervenção do usuário.

**Tela de login**
Design personalizado com: foto de fundo da escola com overlay, card glassmorphism (backdrop-filter blur), marca SENAI em destaque no canto inferior esquerdo (desktop) e barra vermelha de identidade no topo do card.

---

## 10. Decisões Técnicas Relevantes

### Base: Laravel Breeze

O projeto partiu do **Laravel Breeze** como scaffolding inicial, que provê as rotas de autenticação (login, logout, recuperação de senha) e os controllers base. Toda a camada de negócio, modelos, migrations, componentes Livewire e interface visual foram construídos sobre essa base.

### Por que não usar `window.confirm()` para exclusões?

O `window.confirm()` nativo do browser não pode ser estilizado e quebra a experiência visual do sistema. A solução foi criar um Alpine Store global (`dialogo`) que renderiza um modal customizado estilizado com identidade SENAI, reutilizável em toda a aplicação sem duplicar código.

### Alpine.js e Livewire: o problema de duas instâncias

Livewire v4 embute sua própria instância do Alpine.js. O projeto inicialmente também importava o Alpine.js do NPM e chamava `Alpine.start()` manualmente, criando **duas instâncias conflitantes**. Isso causava o `$wire` (magic do Livewire) a não ser registrado na instância usada pelo Alpine, tornando os toggles da chamada não funcionais.

**Solução:** remover a importação manual do Alpine do `app.js` e registrar o Alpine Store via evento `alpine:init`, que é disparado pela instância correta do Alpine (gerenciada pelo Livewire) antes de inicializar os componentes.

```js
// app.js — correto
document.addEventListener('alpine:init', () => {
    Alpine.store('dialogo', { ... });
});
```

### Soft Delete vs. Exclusão Física

- **Professores, Alunos, Turmas** → Soft delete: o histórico de chamadas é preservado mesmo após a remoção do cadastro
- **Empresas** → Exclusão física: remove o usuário de acesso vinculado e todos os vínculos com alunos, já que não há histórico de chamadas associado

### `updateOrCreate` na Chamada

Ao registrar presença, o sistema usa `updateOrCreate` em vez de verificar manualmente se o registro existe. Isso garante idempotência: chamar `toggle()` múltiplas vezes ou reabrir a chamada nunca gera registros duplicados.

```php
ChamadaModel::updateOrCreate(
    ['aula_id' => $this->aula->id, 'aluno_id' => $alunoId],
    ['status' => $novo, 'updated_at' => now()]
);
```

### Constraint UNIQUE em `chamadas`

A tabela `chamadas` possui um índice único composto `(aula_id, aluno_id)`, garantindo integridade a nível de banco de dados: um aluno não pode ter dois registros de presença para a mesma aula.

---

## 11. Credenciais Padrão

### Usuários do Sistema

| Perfil | E-mail | Senha |
|---|---|---|
| Administrador | `admin@senai.br` | `senai@2026` |
| Professor | `professor@senai.br` | `senai@2026` |

### Empresas Cadastradas (exemplo/teste)

| Empresa | E-mail | Senha padrão |
|---|---|---|
| Ajinomoto | `ajinomoto@teste.com` | `senai@empresa` |
| Plasticor | `pasticor@teste.com` | `senai@empresa` |

> A senha padrão das empresas é definida na criação pelo admin. O responsável pela empresa deve alterá-la após o primeiro acesso em **Menu lateral → Alterar senha**.

---

## Resumo do que foi Desenvolvido

| # | Entrega | Descrição |
|---|---|---|
| 1 | Base Laravel + Breeze | Instalação, configuração do banco, migrations |
| 2 | Sistema de autenticação | Login, logout, redirecionamento por perfil, middleware de roles |
| 3 | CRUD de Turmas | Listagem, modal de cadastro/edição, exclusão com confirmação |
| 4 | CRUD de Alunos | Com filtro por turma, ativação/desativação, soft delete |
| 5 | CRUD de Professores | Gestão de acesso, senha opcional na edição |
| 6 | CRUD de Empresas | Criação automática de usuário vinculado, vinculação de alunos |
| 7 | Dashboard do Professor | Resumo da turma, histórico de aulas, criação/edição/exclusão |
| 8 | Chamada em tempo real | Toggle de presença com Livewire + UI otimista com Alpine |
| 9 | Portal da Empresa | Dashboard com busca/filtro, histórico por aluno |
| 10 | Modal de confirmação | Alpine Store global para todos os botões de exclusão |
| 11 | Exportação CSV | Por turma (admin e professor) |
| 12 | Alteração de senha | Disponível para todos os perfis |
| 13 | Polimento mobile | Sidebar hamburguer, scroll em tabelas, cards adaptativos |
| 14 | Telas de autenticação | Redesign de login, recuperação e redefinição de senha |

---

*Documentação gerada em 15/05/2026 — SENAI Frequências v1.0*
