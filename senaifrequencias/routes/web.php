<?php

// ─── Importações ───────────────────────────────────────────────────────────
// Traz os Controllers que serão usados neste arquivo de rotas
use App\Http\Controllers\Admin\AlunosController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;    // "as" cria um apelido para evitar conflito de nomes
use App\Http\Controllers\Admin\EmpresasController;
use App\Http\Controllers\Admin\ProfessoresController;
use App\Http\Controllers\Admin\TurmasController;
use App\Http\Controllers\Empresa\DashboardController as EmpresaDashboard; // mesmo nome, pasta diferente — usa apelido
use App\Http\Controllers\ExportController;
use App\Http\Controllers\Professor\DashboardController as ProfessorDashboard;
use Illuminate\Support\Facades\Route; // classe do Laravel que permite definir rotas

// ─── Rota raiz ─────────────────────────────────────────────────────────────
// Quando o usuário acessa "/" (a URL raiz), redireciona para a tela de login
Route::get('/', fn () => redirect()->route('login'));
// "fn () => ..." é uma função anônima — equivale a function() { return redirect()... }
// route('login') gera a URL da rota com nome 'login' automaticamente

// ─── Rotas do Administrador ────────────────────────────────────────────────
// middleware(['auth', 'role:admin']): só entra quem está logado E tem role = 'admin'
// prefix('admin'): todas as URLs deste grupo começam com /admin/
// name('admin.'): todos os nomes de rota deste grupo começam com 'admin.'
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // GET /admin/dashboard → chama AdminDashboard::index() → nome: 'admin.dashboard'
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Route::resource cria múltiplas rotas de uma vez (CRUD completo)
    // ->only([...]) limita quais rotas criar (sem criar as que não precisamos)
    Route::resource('turmas',      TurmasController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
    //   index   → GET  /admin/turmas         → listar turmas
    //   show    → GET  /admin/turmas/{id}    → ver aulas de uma turma
    //   store   → POST /admin/turmas         → criar nova turma
    //   update  → PUT  /admin/turmas/{id}    → editar turma existente
    //   destroy → DEL  /admin/turmas/{id}    → excluir turma

    Route::resource('alunos',      AlunosController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('professores', ProfessoresController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('empresas',    EmpresasController::class)->only(['index', 'show', 'store', 'update', 'destroy']);

    // Rota especial para exportar frequência de uma turma em CSV
    // {turma} é um parâmetro dinâmico — o Laravel busca a turma pelo ID automaticamente
    Route::get('/turmas/{turma}/exportar', [ExportController::class, 'adminTurma'])->name('turmas.exportar');
});

// ─── Rotas do Professor ────────────────────────────────────────────────────
// Só acessível por usuários logados com role = 'professor'
Route::middleware(['auth', 'role:professor'])->prefix('professor')->name('professor.')->group(function () {

    // Dashboard principal do professor (lista suas aulas)
    Route::get('/dashboard',    [ProfessorDashboard::class, 'index'])->name('dashboard');

    // Tela de chamada de uma aula específica
    // {aula} é o ID da aula — o Laravel carrega o objeto Aula automaticamente
    Route::get('/aulas/{aula}', [ProfessorDashboard::class, 'chamada'])->name('aulas.chamada');

    // Exportação CSV da frequência da turma do professor logado
    Route::get('/exportar',     [ExportController::class, 'professorTurma'])->name('exportar');
});

// ─── Rotas da Empresa ──────────────────────────────────────────────────────
// Só acessível por usuários logados com role = 'empresa'
Route::middleware(['auth', 'role:empresa'])->prefix('empresa')->name('empresa.')->group(function () {

    // Dashboard da empresa: lista os alunos vinculados com percentual de presença
    Route::get('/dashboard',                [EmpresaDashboard::class, 'index'])->name('dashboard');

    // Lista de aulas relevantes para os alunos da empresa
    Route::get('/aulas',                    [EmpresaDashboard::class, 'aulas'])->name('aulas');

    // Histórico detalhado de presença de um aluno específico
    // {aluno} é o ID do aluno — verifica se pertence à empresa antes de mostrar
    Route::get('/alunos/{aluno}/historico', [EmpresaDashboard::class, 'historico'])->name('alunos.historico');
});

// ─── Rotas compartilhadas (todos os perfis logados) ───────────────────────
Route::middleware(['auth'])->group(function () {
    // Página para alterar a própria senha — disponível para admin, professor e empresa
    Route::get('/perfil/senha', fn () => view('perfil.senha'))->name('perfil.senha');
});

// Inclui o arquivo de rotas de autenticação (login, logout, recuperar senha)
// Este arquivo foi gerado pelo Laravel Breeze e contém as rotas padrão de auth
require __DIR__.'/auth.php';
