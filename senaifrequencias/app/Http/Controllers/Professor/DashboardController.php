<?php

// Namespace: este controller fica em app/Http/Controllers/Professor/
namespace App\Http\Controllers\Professor;

// Importações
use App\Http\Controllers\Controller; // classe base do Laravel
use App\Models\Aula;                  // Model da tabela "aulas"
use Illuminate\Http\Request;          // representa a requisição HTTP (dá acesso ao usuário logado, dados do form, etc.)

// DashboardController do Professor — gerencia as telas da área do professor
class DashboardController extends Controller
{
    // Exibe o dashboard principal do professor
    // URL: GET /professor/dashboard
    public function index()
    {
        // Renderiza a view professor/dashboard.blade.php
        // O dashboard carrega os dados via Livewire (TurmaDashboard), então não precisa passar variáveis aqui
        return view('professor.dashboard');
    }

    // Exibe a tela de chamada de uma aula específica
    // URL: GET /professor/aulas/{aula}
    // O Laravel automaticamente busca a Aula pelo ID na URL e injeta como objeto $aula
    public function chamada(Request $request, Aula $aula)
    {
        // Busca a turma vinculada a este professor
        // firstOrFail() → se o professor não tiver turma, lança erro 404
        $turma = $request->user()->turmas()->firstOrFail();

        // Verificação de segurança: a aula pertence à turma deste professor?
        // abort_if(condição, código HTTP, mensagem) → se a condição for verdadeira, aborta com erro
        // Impede que um professor acesse a chamada de outro professor
        abort_if($aula->turma_id !== $turma->id, 403, 'Esta aula não pertence à sua turma.');

        // compact('aula') é um atalho PHP para ['aula' => $aula]
        // Passa a variável $aula para a view ficar disponível como $aula
        return view('professor.chamada', compact('aula'));
    }
}
