<?php

// Namespace: este controller fica em app/Http/Controllers/Empresa/
namespace App\Http\Controllers\Empresa;

// Importações
use App\Http\Controllers\Controller; // classe base do Laravel
use App\Models\Aluno;                 // Model da tabela "alunos"
use Illuminate\Http\Request;          // dá acesso ao usuário logado e dados da requisição

// DashboardController da Empresa — gerencia todas as telas da área da empresa
class DashboardController extends Controller
{
    // Exibe o dashboard principal da empresa (lista de alunos vinculados)
    // URL: GET /empresa/dashboard
    public function index(Request $request)
    {
        // Busca a empresa vinculada ao usuário logado
        // ->with('alunos.turma') carrega os alunos e as turmas deles em uma única consulta (otimização)
        // firstOrFail() → se não encontrar empresa, retorna erro 404
        $empresa = $request->user()->empresa()->with('alunos.turma')->firstOrFail();

        // compact('empresa') → ['empresa' => $empresa]
        // A view recebe $empresa com os alunos e turmas já carregados
        return view('empresa.dashboard', compact('empresa'));
    }

    // Exibe a lista de aulas relevantes para esta empresa
    // URL: GET /empresa/aulas
    public function aulas(Request $request)
    {
        // A view usa Livewire para carregar as aulas — não precisamos passar dados aqui
        return view('empresa.aulas');
    }

    // Exibe o histórico de presença de um aluno específico
    // URL: GET /empresa/alunos/{aluno}/historico
    // O Laravel busca automaticamente o Aluno pelo ID na URL e injeta como $aluno
    public function historico(Request $request, Aluno $aluno)
    {
        // Busca a empresa com todos os alunos vinculados carregados
        $empresa = $request->user()->empresa()->with('alunos')->firstOrFail();

        // Verificação de segurança: este aluno pertence a esta empresa?
        // ->contains($aluno->id) verifica se o ID está na lista de alunos da empresa
        // abort_if → se a condição for verdadeira (aluno NÃO está na empresa), retorna erro 403 (Proibido)
        abort_if(! $empresa->alunos->contains($aluno->id), 403, 'Este aluno não pertence à sua empresa.');

        // Passa a empresa e o aluno para a view
        // A view usa Livewire para carregar o histórico detalhado de chamadas
        return view('empresa.historico', compact('empresa', 'aluno'));
    }
}
