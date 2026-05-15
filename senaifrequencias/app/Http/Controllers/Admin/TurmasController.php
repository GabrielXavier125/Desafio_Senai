<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Turma;

class TurmasController extends Controller
{
    public function index()
    {
        return view('admin.turmas.index');
    }

    public function show(Turma $turma)
    {
        return view('admin.turmas.aulas', compact('turma'));
    }

    public function store()  {}
    public function update() {}
    public function destroy() {}
}
