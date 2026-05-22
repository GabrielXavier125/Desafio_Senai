# SENAI Frequências — Documentação Técnica

> **Audiência:** Desenvolvedor com familiaridade em PHP/Laravel.
> **Objetivo:** Referência completa da arquitetura, estrutura, modelos, rotas, componentes e decisões técnicas do projeto.

---

## Sumário

1. [Stack e versões](#1-stack-e-versões)
2. [Estrutura de pastas](#2-estrutura-de-pastas)
3. [Banco de dados](#3-banco-de-dados)
4. [Models e relacionamentos](#4-models-e-relacionamentos)
5. [Rotas](#5-rotas)
6. [Controllers](#6-controllers)
7. [Livewire Components](#7-livewire-components)
8. [Views](#8-views)
9. [Autenticação e autorização](#9-autenticação-e-autorização)
10. [Frontend (Tailwind + Alpine.js)](#10-frontend-tailwind--alpinejs)
11. [Exportação CSV](#11-exportação-csv)
12. [Decisões técnicas relevantes](#12-decisões-técnicas-relevantes)
13. [Credenciais de acesso](#13-credenciais-de-acesso)
14. [Animação de transição do login](#14-animação-de-transição-do-login)

---

## 1. Stack e versões

| Tecnologia | Versão | Papel |
|---|---|---|
| PHP | 8.3 | Linguagem do servidor |
| Laravel | 13.8 | Framework MVC |
| MySQL | 8.4 | Banco de dados relacional |
| Livewire | 4.3 | Componentes reativos server-side |
| Alpine.js | Bundled no Livewire | Reatividade client-side (UI) |
| Tailwind CSS | v4 | Framework CSS utilitário |
| Vite | 8 | Bundler de assets |
| Laragon | — | Servidor local (Windows) |

**Nota sobre Alpine.js:** A versão do Alpine.js é a que vem embutida no Livewire 4.x. O projeto **não instala Alpine via npm** — o Livewire registra `window.Alpine` e dispara o evento `alpine:init`. Stores e plugins são registrados via `document.addEventListener('alpine:init', ...)` em `resources/js/app.js`.

---

## 2. Estrutura de pastas

```
senaifrequencias/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/                      # Controllers de autenticação (gerados pelo Breeze)
│   │   │   │   ├── AuthenticatedSessionController.php
│   │   │   │   ├── NewPasswordController.php
│   │   │   │   ├── PasswordResetLinkController.php
│   │   │   │   └── ...
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php       # Dashboard do admin (estatísticas)
│   │   │   │   ├── TurmasController.php           # CRUD de turmas (render + Livewire)
│   │   │   │   ├── AlunosController.php           # CRUD de alunos
│   │   │   │   ├── ProfessoresController.php      # CRUD de professores
│   │   │   │   └── EmpresasController.php         # CRUD de empresas
│   │   │   ├── Professor/
│   │   │   │   └── DashboardController.php        # Dashboard professor (render views)
│   │   │   ├── Empresa/
│   │   │   │   └── DashboardController.php        # Dashboard empresa + histórico + aulas
│   │   │   ├── ExportController.php               # Exportação CSV (admin e professor)
│   │   │   └── ProfileController.php              # Perfil do usuário
│   │   └── Middleware/
│   │       └── (middleware de role customizado via RouteServiceProvider ou inline)
│   │
│   ├── Livewire/
│   │   ├── Admin/
│   │   │   ├── GerenciarTurmas.php                # CRUD reativo de turmas
│   │   │   ├── GerenciarAlunos.php                # CRUD reativo de alunos
│   │   │   ├── GerenciarProfessores.php           # CRUD reativo de professores + turma vinculada
│   │   │   ├── GerenciarEmpresas.php              # CRUD reativo de empresas
│   │   │   ├── AulasTurma.php                     # CRUD reativo de aulas por turma
│   │   │   └── VincularAlunosEmpresa.php          # Vínculo aluno ↔ empresa (many-to-many)
│   │   ├── Professor/
│   │   │   ├── TurmaDashboard.php                 # Dashboard + lista de aulas do professor
│   │   │   └── Chamada.php                        # Chamada (toggle presença dos alunos)
│   │   ├── Empresa/
│   │   │   ├── AulasIndex.php                     # Lista de aulas para a empresa
│   │   │   └── HistoricoAluno.php                 # Histórico de presença de um aluno
│   │   └── AlterarSenha.php                       # Alterar senha (todos os perfis)
│   │
│   └── Models/
│       ├── User.php           # Usuários (admin, professor, empresa)
│       ├── Turma.php          # Turmas
│       ├── Aluno.php          # Alunos
│       ├── Aula.php           # Aulas
│       ├── Chamada.php        # Registros de presença
│       └── Empresa.php        # Empresas parceiras
│
├── database/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2026_05_08_000001_create_turmas_table.php
│   │   ├── 2026_05_08_000002_create_alunos_table.php
│   │   ├── 2026_05_08_000003_create_empresas_table.php
│   │   ├── 2026_05_08_000004_create_empresa_aluno_table.php
│   │   ├── 2026_05_08_000005_create_aulas_table.php
│   │   ├── 2026_05_08_000006_create_chamadas_table.php
│   │   └── 2026_05_08_000010_add_soft_deletes_to_users_turmas_alunos.php
│   └── seeders/
│       └── DatabaseSeeder.php   # Cria admin padrão e professor de exemplo
│
├── resources/
│   ├── js/
│   │   ├── app.js              # Bootstrap + alpine:init + Alpine.store('dialogo')
│   │   └── bootstrap.js        # Axios
│   ├── css/
│   │   └── app.css             # @import tailwindcss + variáveis CSS (--color-senai)
│   └── views/
│       ├── layouts/
│       │   ├── senai.blade.php    # Layout principal (sidebar + topbar + mobile)
│       │   ├── guest.blade.php    # Layout para páginas sem login
│       │   └── app.blade.php      # Layout padrão Breeze (não usado nas telas principais)
│       ├── auth/
│       │   ├── login.blade.php
│       │   ├── forgot-password.blade.php   # Redesenhado (standalone, sem x-guest-layout)
│       │   └── reset-password.blade.php    # Redesenhado
│       ├── admin/
│       │   ├── dashboard.blade.php
│       │   ├── turmas/
│       │   │   ├── index.blade.php     # Wrapper que renderiza Livewire GerenciarTurmas
│       │   │   └── aulas.blade.php     # Wrapper que renderiza Livewire AulasTurma
│       │   ├── alunos/index.blade.php
│       │   ├── professores/index.blade.php
│       │   └── empresas/
│       │       ├── index.blade.php
│       │       └── show.blade.php      # Wrapper VincularAlunosEmpresa
│       ├── professor/
│       │   ├── dashboard.blade.php    # Wrapper TurmaDashboard
│       │   └── chamada.blade.php      # Wrapper Chamada
│       ├── empresa/
│       │   ├── dashboard.blade.php    # Alpine client-side search/filter (sem Livewire)
│       │   ├── aulas.blade.php
│       │   └── historico.blade.php
│       ├── livewire/
│       │   ├── admin/
│       │   │   ├── gerenciar-turmas.blade.php
│       │   │   ├── gerenciar-alunos.blade.php
│       │   │   ├── gerenciar-professores.blade.php
│       │   │   ├── gerenciar-empresas.blade.php
│       │   │   ├── aulas-turma.blade.php
│       │   │   └── vincular-alunos-empresa.blade.php
│       │   ├── professor/
│       │   │   ├── turma-dashboard.blade.php
│       │   │   └── chamada.blade.php
│       │   ├── empresa/
│       │   │   ├── aulas-index.blade.php
│       │   │   └── historico-aluno.blade.php
│       │   └── alterar-senha.blade.php
│       ├── perfil/
│       │   └── senha.blade.php
│       └── components/
│           ├── sidebar-link.blade.php
│           ├── stat-card.blade.php
│           └── icon.blade.php
│
├── routes/
│   ├── web.php     # Rotas principais (admin, professor, empresa)
│   └── auth.php    # Rotas de autenticação (geradas pelo Breeze)
│
├── public/
│   └── build/      # Assets compilados pelo Vite (gerados, não editar)
│
├── .env            # Variáveis de ambiente (DB_*, APP_KEY, MAIL_*)
├── composer.json   # Dependências PHP
├── package.json    # Dependências JS/CSS
└── vite.config.js  # Configuração do Vite
```

---

## 3. Banco de dados

### Diagrama de relacionamentos (ERD textual)

```
users (id, name, email, password, role, active, deleted_at)
  │
  ├── [hasMany] turmas via professor_id
  │     turmas (id, nome, curso, ano, professor_id, deleted_at)
  │       │
  │       ├── [hasMany] alunos via turma_id
  │       │     alunos (id, nome, ra, turma_id, active, deleted_at)
  │       │       │
  │       │       ├── [hasMany] chamadas via aluno_id
  │       │       └── [belongsToMany] empresas via empresa_aluno
  │       │
  │       └── [hasMany] aulas via turma_id
  │             aulas (id, turma_id, data, descricao, created_at, updated_at)
  │               │
  │               └── [hasMany] chamadas via aula_id
  │                     chamadas (aula_id, aluno_id, status, updated_at)
  │                     UNIQUE(aula_id, aluno_id)
  │
  └── [hasOne] empresa via user_id
        empresas (id, nome, cnpj, user_id, created_at, updated_at)
          │
          └── [belongsToMany] alunos via empresa_aluno
                empresa_aluno (empresa_id, aluno_id)
```

### Tabelas detalhadas

#### `users`
| Coluna | Tipo | Nullable | Observação |
|---|---|---|---|
| id | bigint unsigned | NOT NULL | PK, auto-increment |
| name | varchar(255) | NOT NULL | |
| email | varchar(255) | NOT NULL | UNIQUE |
| email_verified_at | timestamp | NULL | |
| password | varchar(255) | NOT NULL | bcrypt hash |
| role | enum('admin','professor','empresa') | NOT NULL | |
| active | tinyint(1) | NOT NULL | default: 1 |
| remember_token | varchar(100) | NULL | |
| created_at | timestamp | NULL | |
| updated_at | timestamp | NULL | |
| deleted_at | timestamp | NULL | SoftDeletes |

#### `turmas`
| Coluna | Tipo | Nullable | Observação |
|---|---|---|---|
| id | bigint unsigned | NOT NULL | PK |
| nome | varchar(150) | NOT NULL | |
| curso | varchar(150) | NOT NULL | |
| ano | year | NOT NULL | |
| professor_id | bigint unsigned | NULL | FK → users, nullOnDelete |
| created_at | timestamp | NULL | |
| updated_at | timestamp | NULL | |
| deleted_at | timestamp | NULL | SoftDeletes |

#### `alunos`
| Coluna | Tipo | Nullable | Observação |
|---|---|---|---|
| id | bigint unsigned | NOT NULL | PK |
| nome | varchar(150) | NOT NULL | |
| ra | varchar(50) | NOT NULL | UNIQUE |
| turma_id | bigint unsigned | NULL | FK → turmas, nullOnDelete |
| active | tinyint(1) | NOT NULL | default: 1 |
| created_at | timestamp | NULL | |
| updated_at | timestamp | NULL | |
| deleted_at | timestamp | NULL | SoftDeletes |

#### `empresas`
| Coluna | Tipo | Nullable | Observação |
|---|---|---|---|
| id | bigint unsigned | NOT NULL | PK |
| nome | varchar(150) | NOT NULL | |
| cnpj | varchar(20) | NULL | |
| user_id | bigint unsigned | NOT NULL | FK → users, cascadeOnDelete |
| created_at | timestamp | NULL | |
| updated_at | timestamp | NULL | |

#### `empresa_aluno` (pivot)
| Coluna | Tipo | Observação |
|---|---|---|
| empresa_id | bigint unsigned | FK → empresas |
| aluno_id | bigint unsigned | FK → alunos |
| PRIMARY KEY | (empresa_id, aluno_id) | |

#### `aulas`
| Coluna | Tipo | Nullable | Observação |
|---|---|---|---|
| id | bigint unsigned | NOT NULL | PK |
| turma_id | bigint unsigned | NOT NULL | FK → turmas, cascadeOnDelete |
| data | date | NOT NULL | |
| descricao | text | NOT NULL | |
| created_at | timestamp | NULL | |
| updated_at | timestamp | NULL | |

#### `chamadas`
| Coluna | Tipo | Nullable | Observação |
|---|---|---|---|
| aula_id | bigint unsigned | NOT NULL | FK → aulas, cascadeOnDelete |
| aluno_id | bigint unsigned | NOT NULL | FK → alunos, cascadeOnDelete |
| status | enum('presente','falta') | NOT NULL | default: 'falta' |
| updated_at | timestamp | NULL | |
| UNIQUE | (aula_id, aluno_id) | | Evita duplicatas |

**Nota:** A tabela `chamadas` não tem `id` próprio nem `created_at` — a PK funcional é o par `(aula_id, aluno_id)`.

---

## 4. Models e relacionamentos

### `User` (`app/Models/User.php`)
```php
fillable: ['name', 'email', 'password', 'role', 'active']
casts: ['active' => 'boolean', 'password' => 'hashed']
traits: HasFactory, Notifiable, SoftDeletes

turmas()       → hasMany(Turma::class, 'professor_id')
empresa()      → hasOne(Empresa::class)
isAdmin()      → role === 'admin'
isProfessor()  → role === 'professor'
isEmpresa()    → role === 'empresa'
```

### `Turma` (`app/Models/Turma.php`)
```php
fillable: ['nome', 'curso', 'ano', 'professor_id']
traits: SoftDeletes

professor()    → belongsTo(User::class, 'professor_id')
alunos()       → hasMany(Aluno::class)
aulas()        → hasMany(Aula::class)
```

### `Aluno` (`app/Models/Aluno.php`)
```php
fillable: ['nome', 'ra', 'turma_id', 'active']
casts: ['active' => 'boolean']
traits: SoftDeletes

turma()        → belongsTo(Turma::class)
chamadas()     → hasMany(Chamada::class)
empresas()     → belongsToMany(Empresa::class, 'empresa_aluno')
```

### `Aula` (`app/Models/Aula.php`)
```php
fillable: ['turma_id', 'data', 'descricao']

turma()        → belongsTo(Turma::class)
chamadas()     → hasMany(Chamada::class)
```

### `Chamada` (`app/Models/Chamada.php`)
```php
fillable: ['aula_id', 'aluno_id', 'status']

aula()         → belongsTo(Aula::class)
aluno()        → belongsTo(Aluno::class)
```

### `Empresa` (`app/Models/Empresa.php`)
```php
fillable: ['nome', 'cnpj', 'user_id']

user()         → belongsTo(User::class)
alunos()       → belongsToMany(Aluno::class, 'empresa_aluno')
```

---

## 5. Rotas

```php
// Redireciona / para login
Route::get('/', fn () => redirect()->route('login'));

// ADMIN — middleware: auth + role:admin — prefix: /admin — name: admin.*
GET  /admin/dashboard                      → Admin\DashboardController@index        [admin.dashboard]
GET  /admin/turmas                         → Admin\TurmasController@index            [admin.turmas.index]
GET  /admin/turmas/{turma}                 → Admin\TurmasController@show             [admin.turmas.show]
GET  /admin/turmas/{turma}/exportar        → ExportController@adminTurma             [admin.turmas.exportar]
GET  /admin/alunos                         → Admin\AlunosController@index            [admin.alunos.index]
GET  /admin/professores                    → Admin\ProfessoresController@index       [admin.professores.index]
GET  /admin/empresas                       → Admin\EmpresasController@index          [admin.empresas.index]
GET  /admin/empresas/{empresa}             → Admin\EmpresasController@show           [admin.empresas.show]

// PROFESSOR — middleware: auth + role:professor — prefix: /professor — name: professor.*
GET  /professor/dashboard                  → Professor\DashboardController@index     [professor.dashboard]
GET  /professor/aulas/{aula}               → Professor\DashboardController@chamada   [professor.aulas.chamada]
GET  /professor/exportar                   → ExportController@professorTurma         [professor.exportar]

// EMPRESA — middleware: auth + role:empresa — prefix: /empresa — name: empresa.*
GET  /empresa/dashboard                    → Empresa\DashboardController@index       [empresa.dashboard]
GET  /empresa/aulas                        → Empresa\DashboardController@aulas       [empresa.aulas]
GET  /empresa/alunos/{aluno}/historico     → Empresa\DashboardController@historico   [empresa.alunos.historico]

// COMPARTILHADO — middleware: auth
GET  /perfil/senha                         → view('perfil.senha')                    [perfil.senha]

// AUTH (via routes/auth.php — gerado pelo Breeze)
GET  /login                                → login
POST /login                                → authenticate
POST /logout                               → logout
GET  /forgot-password                      → password.request
POST /forgot-password                      → password.email
GET  /reset-password/{token}               → password.reset
POST /reset-password                       → password.update
```

### Middleware de role
O middleware `role:admin`, `role:professor`, `role:empresa` verifica `auth()->user()->role`. Usuários sem o perfil correto são redirecionados para a rota de login.

---

## 6. Controllers

### `Admin\DashboardController`
- `index()`: Conta turmas, alunos, professores e empresas. Passa para a view `admin.dashboard`.

### `Admin\TurmasController`
- `index()`: Renderiza view com `@livewire('admin.gerenciar-turmas')`
- `show(Turma $turma)`: Renderiza view de aulas com `@livewire('admin.aulas-turma', ['turma' => $turma])`

### `Admin\EmpresasController`
- `index()`: Renderiza listagem de empresas
- `show(Empresa $empresa)`: Renderiza tela de vínculo de alunos com `@livewire('admin.vincular-alunos-empresa', ['empresa' => $empresa])`

### `Professor\DashboardController`
- `index()`: Renderiza `professor.dashboard` com `@livewire('professor.turma-dashboard')`
- `chamada(Aula $aula)`: Autoriza que a aula pertence à turma do professor, renderiza `professor.chamada` com `@livewire('professor.chamada', ['aula' => $aula])`

### `Empresa\DashboardController`
- `index()`: Carrega empresa autenticada com alunos vinculados e suas chamadas. Passa `$alunosJson` para Alpine.js client-side.
- `aulas()`: Lista aulas de todas as turmas dos alunos vinculados.
- `historico(Aluno $aluno)`: Valida que o aluno pertence à empresa autenticada, renderiza com `@livewire('empresa.historico-aluno', ['aluno' => $aluno])`

### `ExportController`
- `adminTurma(Turma $turma)`: Gera CSV com todas as aulas e chamadas da turma
- `professorTurma()`: Gera CSV da turma do professor autenticado

---

## 7. Livewire Components

### Padrão geral dos componentes admin (CRUD)

Todos seguem o mesmo padrão:
```
Propriedades: search, showModal, editingId, + campos do formulário
Métodos: updatingSearch(), openModal(?int $id), closeModal(), save(), excluir(int $id)
render(): retorna view com registros paginados (10 por página)
```

---

### `Admin\GerenciarTurmas`
**Propriedades:** `nome`, `curso`, `ano`
**render():** `Turma::paginate(10)` com busca por nome/curso

---

### `Admin\GerenciarAlunos`
**Propriedades:** `nome`, `ra`, `turmaId`, `filtroTurma`
**Extras:**
- `toggleAtivo(int $id)`: ativa/desativa aluno (aluno inativo não aparece na chamada)
- Filtro por turma além do search
- **render():** passa `$turmas` para o dropdown de filtro e seleção

---

### `Admin\GerenciarProfessores`
**Propriedades:** `nome`, `email`, `senha`, `turmaId`
**Extras:**
- `turmaId`: armazena o ID da turma a ser vinculada ao professor
- `openModal()`: carrega `Turma::where('professor_id', $id)->first()?->id`
- `save()`: desvincula turma anterior → vincula nova (`professor_id`)
- `toggleAtivo(int $id)`
- **render():** passa `$turmas` (lista completa); a view marca turmas já ocupadas como `disabled`

---

### `Admin\GerenciarEmpresas`
**Propriedades:** `nome`, `cnpj`, `email` (login), `senha`
**Extras:**
- `save()`: ao criar empresa, cria o `User` (role=empresa) e o registro `Empresa` vinculado
- Ao excluir: exclui o User (cascata exclui Empresa)

---

### `Admin\AulasTurma`
**Props recebidas:** `Turma $turma`
**Propriedades:** `data`, `descricao`
**Funcionalidade:** CRUD de aulas de uma turma específica.

---

### `Admin\VincularAlunosEmpresa`
**Props recebidas:** `Empresa $empresa`
**Funcionalidade:** Exibe todos os alunos ativos. Toggle para vincular/desvincular o aluno a esta empresa (`empresa_aluno` pivot). Busca em tempo real.

---

### `Professor\TurmaDashboard`
**mount():** Carrega a turma do professor autenticado:
```php
$this->turma = auth()->user()->turmas()->with(['alunos', 'aulas.chamadas'])->first();
```
**Funcionalidade:** Exibe estatísticas (total alunos, aulas, % presença média) e lista de aulas com link para chamada.

---

### `Professor\Chamada`
**Props recebidas:** `Aula $aula`
**Funcionalidade principal:**
- `mount()`: carrega alunos ativos da turma com suas chamadas para esta aula
- `toggle(int $alunoId)`: usa `updateOrCreate` para registrar/alternar presença
- **UI Otimista**: Alpine.js alterna visualmente o toggle imediatamente; `$wire.toggle()` salva em background

```php
Chamada::updateOrCreate(
    ['aula_id' => $this->aula->id, 'aluno_id' => $alunoId],
    ['status' => $novoStatus]
);
```

---

### `Empresa\HistoricoAluno`
**Props recebidas:** `Aluno $aluno`
**Funcionalidade:** Carrega todas as chamadas do aluno (com aulas) ordenadas por data. Calcula percentual de presença.

---

### `Empresa\AulasIndex`
**Funcionalidade:** Lista aulas relevantes para os alunos da empresa autenticada.

---

### `AlterarSenha`
**Funcionalidade:** Formulário para qualquer usuário alterar sua própria senha. Valida senha atual antes de salvar.

---

## 8. Views

### Layout principal: `layouts/senai.blade.php`
- Sidebar fixa (desktop) / drawer deslizante (mobile)
- `x-data="{ sidebarOpen: false }"` na div raiz
- Overlay preto semitransparente em mobile (`@click` fecha sidebar)
- Sidebar: `fixed inset-y-0 left-0 z-50 ... lg:relative lg:z-auto lg:translate-x-0`
- `:class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"` (sobrescrito por `lg:translate-x-0`)
- Topbar com botão hamburger (`lg:hidden`) e nome do usuário
- Links da sidebar usam o componente `<x-sidebar-link>`
- Modal de confirmação global: componente Alpine via `$store.dialogo`

### Modal de confirmação global
Registrado em `app.js` via `alpine:init`:
```js
Alpine.store('dialogo', {
    aberto: false, titulo: '', mensagem: '', textoBotao: 'Confirmar', _acao: null,
    perguntar(titulo, mensagem, textoBotao, acao) { ... },
    confirmar() { if (this._acao) this._acao(); this.aberto = false; },
    cancelar() { this.aberto = false; },
});
```
Renderizado em `senai.blade.php`. Chamado por qualquer view/componente via:
```js
@click="$store.dialogo.perguntar('Título', 'Mensagem', 'Texto botão', () => $wire.metodo(id))"
```

### Dashboard Empresa: `empresa/dashboard.blade.php`
Busca e filtro client-side com Alpine.js puro (sem Livewire):
```js
x-data="{ busca: '', turmaFiltro: '', alunos: {{ $alunosJson }} }"
```
O controller passa `Js::from($alunos)` para garantir escape seguro de HTML.

### Telas auth customizadas
`forgot-password.blade.php` e `reset-password.blade.php` são **standalone HTML** (sem `x-guest-layout`), com o mesmo design do `login.blade.php` (fundo gradiente SENAI vermelho, card centralizado, logo).

---

## 9. Autenticação e autorização

### Autenticação
Gerada pelo **Laravel Breeze**. Usa sessions (stateful). Controllers em `app/Http/Controllers/Auth/`.

### Autorização por role
O arquivo `routes/web.php` usa middleware `role:X` para proteger grupos de rotas. O middleware verifica `auth()->user()->role`.

Adicionalmente, os Livewire components da área do professor verificam se a aula pertence à turma do professor autenticado antes de permitir operações.

### Soft Deletes
- `User`, `Turma`, `Aluno` usam `SoftDeletes`
- `Empresa` **não** usa SoftDeletes (exclui diretamente)
- `Aula` e `Chamada` **não** usam SoftDeletes (excluídas por cascata via FK)

---

## 10. Frontend (Tailwind + Alpine.js)

### Tailwind CSS v4
Configurado em `resources/css/app.css`:
```css
@import "tailwindcss";

@theme {
    --color-senai: #c8161d;
}
```

A cor `senai` (vermelho SENAI) fica disponível como `bg-senai`, `text-senai`, `ring-senai`, etc.

### Alpine.js — integração com Livewire
**Regra crítica:** Não importar Alpine via npm. O Livewire 4 registra `window.Alpine` e dispara `alpine:init` ao iniciar. Todo código Alpine customizado vai em:
```js
document.addEventListener('alpine:init', () => {
    Alpine.store('dialogo', { ... });
});
```

### UI Otimista na chamada
O toggle de presença usa Alpine local state para atualização instantânea:
```html
x-data="{ presente: {{ $status === 'presente' ? 'true' : 'false' }} }"
@click="presente = !presente; $wire.toggle({{ $aluno->id }})"
```
O estado visual muda imediatamente; o Livewire salva em background.

---

## 11. Exportação CSV

`ExportController` gera um response com headers CSV:
```php
return response()->streamDownload(function () use ($turma) {
    // fputcsv com header + linhas de dados
}, 'frequencia-turma.csv', [
    'Content-Type' => 'text/csv; charset=UTF-8',
]);
```

O CSV inclui: data da aula, descrição, nome do aluno, RA, status (Presente/Falta).

---

## 12. Decisões técnicas relevantes

### Por que `updateOrCreate` na chamada?
A tabela `chamadas` tem `UNIQUE(aula_id, aluno_id)`. O `updateOrCreate` é idempotente: se o registro já existe (professor clicou mais de uma vez), ele atualiza o status em vez de tentar inserir duplicata (o que daria erro de constraint).

### Por que Alpine.js sem npm?
Livewire 4 embute Alpine.js internamente. Importar Alpine via npm gerava **dois instances** do Alpine: o npm Alpine iniciava como deferred ES module, sobrescrevia `window.Alpine` que o Livewire tinha registrado, e o `$wire` magic nunca funcionava corretamente. Solução: remover import do npm, usar `alpine:init` event.

### Por que a empresa usa Alpine client-side em vez de Livewire?
O dashboard da empresa faz busca e filtro de alunos. Como o conjunto de dados (alunos vinculados à empresa) já é carregado de uma vez no controller, o filtro pode ser feito inteiramente no browser com Alpine.js, sem round-trips ao servidor — mais rápido para o usuário.

### Por que Soft Deletes?
Preservar histórico de frequência. Se um aluno for excluído, os registros de chamada ficam órfãos sem soft delete. Com soft delete, o aluno fica "invisível" no sistema mas os dados históricos permanecem intactos.

### Professor vinculado a uma turma
A relação `turmas.professor_id` garante que um professor veja apenas sua turma no `TurmaDashboard`. O `mount()` do componente usa `auth()->user()->turmas()->first()` — se o professor não tiver turma, o dashboard mostra estado vazio.

---

## 13. Credenciais de acesso

| Perfil | E-mail | Senha |
|---|---|---|
| Administrador | admin@senai.br | 1234 |
| Professor (padrão) | professor@senai.br | 1234 |

**Nota:** Professores e empresas adicionais são criados pelo administrador via interface web.

---

## 14. Animação de transição do login

### Visão geral

Ao fazer login com sucesso, o card da tela de login sobe e desaparece pelo topo da viewport enquanto um overlay na cor de fundo do dashboard (`#F0F2F5`) cobre a tela. O dashboard aparece com um fade-in de 300ms, criando uma transição contínua sem flash visual.

### Arquivos envolvidos

| Arquivo | O que foi alterado |
|---|---|
| `resources/views/auth/login.blade.php` | `id` no card e no form, overlay div, script de animação |
| `resources/views/layouts/senai.blade.php` | `pageFadeIn` animation no `<body>` |

### Mecanismo

**1. Intercepção do submit via `fetch()`**

O form tem `id="login-form"`. Um `EventListener` no evento `submit` chama `e.preventDefault()` e envia as credenciais via `fetch()` com `redirect: 'follow'`.

```javascript
const response = await fetch(form.action, {
    method: 'POST', body: formData, redirect: 'follow'
});
urlFinal = response.url;
```

**2. Detecção do resultado por URL**

O Laravel redireciona para `/login` em caso de falha e para o dashboard em caso de sucesso. A detecção é feita pela URL final da resposta:

```javascript
const loginFalhou = !urlFinal || urlFinal.includes('/login');
```

- **Falha** → `window.location.href = urlFinal` (navega para `/login` com erros na session — comportamento padrão preservado)
- **Sucesso** → dispara a animação e depois navega

**3. Animação de saída**

```javascript
function animarSaida(callback) {
    overlay.style.opacity = '1';                  // fundo vira #F0F2F5 em 550ms
    card.style.transform  = 'translateY(-110vh)'; // card sobe para fora da tela em 500ms
    card.style.opacity    = '0';
    setTimeout(callback, 650);                    // navega após animação completar
}
```

O overlay tem `transition: opacity 0.55s cubic-bezier(0.4,0,0.2,1)` definido inline no HTML.
O card tem a transition aplicada dinamicamente pelo JS antes de mudar `transform` e `opacity`.

**4. Entrada no dashboard**

O `<body>` do layout recebe `animation: pageFadeIn 0.3s ease-out both` via tag `<style>` injetada no próprio elemento. O `pageFadeIn` é um simples `opacity: 0 → 1`. Como o overlay termina na mesma cor do fundo do body, a troca de página é imperceptível.

### Por que fetch() e não submit() direto?

Se o form fosse submetido normalmente (sem AJAX), o browser navegaria imediatamente e não haveria tempo para animar. Com `fetch()`, o JS controla o momento exato da navegação — só navega após `setTimeout(callback, 650)`.

### Tratamento de erros

- **Erro de rede** → `catch` cai em `form.submit()` (fallback: comportamento padrão sem animação)
- **Login com credenciais erradas** → nunca dispara a animação; navega normalmente para mostrar o erro
- **CSRF** → o `FormData` captura automaticamente o campo `@csrf` do formulário, então o token é enviado junto com as credenciais
