<?php

namespace App\Http\Controllers\Empresa;

use App\Http\Controllers\Controller;
use App\Models\Aluno;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $empresa = $request->user()->empresa()->with('alunos.turma')->firstOrFail();

        return view('empresa.dashboard', compact('empresa'));
    }

    public function aulas(Request $request)
    {
        return view('empresa.aulas');
    }

    public function historico(Request $request, Aluno $aluno)
    {
        $empresa = $request->user()->empresa()->with('alunos')->firstOrFail();

        abort_if(! $empresa->alunos->contains($aluno->id), 403, 'Este aluno não pertence à sua empresa.');

        return view('empresa.historico', compact('empresa', 'aluno'));
    }
}
