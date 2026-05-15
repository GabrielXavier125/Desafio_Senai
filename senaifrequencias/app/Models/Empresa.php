<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $fillable = ['nome', 'cnpj', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function alunos()
    {
        return $this->belongsToMany(Aluno::class, 'empresa_aluno');
    }
}
