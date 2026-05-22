# SENAI Frequências — Documentação de Apresentação

> **Para quem é este documento?**
> Este documento foi escrito para apresentar o projeto a pessoas que estão aprendendo programação. Todos os termos técnicos são explicados com exemplos do dia a dia.

---

## Sumário

1. [O que é o projeto?](#1-o-que-é-o-projeto)
2. [Qual problema ele resolve?](#2-qual-problema-ele-resolve)
3. [Para quem foi feito?](#3-para-quem-foi-feito)
4. [Como o sistema funciona — visão geral](#4-como-o-sistema-funciona--visão-geral)
5. [As tecnologias utilizadas](#5-as-tecnologias-utilizadas)
6. [O banco de dados](#6-o-banco-de-dados)
7. [As telas do sistema — passo a passo](#7-as-telas-do-sistema--passo-a-passo)
8. [O que aprendi construindo este projeto](#8-o-que-aprendi-construindo-este-projeto)
9. [A estrutura de pastas — o que cada pasta faz](#9-a-estrutura-de-pastas--o-que-cada-pasta-faz)
10. [Onde está o CSS do site](#10-onde-está-o-css-do-site)
11. [Código explicado linha por linha](#11-código-explicado-linha-por-linha)
12. [De onde cada tela puxa seus dados](#12-de-onde-cada-tela-puxa-seus-dados)
13. [Como o sistema protege as páginas](#13-como-o-sistema-protege-as-páginas)
14. [O padrão MVC na prática — exemplo completo](#14-o-padrão-mvc-na-prática--exemplo-completo)
15. [A animação de transição do login](#15-a-animação-de-transição-do-login)

---

## 1. O que é o projeto?

**SENAI Frequências** é um sistema web de controle de frequência escolar desenvolvido para o SENAI.

Na prática, ele substitui a velha lista de papel onde o professor marca quem está presente ou ausente na aula. Em vez de papel, o professor usa o computador ou celular para fazer a chamada, e tudo fica salvo automaticamente em um banco de dados.

Além do professor, o sistema tem mais dois tipos de usuários: o **administrador** (responsável por gerenciar tudo) e a **empresa** (que pode consultar a frequência dos seus aprendizes, ou seja, dos alunos que trabalham na empresa).

---

## 2. Qual problema ele resolve?

Imagine a situação:

- O professor faz a chamada em papel. Depois precisa passar os dados para uma planilha. Depois a empresa precisa ligar para a escola para saber se o aprendiz está comparecendo às aulas.
- E se o papel se perder? E se houver erro na transcrição? E se a empresa precisar de um relatório urgente?

O **SENAI Frequências** resolve isso centralizando tudo em um único lugar online:

| Problema antigo | Solução no sistema |
|---|---|
| Lista de chamada em papel | Chamada digital com toggle (ligar/desligar) |
| Dados jogados em planilhas | Banco de dados organizado com histórico completo |
| Empresa não sabe a frequência | Acesso direto ao portal da empresa, em tempo real |
| Difícil gerar relatórios | Exportação em CSV com um clique |
| Qualquer um podia ver os dados | Sistema de login com permissões por perfil |

---

## 3. Para quem foi feito?

O sistema tem **três tipos de usuários** (chamados de "perfis" ou "roles"):

### Administrador
- É a pessoa responsável pelo SENAI
- Tem acesso total ao sistema
- Pode criar turmas, cadastrar alunos, professores e empresas
- Pode criar aulas e visualizar tudo
- Existe apenas um administrador no sistema

### Professor
- Cada professor tem seu próprio login
- Cada professor é vinculado a uma turma específica
- Faz a chamada das aulas da sua turma
- Pode exportar o relatório de frequência da sua turma em CSV

### Empresa
- Cada empresa parceira do SENAI tem seu próprio login
- Pode visualizar apenas os alunos que foram vinculados a ela
- Consulta a frequência dos seus aprendizes
- Acessa o histórico de chamadas por aluno
- **Não pode alterar nada**, apenas visualizar

---

## 4. Como o sistema funciona — visão geral

Pense no sistema como um conjunto de "peças" que se encaixam:

```
[Navegador do usuário]
        ↓
  [Tela de Login]
        ↓
  [Redirecionamento automático conforme o perfil]
        ↓
┌───────────────┬──────────────────┬─────────────────────┐
│   ADMIN       │   PROFESSOR      │   EMPRESA           │
│               │                  │                     │
│ Gerencia:     │ Vê sua turma     │ Consulta freq.      │
│ - Turmas      │ Faz chamada      │ dos seus aprendizes │
│ - Alunos      │ Exporta CSV      │                     │
│ - Professores │                  │                     │
│ - Empresas    │                  │                     │
│ - Aulas       │                  │                     │
└───────────────┴──────────────────┴─────────────────────┘
        ↓
  [Banco de Dados MySQL]
  (salva tudo de forma permanente)
```

---

## 5. As tecnologias utilizadas

Aqui estão as ferramentas usadas para construir o sistema. Para cada uma, tem uma explicação simples:

---

### PHP 8.3 — A linguagem de programação do servidor

**O que é?** PHP é uma linguagem de programação que roda no servidor (no computador que hospeda o site). Quando você acessa uma página, o servidor processa o PHP e devolve o HTML para o seu navegador.

**Analogia:** Pense no PHP como a cozinha de um restaurante. Você (o cliente/navegador) faz o pedido, a cozinha (PHP) prepara e te entrega o prato pronto.

**Por que PHP?** É amplamente usado para web, especialmente com o framework Laravel.

---

### Laravel 13.8 — O framework PHP

**O que é?** Um framework é um conjunto de ferramentas prontas que aceleram o desenvolvimento. Em vez de criar tudo do zero, usamos o Laravel que já tem login, banco de dados, validação de formulários, etc.

**Analogia:** Se construir um site do zero é como construir uma casa tijolo por tijolo, o Laravel é como comprar uma casa pré-fabricada: a estrutura já está pronta, você só decora e personaliza.

**O que o Laravel oferece neste projeto:**
- Sistema de autenticação (login/logout/recuperar senha)
- ORM Eloquent (conversa com o banco de dados usando PHP, sem SQL bruto)
- Sistema de rotas (define quais URLs existem e quem pode acessar)
- Blade (linguagem de templates para as telas HTML)
- Migrações (cria e atualiza as tabelas do banco de dados de forma organizada)

---

### MySQL 8.4 — O banco de dados

**O que é?** Um banco de dados é onde todas as informações ficam salvas de forma permanente. O MySQL é um dos bancos de dados mais populares do mundo.

**Analogia:** Se o sistema fosse uma escola, o banco de dados seria o arquivo físico onde ficam guardadas as fichas de todos os alunos, professores e registros de presença.

**Neste projeto o MySQL guarda:**
- Usuários (admins, professores, empresas)
- Turmas e alunos
- Aulas realizadas
- Registro de presença de cada aluno em cada aula

---

### Livewire 4.3 — Interatividade sem recarregar a página

**O que é?** Normalmente, quando você envia um formulário em um site, a página inteira recarrega. O Livewire permite que partes da página se atualizem sem recarregar tudo, como se fosse um aplicativo moderno.

**Analogia:** Pense no Livewire como a diferença entre uma loja física antiga (você faz um pedido, vai embora e volta no dia seguinte para buscar) vs. um balcão de fast food (você pede, espera 2 minutos e já recebe ali mesmo).

**Onde é usado:** Todos os modais (janelas de edição), buscas em tempo real, chamada dos alunos.

---

### Alpine.js — Pequenas animações e interações

**O que é?** Uma biblioteca JavaScript leve para adicionar comportamentos interativos simples na tela, como abrir/fechar menus, mostrar/esconder elementos, e o famoso toggle (botão liga/desliga).

**Analogia:** Se o Livewire é o motor do carro (lida com dados e servidor), o Alpine.js é o painel: faz o velocímetro girar, acende o farol, abre o vidro elétrico — tudo visual e imediato.

**Onde é usado:** Sidebar mobile (abrir/fechar), modais de confirmação, switch de presença na chamada.

---

### Tailwind CSS v4 — O visual do sistema

**O que é?** Uma biblioteca de estilos CSS que usa classes diretamente no HTML para estilizar os elementos. Em vez de criar um arquivo `.css` separado, você escreve `bg-red-600 text-white px-4 py-2 rounded` diretamente no HTML.

**Analogia:** Em vez de pedir para um estilista criar um traje personalizado (CSS tradicional), você combina peças prontas de um armário (classes Tailwind) para montar o look.

**Por que Tailwind?** Desenvolvimento muito mais rápido, código visual mais legível, e o arquivo CSS final é otimizado automaticamente.

---

### Vite 8 — O compilador de assets

**O que é?** Uma ferramenta que pega todos os arquivos JavaScript e CSS, compila, minifica (remove espaços desnecessários) e gera os arquivos finais otimizados para o navegador.

**Analogia:** É o editor de vídeo do projeto: pega todos os "clipes" brutos (JS, CSS) e renderiza um vídeo final polido e leve.

---

### Laragon — O servidor local de desenvolvimento

**O que é?** Um programa para Windows que simula um servidor web na sua máquina. Permite testar o site localmente sem precisar de hospedagem.

**Analogia:** É como um estúdio em casa: você ensaia (desenvolve) ali, e quando estiver pronto, vai para o palco de verdade (servidor de produção).

---

## 6. O banco de dados

O banco de dados é como uma coleção de tabelas (parecidas com planilhas Excel), onde cada tabela guarda um tipo de informação.

### As 7 tabelas do sistema

#### Tabela `users` — Usuários do sistema
Guarda todos os logins do sistema.

| Campo | O que é | Exemplo |
|---|---|---|
| id | Número único de identificação | 1, 2, 3... |
| name | Nome do usuário | "Prof. João Silva" |
| email | E-mail de login | "joao@senai.br" |
| password | Senha (criptografada) | "$2y$10$..." |
| role | Tipo de usuário | "admin", "professor", "empresa" |
| active | Se está ativo ou não | true / false |

#### Tabela `turmas` — Turmas da escola
Cada turma tem um nome, curso, ano e professor responsável.

| Campo | O que é | Exemplo |
|---|---|---|
| id | Número único | 1, 2, 3... |
| nome | Nome da turma | "Turma A - 2026" |
| curso | Nome do curso | "Desenvolvimento de Sistemas" |
| ano | Ano letivo | 2026 |
| professor_id | Qual professor é responsável | 5 (referência à tabela users) |

#### Tabela `alunos` — Os alunos
Cada aluno pertence a uma turma.

| Campo | O que é | Exemplo |
|---|---|---|
| id | Número único | 1, 2, 3... |
| nome | Nome completo | "Maria Oliveira" |
| ra | Registro do Aluno | "RA-2026-001" |
| turma_id | Qual turma o aluno pertence | 2 (referência à tabela turmas) |
| active | Se está ativo | true / false |

#### Tabela `empresas` — Empresas parceiras
Cada empresa está vinculada a um usuário do tipo "empresa".

| Campo | O que é | Exemplo |
|---|---|---|
| id | Número único | 1, 2... |
| nome | Nome da empresa | "Tech Solutions LTDA" |
| cnpj | CNPJ da empresa | "12.345.678/0001-99" |
| user_id | Qual login é desta empresa | 8 (referência à tabela users) |

#### Tabela `empresa_aluno` — Quais alunos cada empresa acompanha
Uma tabela de ligação: conecta empresas a alunos (uma empresa pode ter vários alunos, e um aluno pode estar em várias empresas).

| Campo | O que é |
|---|---|
| empresa_id | ID da empresa |
| aluno_id | ID do aluno |

#### Tabela `aulas` — As aulas realizadas
Cada aula pertence a uma turma e tem uma data.

| Campo | O que é | Exemplo |
|---|---|---|
| id | Número único | 1, 2, 3... |
| turma_id | Qual turma foi essa aula | 1 |
| data | Data da aula | 2026-05-15 |
| descricao | Descrição do conteúdo | "Introdução ao PHP" |

#### Tabela `chamadas` — Registro de presença
Cada linha representa a presença ou ausência de UM aluno em UMA aula específica.

| Campo | O que é | Exemplo |
|---|---|---|
| aula_id | Qual aula | 3 |
| aluno_id | Qual aluno | 7 |
| status | Presente ou falta | "presente" / "falta" |

---

### Como as tabelas se relacionam

```
users
  └──→ turmas (um professor tem várias turmas)
         └──→ alunos (uma turma tem vários alunos)
         └──→ aulas (uma turma tem várias aulas)
                └──→ chamadas (uma aula tem o registro de presença de cada aluno)

users
  └──→ empresas (um usuário-empresa tem um registro de empresa)
         └──→ empresa_aluno (uma empresa pode monitorar vários alunos)
```

---

## 7. As telas do sistema — passo a passo

### Tela de Login

Primeira tela que qualquer usuário vê. Tem campo de e-mail, senha e link "Esqueci a senha". O sistema verifica as credenciais e redireciona automaticamente:
- Admin → `/admin/dashboard`
- Professor → `/professor/dashboard`
- Empresa → `/empresa/dashboard`

---

### Área do Administrador

#### Dashboard
Tela inicial com quatro cartões mostrando o total de: turmas, alunos, professores e empresas cadastradas.

#### Gerenciar Turmas
Lista todas as turmas. Para cada turma, o admin pode:
- Criar nova turma (modal com nome, curso, ano)
- Editar turma existente
- Excluir turma
- Clicar em "Ver Aulas" para gerenciar as aulas daquela turma

#### Gerenciar Alunos
Lista todos os alunos com busca por nome/RA e filtro por turma. Para cada aluno:
- Criar novo aluno (modal com nome, RA, turma)
- Editar
- Ativar/Desativar (aluno desativado não aparece na chamada)
- Excluir

#### Gerenciar Professores
Lista todos os professores. Para cada um:
- Criar novo professor (modal com nome, e-mail, senha, turma vinculada)
- Editar (incluindo alterar turma)
- Ativar/Desativar
- Excluir
- A tabela mostra qual turma cada professor está vinculado

#### Gerenciar Empresas
Lista todas as empresas. Para cada uma:
- Criar nova empresa (modal com nome, CNPJ, e-mail e senha do login)
- Editar
- Excluir
- Botão "Gerenciar Alunos" — abre uma tela onde o admin vincula quais alunos esta empresa pode ver

#### Aulas de uma Turma
Listagem de aulas de uma turma específica. O admin pode:
- Criar nova aula (data + descrição)
- Editar aula
- Excluir aula
- Exportar relatório CSV com todas as presenças

---

### Área do Professor

#### Dashboard
Exibe as estatísticas da turma vinculada ao professor:
- Total de alunos, número de aulas, percentual médio de presença
- Lista de todas as aulas com data, descrição e total de presenças
- Botão para fazer a chamada de cada aula
- Botão para exportar CSV

#### Tela de Chamada
Tela principal e mais importante para o professor:
- Lista todos os alunos da turma
- Cada aluno tem um **toggle** (interruptor) que alterna entre "Presente" (verde) e "Falta" (vermelho)
- A mudança é **instantânea** visualmente — sem precisar esperar o servidor responder
- O sistema salva automaticamente no banco de dados em segundo plano

---

### Área da Empresa

#### Dashboard
Visão geral com lista de todos os alunos vinculados a essa empresa. Mostra:
- Nome do aluno, RA, turma
- Percentual de presença de cada aluno
- Barra de busca por nome ou RA
- Filtro por turma

#### Histórico do Aluno
Ao clicar em um aluno, a empresa vê o histórico completo de presença:
- Data de cada aula
- Status (presente ou falta)
- Total de aulas, presenças e percentual

#### Consulta de Aulas
Lista todas as aulas de todas as turmas, com data e descrição.

---

### Recuperação de Senha

Ao clicar em "Esqueci a senha" na tela de login:
1. O usuário digita seu e-mail
2. O sistema envia um e-mail com um link de recuperação
3. O usuário clica no link, define uma nova senha
4. Pronto, pode fazer login com a nova senha

---

## 8. O que aprendi construindo este projeto

Este projeto demonstra os principais conceitos de desenvolvimento web moderno:

### Conceitos de Back-end (servidor)
- **MVC** (Model-View-Controller): separação de responsabilidades — o Model cuida dos dados, o Controller da lógica, o View da apresentação
- **Autenticação e autorização**: controlar quem pode entrar e o que cada um pode fazer
- **ORM**: acessar o banco de dados usando objetos PHP em vez de SQL bruto
- **Relacionamentos**: como conectar dados de tabelas diferentes (um professor tem muitos alunos, etc.)
- **Soft Delete**: "excluir" registros sem realmente apagá-los, mantendo histórico
- **Migrações**: controle de versão do banco de dados

### Conceitos de Front-end (navegador)
- **Componentes reativos**: partes da página que atualizam sem recarregar (Livewire)
- **UI Otimista**: mostrar o resultado visualmente antes do servidor confirmar (toggle da chamada)
- **Design responsivo**: o sistema funciona tanto no desktop quanto no celular
- **Tailwind CSS**: estilização por classes utilitárias

### Boas práticas
- **Validação de dados**: verificar se o que o usuário digitou é válido antes de salvar
- **Controle de acesso por role**: middleware que bloqueia acesso a páginas não autorizadas
- **Paginação**: não carregar 1000 registros de uma vez, mostrar de 10 em 10
- **Feedback ao usuário**: mensagens de sucesso e erro sempre visíveis

---

*Desenvolvido com Laravel 13.8, PHP 8.3, MySQL 8.4, Livewire 4.3, Alpine.js e Tailwind CSS v4.*

---

## 9. A estrutura de pastas — o que cada pasta faz

Quando você abre o projeto no VS Code, vê várias pastas. Aqui está o que cada uma significa:

```
senaifrequencias/          ← Pasta raiz do projeto
```

---

### `app/` — O coração do sistema (PHP puro)

É a pasta mais importante. Aqui fica **todo o código PHP que você escreveu**.

```
app/
├── Http/
│   └── Controllers/       ← Os "gerentes" de cada página
│       ├── Admin/          ← Gerentes das páginas do administrador
│       ├── Professor/      ← Gerente das páginas do professor
│       └── Empresa/        ← Gerente das páginas da empresa
│
├── Livewire/              ← Componentes interativos (modais, formulários ao vivo)
│   ├── Admin/              ← Tabelas com busca, modais de criar/editar
│   ├── Professor/          ← Dashboard da turma + chamada
│   └── Empresa/            ← Histórico do aluno
│
└── Models/                ← As "fichas" que representam cada tabela do banco
    ├── User.php            ← Representa a tabela users
    ├── Turma.php           ← Representa a tabela turmas
    ├── Aluno.php           ← Representa a tabela alunos
    ├── Aula.php            ← Representa a tabela aulas
    ├── Chamada.php         ← Representa a tabela chamadas
    └── Empresa.php         ← Representa a tabela empresas
```

**Analogia para Controllers:** Imagine um recepcionista. Quando você chega ao hotel (acessa uma URL), o recepcionista (Controller) te recebe, busca as informações certas (consulta o banco de dados), e te encaminha para o quarto correto (mostra a página certa).

**Analogia para Models:** São como fichas de cadastro. O `Aluno.php` sabe que o aluno tem nome, RA e turma — e sabe como buscar, salvar e excluir alunos no banco de dados.

---

### `database/` — Tudo relacionado ao banco de dados

```
database/
├── migrations/            ← Instruções para criar as tabelas do banco
│   ├── ..._create_turmas_table.php
│   ├── ..._create_alunos_table.php
│   └── ...
└── seeders/               ← Scripts para popular o banco com dados iniciais
    └── DatabaseSeeder.php  ← Cria o usuário admin padrão
```

**O que são migrations?** São arquivos PHP que descrevem como criar (ou modificar) tabelas no banco de dados. Em vez de abrir o MySQL e digitar SQL na mão, você escreve PHP e roda o comando `php artisan migrate` — o Laravel cria todas as tabelas automaticamente.

**Analogia:** É como uma receita de bolo. Em vez de descrever o bolo pronto, descreve o **passo a passo para criá-lo**. Qualquer pessoa pode pegar a receita e criar o mesmo bolo do zero.

---

### `resources/` — Tudo que o usuário vê (HTML, CSS, JavaScript)

```
resources/
├── views/                 ← As telas HTML do sistema
│   ├── layouts/            ← O "esqueleto" que todas as páginas usam
│   │   └── senai.blade.php ← Layout com sidebar, topbar, menu lateral
│   ├── auth/               ← Telas de login, esqueci a senha, redefinir senha
│   ├── admin/              ← Páginas da área do administrador
│   ├── professor/          ← Páginas da área do professor
│   ├── empresa/            ← Páginas da área da empresa
│   └── livewire/           ← Partes interativas das páginas (os formulários vivos)
│
├── css/
│   └── app.css            ← O arquivo de estilos do site (CSS + configuração Tailwind)
│
└── js/
    └── app.js             ← O arquivo JavaScript principal do site
```

**O que são views `.blade.php`?** São arquivos HTML com "superpoderes" do Laravel. O `.blade.php` permite misturar HTML com PHP usando uma sintaxe mais limpa. Por exemplo, `{{ $nome }}` exibe uma variável PHP, e `@foreach($alunos as $aluno)` faz um loop.

---

### `routes/` — O mapa de URLs do sistema

```
routes/
├── web.php    ← Define TODAS as URLs do site e quem pode acessá-las
└── auth.php   ← URLs de autenticação (login, logout, recuperar senha)
```

**O que são rotas?** É o mapa do site. Quando você digita `senaifrequencias.test/admin/dashboard` no navegador, o Laravel olha neste arquivo para saber: "quem é responsável por essa URL? Que código precisa rodar?".

---

### `public/` — A única pasta acessível pelo navegador

```
public/
├── index.php     ← Ponto de entrada de TODAS as requisições
├── images/       ← Imagens do site (foto de fundo do login, etc.)
└── build/        ← CSS e JS compilados pelo Vite (gerados automaticamente)
```

**Importante:** O navegador só consegue acessar arquivos desta pasta. Todo o resto (app/, database/, etc.) fica "escondido" do público. Isso é uma medida de segurança.

---

### `.env` — As configurações secretas

Arquivo na raiz do projeto com configurações que variam por ambiente:
- Senha do banco de dados
- Chave secreta do sistema
- Configurações de e-mail

**Este arquivo nunca deve ser enviado ao GitHub ou compartilhado** — ele contém informações sensíveis.

---

### `vendor/` — Biblioteca de terceiros (não editar)

Contém o código do Laravel, Livewire e todas as outras bibliotecas instaladas. Gerado automaticamente pelo Composer. Nunca edite arquivos aqui.

---

### `node_modules/` — Biblioteca JavaScript (não editar)

Contém o Tailwind CSS, Vite e outras ferramentas JS. Gerado pelo npm. Nunca edite arquivos aqui.

---

## 10. Onde está o CSS do site

**Pergunta do professor:** "Onde está o CSS do seu site?"

**Resposta direta:** O CSS do site está em `resources/css/app.css`. Mas a maioria dos estilos não é escrita como CSS tradicional — usa-se o **Tailwind CSS**, que coloca os estilos diretamente no HTML como classes.

---

### O arquivo `resources/css/app.css`

Este arquivo tem apenas estas linhas:

```css
@import 'tailwindcss';

@source '../../vendor/laravel/framework/...';
@source '../**/*.blade.php';
@source '../**/*.js';

@theme {
    --font-sans: 'Inter', 'Roboto', ui-sans-serif, system-ui, sans-serif;

    --color-senai:      #E30613;   /* ← Vermelho SENAI */
    --color-senai-dark: #1A1A1A;   /* ← Preto da sidebar */
    --color-senai-bg:   #F5F5F5;   /* ← Cinza do fundo */
    --color-senai-aux:  #555555;

    --animate-fade-up:   fade-up   0.5s ease-out both;   /* ← Animação de entrada */
    --animate-fade-in:   fade-in   0.4s ease-out both;
    --animate-fade-left: fade-left 0.5s ease-out both;
}

@keyframes fade-up {
    from { opacity: 0; transform: translateY(18px); }  /* ← começa invisível, 18px abaixo */
    to   { opacity: 1; transform: translateY(0);    }  /* ← termina visível, posição normal */
}
```

**O que cada parte faz:**
- `@import 'tailwindcss'` → importa todo o Tailwind (equivale a incluir milhares de classes CSS prontas)
- `@source '../**/*.blade.php'` → diz ao Tailwind para escanear os arquivos HTML e incluir só as classes que estão sendo usadas (o CSS final fica pequeno)
- `@theme { --color-senai: #E30613 }` → define a cor vermelha do SENAI como uma variável que pode ser usada em qualquer lugar como `bg-senai`, `text-senai`, `border-senai`
- `@keyframes fade-up` → cria a animação que faz os elementos "subirem" na tela ao aparecer

---

### Como o Tailwind funciona na prática

Em vez de escrever CSS separado assim:
```css
/* CSS tradicional */
.meu-botao {
    background-color: #E30613;
    color: white;
    padding: 12px 16px;
    border-radius: 8px;
    font-weight: bold;
}
```

No Tailwind, você escreve as classes diretamente no HTML:
```html
<!-- Tailwind: cada classe = uma propriedade CSS -->
<button class="bg-senai text-white px-4 py-3 rounded-lg font-bold">
    Entrar
</button>
```

**Traduzindo cada classe:**
| Classe Tailwind | CSS equivalente |
|---|---|
| `bg-senai` | `background-color: #E30613` |
| `text-white` | `color: white` |
| `px-4` | `padding-left: 1rem; padding-right: 1rem` |
| `py-3` | `padding-top: 0.75rem; padding-bottom: 0.75rem` |
| `rounded-lg` | `border-radius: 0.5rem` |
| `font-bold` | `font-weight: 700` |
| `hover:bg-red-700` | ao passar o mouse: `background-color: #b91c1c` |
| `lg:hidden` | em telas grandes (≥1024px): `display: none` |

---

### Onde o CSS final fica após compilado

Quando você roda `npm run build`, o Vite pega o `app.css` e gera o arquivo final em `public/build/assets/app-[hash].css`. Esse é o arquivo que o navegador realmente baixa. Ele contém **apenas as classes do Tailwind que você usou** — o resto é removido automaticamente.

---

### Como o CSS é carregado nas páginas

No arquivo `resources/views/auth/login.blade.php`, linha 9:
```html
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

Essa linha `@vite(...)` é uma diretiva do Blade que gera automaticamente as tags `<link>` e `<script>` apontando para os arquivos compilados em `public/build/`. Em desenvolvimento, aponta para o servidor do Vite (com hot reload). Em produção, aponta para os arquivos estáticos.

---

## 11. Código explicado linha por linha

### 11.1 — A tela de login (`resources/views/auth/login.blade.php`)

```html
<!DOCTYPE html>
<html lang="pt-BR">
```
↑ Declara que é um documento HTML e que o idioma é Português do Brasil.

```html
<meta name="viewport" content="width=device-width, initial-scale=1.0">
```
↑ Faz o site funcionar bem em celulares. Sem essa linha, o site apareceria minúsculo no celular.

```html
@vite(['resources/css/app.css', 'resources/js/app.js'])
```
↑ Carrega o CSS (estilos) e o JavaScript do site. O `@vite` é uma diretiva do Laravel que sabe onde estão os arquivos compilados.

```html
<img src="/images/login-bg.jpg" class="w-full h-full object-cover"
     style="filter:brightness(0.75) saturate(0.9);">
```
↑ Exibe a foto de fundo. `object-cover` faz a imagem preencher toda a área sem distorcer. `brightness(0.75)` escurece 25% para o texto ficar legível em cima.

```html
<form method="POST" action="{{ route('login') }}" class="space-y-5">
    @csrf
```
↑ O formulário envia dados via POST (não aparece na URL) para a rota de login. `{{ route('login') }}` gera a URL automaticamente. `@csrf` cria um campo oculto com um token de segurança que protege contra ataques.

```html
<input value="{{ old('email') }}" ...>
```
↑ `old('email')` recoloca o e-mail que o usuário digitou caso o formulário dê erro e precise recarregar. Evita que o usuário precise digitar tudo de novo.

```html
@if($errors->any())
    <div class="... bg-red-50 ...">
        {{ $errors->first() }}
    </div>
@endif
```
↑ `@if` é uma condicional — "se existirem erros de validação, mostre este bloco vermelho". `$errors->first()` mostra a primeira mensagem de erro.

```html
@if(Route::has('password.request'))
    <a href="{{ route('password.request') }}">Esqueceu a senha?</a>
@endif
```
↑ Só mostra o link "Esqueceu a senha?" se essa rota existir no sistema. É uma verificação de segurança — se a funcionalidade não existisse, o link não aparecia.

---

### 11.2 — A migração de alunos (`database/migrations/..._create_alunos_table.php`)

```php
public function up(): void
{
    Schema::create('alunos', function (Blueprint $table) {
```
↑ `up()` é chamado quando você roda `php artisan migrate`. `Schema::create('alunos', ...)` cria uma nova tabela chamada `alunos` no banco de dados.

```php
        $table->id();
```
↑ Cria a coluna `id` — um número inteiro que aumenta automaticamente (1, 2, 3...). É a chave primária que identifica cada aluno de forma única.

```php
        $table->string('nome', 150);
```
↑ Cria a coluna `nome` que guarda texto de até 150 caracteres. `string` = `VARCHAR(150)` no MySQL.

```php
        $table->string('ra', 30)->unique();
```
↑ Coluna `ra` (Registro do Aluno) de até 30 caracteres. `->unique()` garante que dois alunos não podem ter o mesmo RA — o banco de dados vai rejeitar se tentar inserir um RA repetido.

```php
        $table->foreignId('turma_id')->constrained('turmas')->cascadeOnDelete();
```
↑ Esta é a linha mais importante para entender relacionamentos:
- `foreignId('turma_id')` → cria uma coluna `turma_id` que guarda um número inteiro
- `->constrained('turmas')` → esse número DEVE existir na coluna `id` da tabela `turmas` (chave estrangeira)
- `->cascadeOnDelete()` → se a turma for excluída, todos os alunos dela são excluídos automaticamente

```php
        $table->boolean('active')->default(true);
```
↑ Coluna `active` que só aceita `true` (ativo) ou `false` (inativo). Começa como `true` por padrão.

```php
        $table->timestamps();
```
↑ Cria automaticamente duas colunas: `created_at` (quando foi criado) e `updated_at` (quando foi modificado por último). O Laravel preenche essas colunas sozinho.

```php
public function down(): void
{
    Schema::dropIfExists('alunos');
}
```
↑ `down()` é o contrário de `up()` — é chamado se você quiser desfazer a migração. Aqui simplesmente apaga a tabela.

---

### 11.3 — O Controller do dashboard admin (`app/Http/Controllers/Admin/DashboardController.php`)

```php
class DashboardController extends Controller
{
    public function index()
    {
```
↑ `index()` é o método chamado quando alguém acessa `/admin/dashboard`. O Laravel automaticamente associa o método `index` à URL principal de um recurso.

```php
        return view('admin.dashboard', [
            'totalAlunos'      => Aluno::where('active', true)->count(),
            'totalProfessores' => User::where('role', 'professor')->where('active', true)->count(),
            'totalEmpresas'    => Empresa::count(),
            'totalTurmas'      => Turma::count(),
        ]);
```
↑ `view('admin.dashboard', [...])` diz: "renderize o arquivo `resources/views/admin/dashboard.blade.php`" e passe essas variáveis para ele".

Cada linha busca dados do banco:
- `Aluno::where('active', true)->count()` → conta quantos alunos têm `active = true`
- `User::where('role', 'professor')->count()` → conta usuários com role = professor
- `Empresa::count()` → conta todas as empresas
- `Turma::count()` → conta todas as turmas

Essas variáveis chegam na view como `$totalAlunos`, `$totalProfessores`, etc.

---

### 11.4 — A view do dashboard admin (`resources/views/admin/dashboard.blade.php`)

```html
<p class="text-3xl font-bold text-gray-800">{{ $totalAlunos }}</p>
<p class="text-sm text-gray-500">Alunos ativos</p>
```
↑ `{{ $totalAlunos }}` exibe o valor que o Controller enviou. As chaves duplas `{{ }}` significam "imprima o valor desta variável PHP aqui". Se `$totalAlunos` for `47`, aparece "47" na tela.

```html
<div class="border-l-4 border-senai">
```
↑ `border-l-4` = borda de 4px apenas no lado esquerdo. `border-senai` = cor vermelha do SENAI. Isso cria aquela listinha colorida do lado do cartão.

```html
<a href="{{ route('admin.turmas.index') }}" class="hover:border-senai hover:bg-red-50 ...">
    Turmas
</a>
```
↑ `route('admin.turmas.index')` gera automaticamente a URL `/admin/turmas`. Em vez de escrever a URL na mão, usa-se o nome da rota — se a URL mudar no futuro, só precisa mudar no `routes/web.php`.

---

### 11.5 — O componente de chamada PHP (`app/Livewire/Professor/Chamada.php`)

```php
public function mount(Aula $aula): void
{
    $this->aula = $aula->load(['turma.alunos' => fn ($q) => $q->where('active', true)]);
```
↑ `mount()` é chamado uma única vez quando o componente é carregado, como um construtor. `->load(...)` carrega os dados relacionados em uma única consulta SQL (alunos ativos da turma desta aula).

```php
    $chamadas = ChamadaModel::where('aula_id', $aula->id)
        ->pluck('status', 'aluno_id')
        ->toArray();
```
↑ Busca todos os registros de presença desta aula. `->pluck('status', 'aluno_id')` cria um array associativo: `[3 => 'presente', 7 => 'falta', 12 => 'presente']` onde o número é o ID do aluno.

```php
    foreach ($this->aula->turma->alunos as $aluno) {
        $this->statuses[$aluno->id] = $chamadas[$aluno->id] ?? 'falta';
    }
```
↑ Para cada aluno da turma: pega o status do banco de dados (`$chamadas[$aluno->id]`). O `?? 'falta'` significa "se não existir registro, usa 'falta' como padrão".

```php
public function toggle(int $alunoId): void
{
    $novo = $this->statuses[$alunoId] === 'presente' ? 'falta' : 'presente';
```
↑ Lógica do toggle: se atualmente é 'presente', muda para 'falta'. Se é 'falta', muda para 'presente'.

```php
    ChamadaModel::updateOrCreate(
        ['aula_id' => $this->aula->id, 'aluno_id' => $alunoId],
        ['status' => $novo, 'updated_at' => now()]
    );
```
↑ `updateOrCreate` = "tenta encontrar um registro com essas condições (aula_id + aluno_id). Se encontrar, atualiza o status. Se não encontrar, cria um novo". Isso evita duplicatas no banco de dados.

---

### 11.6 — A view da chamada: o toggle interativo (`resources/views/livewire/professor/chamada.blade.php`)

```html
<div wire:key="aluno-{{ $aluno->id }}"
     x-data="{ presente: {{ $status === 'presente' ? 'true' : 'false' }} }">
```
↑
- `wire:key="aluno-3"` → diz ao Livewire que este elemento corresponde ao aluno com ID 3. Necessário para o Livewire saber qual elemento atualizar quando a lista muda.
- `x-data="{ presente: true }"` → cria uma "mini memória" local para este aluno. `presente` é uma variável que só existe neste bloco HTML. O PHP injeta `true` ou `false` conforme o status atual do banco.

```html
<div :class="presente ? 'bg-green-500' : 'bg-red-400'">
```
↑ O `:class` (com dois pontos) é Alpine.js reativo: "se `presente` for verdadeiro, aplica `bg-green-500` (verde); caso contrário, aplica `bg-red-400` (vermelho)". Atualiza instantaneamente quando `presente` muda.

```html
<button @click="presente = !presente; $wire.toggle({{ $aluno->id }})">
```
↑ Quando o botão é clicado, duas coisas acontecem simultaneamente:
1. `presente = !presente` → inverte o valor visual imediatamente (verde ↔ vermelho) — isso é instantâneo
2. `$wire.toggle(3)` → chama o método `toggle(3)` no PHP/Livewire para salvar no banco — isso vai ao servidor

A separação é intencional: o visual muda na hora (sem esperar o servidor), enquanto o banco é atualizado em segundo plano.

```html
<span :class="presente ? 'translate-x-8' : 'translate-x-1'"></span>
```
↑ O "bolinho" branco do toggle. `translate-x-8` = mover 2rem para a direita (posição "ligado"). `translate-x-1` = quase sem movimento (posição "desligado"). Isso cria o efeito de deslizar.

---

### 11.7 — O arquivo de rotas (`routes/web.php`)

```php
Route::get('/', fn () => redirect()->route('login'));
```
↑ Quando alguém acessa a URL raiz (`/`), redireciona automaticamente para a tela de login.

```php
Route::middleware(['auth', 'role:admin'])
     ->prefix('admin')
     ->name('admin.')
     ->group(function () {
```
↑ Define um grupo de rotas com:
- `middleware(['auth', 'role:admin'])` → só usuários logados E com role=admin podem acessar
- `->prefix('admin')` → todas as URLs deste grupo começam com `/admin/`
- `->name('admin.')` → todas as rotas terão nome começando com `admin.` (ex: `admin.dashboard`)

```php
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
```
↑ Quando alguém acessa `GET /admin/dashboard`, o Laravel chama o método `index()` da classe `AdminDashboard`. O nome completo da rota fica `admin.dashboard`.

```php
    Route::resource('turmas', TurmasController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
```
↑ `Route::resource` cria múltiplas rotas de uma vez (CRUD completo). O `->only([...])` limita quais rotas criar:
- `index` → `GET /admin/turmas` (listar)
- `show` → `GET /admin/turmas/{id}` (ver detalhes)
- `store` → `POST /admin/turmas` (criar)
- `update` → `PUT /admin/turmas/{id}` (editar)
- `destroy` → `DELETE /admin/turmas/{id}` (excluir)

---

### 11.8 — O JavaScript principal (`resources/js/app.js`)

```js
import './bootstrap';
```
↑ Importa o arquivo `bootstrap.js` que configura o Axios (biblioteca para fazer requisições HTTP em JavaScript).

```js
document.addEventListener('alpine:init', () => {
```
↑ "Quando o Alpine.js terminar de inicializar, execute este código". É como esperar o sistema estar pronto antes de configurar algo.

```js
    Alpine.store('dialogo', {
        aberto: false,
        titulo: '',
        mensagem: '',
        _acao: null,
```
↑ `Alpine.store` cria uma variável global compartilhada por todas as páginas. O `dialogo` guarda o estado do modal de confirmação (se está aberto/fechado, título, mensagem, e a ação a executar).

```js
        perguntar(titulo, mensagem, textoBotao, acao) {
            this.titulo   = titulo;
            this.mensagem = mensagem;
            this._acao    = acao;
            this.aberto   = true;
        },
```
↑ Método chamado quando alguém clica em "Excluir". Preenche o modal com o título e mensagem corretos, salva a função que deve rodar se o usuário confirmar, e abre o modal (`aberto = true`).

```js
        confirmar() {
            if (this._acao) this._acao();
            this.aberto = false;
        },
```
↑ Quando o usuário clica em "Confirmar" no modal: executa a ação salva (`this._acao()`, ex: chamar `$wire.excluir(5)`) e fecha o modal.

---

## 12. De onde cada tela puxa seus dados

Esta seção explica o caminho completo que os dados percorrem — do banco de dados até aparecer na tela.

---

### Dashboard do Administrador

**Pergunta:** "De onde vêm os números nos cartões do dashboard?"

```
1. Usuário acessa /admin/dashboard
         ↓
2. routes/web.php encontra: Route::get('/dashboard', [AdminDashboard::class, 'index'])
         ↓
3. Laravel chama AdminDashboard::index() em app/Http/Controllers/Admin/DashboardController.php
         ↓
4. O Controller consulta o banco de dados:
   - Aluno::where('active', true)->count()          → SELECT COUNT(*) FROM alunos WHERE active = 1
   - User::where('role', 'professor')->count()       → SELECT COUNT(*) FROM users WHERE role = 'professor'
   - Empresa::count()                                → SELECT COUNT(*) FROM empresas
   - Turma::count()                                  → SELECT COUNT(*) FROM turmas
         ↓
5. O Controller passa os resultados para a view:
   return view('admin.dashboard', ['totalAlunos' => 47, 'totalProfessores' => 3, ...])
         ↓
6. A view exibe:
   <p>{{ $totalAlunos }}</p>  →  <p>47</p>
```

---

### Tela de Chamada do Professor

**Pergunta:** "Como o sistema sabe quais alunos estão presentes quando o professor abre a chamada?"

```
1. Professor clica em "Fazer Chamada" de uma aula
         ↓
2. Navegador acessa /professor/aulas/5 (onde 5 é o ID da aula)
         ↓
3. routes/web.php: Route::get('/aulas/{aula}', [...'chamada'])
         ↓
4. Laravel carrega a Aula com ID 5 do banco automaticamente
         ↓
5. Livewire\Professor\Chamada::mount($aula) é chamado:
   - Carrega os alunos ativos da turma desta aula
     → SELECT * FROM alunos WHERE turma_id = 2 AND active = 1
   - Carrega os registros de chamada existentes desta aula
     → SELECT aluno_id, status FROM chamadas WHERE aula_id = 5
   - Combina: para cada aluno, verifica se tem registro. Se não tem, usa 'falta'
         ↓
6. A view recebe $statuses = [1 => 'presente', 2 => 'falta', 3 => 'presente']
         ↓
7. Para cada aluno, exibe o toggle verde ou vermelho conforme o status
```

**Quando o professor clica no toggle:**
```
1. @click="presente = !presente; $wire.toggle(2)"
         ↓
2. Alpine.js muda o toggle visualmente (INSTANTÂNEO — sem ir ao servidor)
         ↓
3. $wire.toggle(2) envia requisição ao servidor em background
         ↓
4. Chamada::updateOrCreate(
       ['aula_id' => 5, 'aluno_id' => 2],
       ['status' => 'presente']
   )
   → INSERT ou UPDATE na tabela chamadas
         ↓
5. Banco de dados atualizado ✓
```

---

### Dashboard da Empresa

**Pergunta:** "Como o sistema sabe quais alunos mostrar para a empresa?"

```
1. Empresa faz login → redirecionada para /empresa/dashboard
         ↓
2. Empresa\DashboardController::index() é chamado
         ↓
3. O controller identifica qual empresa está logada:
   $empresa = auth()->user()->empresa
   → SELECT * FROM empresas WHERE user_id = [id do usuário logado]
         ↓
4. Busca os alunos vinculados a esta empresa:
   $empresa->alunos()->with(['chamadas', 'turma'])->get()
   → SELECT alunos.* FROM alunos
     JOIN empresa_aluno ON alunos.id = empresa_aluno.aluno_id
     WHERE empresa_aluno.empresa_id = [id da empresa]
         ↓
5. Para cada aluno, calcula o percentual de presença
         ↓
6. Os dados são convertidos para JSON e enviados para Alpine.js na view:
   $alunosJson = Js::from($alunos)
         ↓
7. Na tela, Alpine.js filtra os alunos conforme o usuário digita na busca,
   sem precisar ir ao servidor — tudo no navegador
```

---

### Histórico de um Aluno (visão empresa)

**Pergunta:** "Quando a empresa clica num aluno, de onde vêm as informações de presença?"

```
1. Empresa clica no aluno "Maria Oliveira" (ID 7)
         ↓
2. Navegador acessa /empresa/alunos/7/historico
         ↓
3. Empresa\DashboardController::historico($aluno) é chamado
         ↓
4. Verifica segurança: o aluno 7 pertence a esta empresa?
   Se não, retorna erro 403 (Acesso negado)
         ↓
5. Se sim, Livewire\Empresa\HistoricoAluno carrega:
   → SELECT aulas.data, aulas.descricao, chamadas.status
     FROM chamadas
     JOIN aulas ON chamadas.aula_id = aulas.id
     WHERE chamadas.aluno_id = 7
     ORDER BY aulas.data DESC
         ↓
6. A view exibe a tabela com data, descrição da aula e Presente/Falta
7. Calcula: total = 10 aulas, presentes = 8, percentual = 80%
```

---

## 13. Como o sistema protege as páginas

**Pergunta:** "O que impede um professor de acessar as páginas do admin?"

O sistema usa **middleware** para proteger cada grupo de páginas.

### O que é middleware?

Middleware é um "fiscal" que intercepta cada requisição antes de ela chegar ao Controller. Se o usuário não passar na fiscalização, é bloqueado.

```
Usuário acessa /admin/dashboard
        ↓
┌─────────────────────────────────┐
│  MIDDLEWARE 1: auth             │
│  "Está logado?"                 │
│  Não → redireciona para /login  │
│  Sim → passa para o próximo     │
└──────────────┬──────────────────┘
               ↓
┌─────────────────────────────────┐
│  MIDDLEWARE 2: role:admin       │
│  "Tem role = admin?"            │
│  Não → redireciona para /login  │
│  Sim → passa para o Controller  │
└──────────────┬──────────────────┘
               ↓
        AdminDashboardController::index()
               ↓
        Renderiza a página ✓
```

### Como está configurado no código

Em `routes/web.php`:
```php
Route::middleware(['auth', 'role:admin'])
     ->prefix('admin')
     ->group(function () {
    // Todas as rotas aqui só são acessíveis por admin logado
});

Route::middleware(['auth', 'role:professor'])
     ->prefix('professor')
     ->group(function () {
    // Todas as rotas aqui só são acessíveis por professor logado
});
```

Se um professor tentar acessar `/admin/dashboard`:
1. O middleware `auth` passa (está logado ✓)
2. O middleware `role:admin` verifica: `auth()->user()->role === 'admin'`? → **Não**, é `professor` → bloqueia e redireciona

### O campo `role` na tabela users

```
users
┌────┬──────────────────┬──────────────────────┬───────────┐
│ id │ name             │ email                │ role      │
├────┼──────────────────┼──────────────────────┼───────────┤
│  1 │ Administrador    │ admin@senai.br       │ admin     │
│  2 │ Prof. João       │ joao@senai.br        │ professor │
│  3 │ Tech Solutions   │ empresa@tech.com.br  │ empresa   │
└────┴──────────────────┴──────────────────────┴───────────┘
```

O campo `role` determina o que cada usuário pode ver. Quando o login é feito, o Laravel sabe qual role aquele usuário tem e usa isso em toda a navegação.

---

## 14. O padrão MVC na prática — exemplo completo

**Pergunta:** "O que é MVC e como funciona no seu projeto?"

MVC = **M**odel (Modelo) + **V**iew (Visão) + **C**ontroller (Controlador)

É uma forma de organizar o código separando três responsabilidades diferentes:

| Sigla | Nome | Responsabilidade | Pasta no projeto |
|---|---|---|---|
| M | Model | Cuidar dos dados (banco de dados) | `app/Models/` |
| V | View | Cuidar do visual (HTML) | `resources/views/` |
| C | Controller | Cuidar da lógica (conecta M e V) | `app/Http/Controllers/` |

### Exemplo concreto: Exibir o dashboard do admin

**Etapa 1 — Usuário (navegador)**
Usuário digita `senaifrequencias.test/admin/dashboard` e pressiona Enter.

**Etapa 2 — Rotas (`routes/web.php`)**
```php
Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
```
O Laravel lê este arquivo e descobre: "para a URL `/admin/dashboard`, chame o método `index` da classe `AdminDashboard`".

**Etapa 3 — Controller (`app/Http/Controllers/Admin/DashboardController.php`)**
```php
public function index()
{
    return view('admin.dashboard', [
        'totalAlunos' => Aluno::where('active', true)->count(),
        'totalTurmas' => Turma::count(),
    ]);
}
```
O Controller é a "cola". Ele:
1. Pede ao Model que busque os dados no banco
2. Repassa os dados para a View

**Etapa 4 — Model (`app/Models/Aluno.php`)**
```php
Aluno::where('active', true)->count()
```
O Laravel traduz isso para SQL: `SELECT COUNT(*) FROM alunos WHERE active = 1`
O banco retorna: `47`

**Etapa 5 — View (`resources/views/admin/dashboard.blade.php`)**
```html
<p class="text-3xl font-bold">{{ $totalAlunos }}</p>
```
A view recebe `$totalAlunos = 47` e exibe `47` na tela.

**Etapa 6 — Resposta**
O HTML final é enviado de volta ao navegador e o usuário vê a tela.

---

### Diagrama do MVC

```
USUÁRIO
  │  acessa /admin/dashboard
  ↓
ROUTE (routes/web.php)
  │  "quem atende essa URL?"
  ↓
CONTROLLER (DashboardController)
  │  "preciso de dados"           │  "tenho os dados, vou montar a tela"
  ↓                               ↑
MODEL (Aluno, Turma...)  →→→  VIEW (dashboard.blade.php)
  │                               │
  ↓                               ↓
BANCO DE DADOS MySQL          HTML enviado ao navegador
```

Cada peça tem sua função. Se precisar mudar o visual, mexe só na View. Se precisar mudar como os dados são buscados, mexe no Model ou Controller. As partes não se misturam — isso é a beleza do MVC.

---

## 15. A animação de transição do login

**O que é?** Quando o usuário faz login com sucesso, em vez de a página simplesmente trocar de forma abrupta, acontece uma animação suave: o card de login sobe até desaparecer pelo topo da tela, o fundo vai gradualmente ficando na mesma cor do painel principal, e então o dashboard aparece — como se uma cortina tivesse sido puxada para revelar a próxima tela.

---

### O problema que precisava ser resolvido

Um formulário HTML normal funciona assim:
1. Usuário clica "Entrar"
2. Navegador envia os dados para o servidor (POST)
3. Servidor verifica a senha
4. Se certo: redireciona para o dashboard — **página troca abruptamente**
5. Se errado: volta para o login com mensagem de erro

Para adicionar animação antes da troca de página, precisamos **interceptar** o passo 2 — ou seja, impedir que o navegador envie o formulário imediatamente e fazer isso nós mesmos via JavaScript.

---

### A solução: AJAX + detecção de resultado

**O que é AJAX?** É uma forma de o JavaScript enviar e receber dados do servidor **sem recarregar a página**. O JavaScript manda o pedido "nos bastidores", o servidor responde, e o JavaScript decide o que fazer com a resposta.

```
Sem AJAX (normal):
  [Clique em "Entrar"] → [Navegador vai pro servidor] → [Servidor redireciona] → [Página nova]
  Problema: não dá para animar antes da troca de página.

Com AJAX (nossa solução):
  [Clique em "Entrar"]
        ↓
  [JavaScript intercepta] → [fetch() envia as credenciais] → [Servidor responde]
        ↓
  SE login OK → [Animação] → [JavaScript navega para o dashboard]
  SE login FALHOU → [JavaScript navega para /login com erros]
```

---

### Como o código funciona — passo a passo

#### Passo 1: Interceptar o submit do formulário

```javascript
form.addEventListener('submit', async function (e) {
    e.preventDefault(); // PAUSA o envio normal do formulário
```

`e.preventDefault()` diz ao navegador: "não faça a ação padrão deste evento". Para um formulário, a ação padrão é enviar os dados e recarregar a página. Ao pausar isso, o JavaScript toma o controle.

#### Passo 2: Enviar as credenciais via fetch()

```javascript
const formData = new FormData(form); // coleta todos os campos do formulário

const response = await fetch(form.action, {
    method:   'POST',    // método HTTP: enviar dados
    body:     formData,  // os dados do formulário (e-mail + senha + token CSRF)
    redirect: 'follow',  // segue automaticamente qualquer redirecionamento do servidor
});

urlFinal = response.url; // URL onde o servidor nos mandou após processar
```

`fetch()` é como um mensageiro: vai até o servidor, entrega os dados, e traz a resposta de volta. `await` significa "espere a resposta antes de continuar" (sem travar o navegador).

O `redirect: 'follow'` é a chave: o Laravel redireciona após o login, e o fetch segue esse redirecionamento. No final, `response.url` contém a URL destino.

#### Passo 3: Detectar se o login foi bem-sucedido

```javascript
const loginFalhou = !urlFinal || urlFinal.includes('/login');
```

Esta linha é inteligente:
- Se o login **falhou**: o Laravel redireciona de volta para `/login` (mesma página, com erros)
- Se o login **foi bem-sucedido**: o Laravel redireciona para `/admin/dashboard` ou `/professor/dashboard`

Logo: se a URL final **contém `/login`**, o login falhou. Se não contém, foi bem-sucedido!

#### Passo 4: Executar a animação (só em caso de sucesso)

```javascript
function animarSaida(callback) {
    // Fundo vai ficando da cor do painel (cinza claro #F0F2F5)
    overlay.style.opacity = '1';

    // Card sobe para fora da tela
    card.style.transform = 'translateY(-110vh)'; // move 110% da altura da tela para cima
    card.style.opacity   = '0';                  // some gradualmente

    setTimeout(callback, 650); // após 650ms, executa a próxima ação
}
```

`callback` é uma função passada como parâmetro — quando chamamos `animarSaida(função)`, a `função` é executada automaticamente após a animação terminar. Isso chama `window.location.href = urlFinal` para navegar para o dashboard.

#### Passo 5: A transição final

O overlay tem exatamente a mesma cor de fundo do painel (`#F0F2F5`). Quando o navegador carrega o dashboard, o fundo dele já tem essa cor — então não há nenhum flash ou salto visual. A transição parece contínua.

O dashboard tem um leve `fade-in` de 0.3s para cobrir qualquer variação visual de carregamento.

---

### Diagrama da animação completa

```
[Usuário clica "Entrar"]
        │
        ▼
[JavaScript pausa o submit]
        │
        ▼
[fetch() envia e-mail + senha ao servidor]
        │
        ├─── Servidor: credenciais ERRADAS ───▶ redireciona para /login
        │                                           │
        │                                           ▼
        │                                 [Navega para /login com erro]
        │                                 [Mensagem "Credenciais inválidas" aparece]
        │
        └─── Servidor: credenciais CORRETAS ──▶ redireciona para /admin/dashboard
                                                    │
                                                    ▼
                                         [ANIMAÇÃO INICIA]
                                         Card sobe (500ms) ┐
                                         Fundo vira cinza  ┘ simultâneos
                                                    │
                                                    ▼  após 650ms
                                         [window.location.href = '/admin/dashboard']
                                                    │
                                                    ▼
                                         [Dashboard aparece com fade-in (300ms)]
                                         [Transição parece contínua ✓]
```

---

### Por que essa animação é um detalhe importante?

Sistemas profissionais (Gmail, GitHub, Figma, Notion) usam transições de página para tornar a experiência mais suave e "polida". Não é apenas estética — pesquisas de UX mostram que transições suaves fazem o usuário perceber o sistema como mais rápido e mais confiável, mesmo quando o tempo de carregamento é o mesmo.

Implementar isso em um projeto escolar demonstra:
- Compreensão de eventos JavaScript (`addEventListener`, `preventDefault`)
- Conhecimento de requisições assíncronas (`fetch`, `async/await`)
- Noção de UX (experiência do usuário) e CSS de animação
- Integração entre front-end e back-end (detectar o redirect do Laravel via JS)

---

*Desenvolvido com Laravel 13.8, PHP 8.3, MySQL 8.4, Livewire 4.3, Alpine.js e Tailwind CSS v4.*
