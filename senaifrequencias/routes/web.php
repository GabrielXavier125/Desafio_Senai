<?php

use App\Http\Controllers\Admin\AlunosController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\EmpresasController;
use App\Http\Controllers\Admin\ProfessoresController;
use App\Http\Controllers\Admin\TurmasController;
use App\Http\Controllers\Empresa\DashboardController as EmpresaDashboard;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\Professor\DashboardController as ProfessorDashboard;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    Route::resource('turmas',      TurmasController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
    Route::resource('alunos',      AlunosController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('professores', ProfessoresController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('empresas',    EmpresasController::class)->only(['index', 'show', 'store', 'update', 'destroy']);

    Route::get('/turmas/{turma}/exportar', [ExportController::class, 'adminTurma'])->name('turmas.exportar');
});

// Professor routes
Route::middleware(['auth', 'role:professor'])->prefix('professor')->name('professor.')->group(function () {
    Route::get('/dashboard',    [ProfessorDashboard::class, 'index'])->name('dashboard');
    Route::get('/aulas/{aula}', [ProfessorDashboard::class, 'chamada'])->name('aulas.chamada');
    Route::get('/exportar',     [ExportController::class, 'professorTurma'])->name('exportar');
});

// Empresa routes
Route::middleware(['auth', 'role:empresa'])->prefix('empresa')->name('empresa.')->group(function () {
    Route::get('/dashboard',                [EmpresaDashboard::class, 'index'])->name('dashboard');
    Route::get('/aulas',                    [EmpresaDashboard::class, 'aulas'])->name('aulas');
    Route::get('/alunos/{aluno}/historico', [EmpresaDashboard::class, 'historico'])->name('alunos.historico');
});

// Shared auth routes (all roles)
Route::middleware(['auth'])->group(function () {
    Route::get('/perfil/senha', fn () => view('perfil.senha'))->name('perfil.senha');
});

require __DIR__.'/auth.php';
