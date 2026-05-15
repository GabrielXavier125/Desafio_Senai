<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class ProfessoresController extends Controller
{
    public function index()
    {
        return view('admin.professores.index');
    }

    public function store()  {}
    public function update() {}
    public function destroy() {}
}
