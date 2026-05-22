<?php

// Namespace: este controller fica em app/Http/Controllers/Admin/
namespace App\Http\Controllers\Admin;

// Importações das classes que este controller vai usar
use App\Http\Controllers\Controller; // classe base do Laravel — todo controller herda dela
use App\Models\Aluno;    // Model que representa a tabela "alunos"
use App\Models\Empresa;  // Model que representa a tabela "empresas"
use App\Models\Turma;    // Model que representa a tabela "turmas"
use App\Models\User;     // Model que representa a tabela "users"

// DashboardController — responsável pela tela inicial do administrador
// URL: GET /admin/dashboard
// Só exibe contagens — não cria, edita ou exclui nada
class DashboardController extends Controller
{
    // Método index() é chamado quando o admin acessa /admin/dashboard
    // Convenção Laravel: index = "listar / mostrar tela principal"
    public function index()
    {
        // view('admin.dashboard', [...]) faz duas coisas:
        // 1. Diz ao Laravel para renderizar o arquivo resources/views/admin/dashboard.blade.php
        // 2. Passa as variáveis dentro do array para a view poder exibir
        return view('admin.dashboard', [

            // Conta quantos alunos têm active = true no banco
            // SQL gerado: SELECT COUNT(*) FROM alunos WHERE active = 1 AND deleted_at IS NULL
            'totalAlunos' => Aluno::where('active', true)->count(),

            // Conta usuários que são professores e estão ativos
            // SQL: SELECT COUNT(*) FROM users WHERE role = 'professor' AND active = 1
            'totalProfessores' => User::where('role', 'professor')->where('active', true)->count(),

            // Conta todas as empresas cadastradas
            // SQL: SELECT COUNT(*) FROM empresas
            'totalEmpresas' => Empresa::count(),

            // Conta todas as turmas (não excluídas via soft delete)
            // SQL: SELECT COUNT(*) FROM turmas WHERE deleted_at IS NULL
            'totalTurmas' => Turma::count(),
        ]);
        // Na view, esses valores chegam como: $totalAlunos, $totalProfessores, etc.
    }
}
