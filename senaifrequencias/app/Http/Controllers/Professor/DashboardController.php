<?php

namespace App\Http\Controllers\Professor;

use App\Http\Controllers\Controller;
use App\Models\Aula;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('professor.dashboard');
    }

    public function chamada(Request $request, Aula $aula)
    {
        $turma = $request->user()->turmas()->firstOrFail();

        abort_if($aula->turma_id !== $turma->id, 403, 'Esta aula não pertence à sua turma.');

        return view('professor.chamada', compact('aula'));
    }
}
