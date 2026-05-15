<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class AlunosController extends Controller
{
    public function index()
    {
        return view('admin.alunos.index');
    }

    public function store()  {}
    public function update() {}
    public function destroy() {}
}
