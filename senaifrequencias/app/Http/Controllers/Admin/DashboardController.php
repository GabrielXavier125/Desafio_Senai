<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aluno;
use App\Models\Empresa;
use App\Models\Turma;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'totalAlunos'     => Aluno::where('active', true)->count(),
            'totalProfessores' => User::where('role', 'professor')->where('active', true)->count(),
            'totalEmpresas'   => Empresa::count(),
            'totalTurmas'     => Turma::count(),
        ]);
    }
}
