<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;

class EmpresasController extends Controller
{
    public function index()
    {
        return view('admin.empresas.index');
    }

    public function show(Empresa $empresa)
    {
        return view('admin.empresas.show', compact('empresa'));
    }

    public function store()  {}
    public function update() {}
    public function destroy() {}
}
