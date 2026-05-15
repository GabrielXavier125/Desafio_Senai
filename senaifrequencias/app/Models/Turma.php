<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Turma extends Model
{
    use SoftDeletes;

    protected $fillable = ['nome', 'curso', 'ano', 'professor_id'];

    public function professor()
    {
        return $this->belongsTo(User::class, 'professor_id');
    }

    public function alunos()
    {
        return $this->hasMany(Aluno::class);
    }

    public function aulas()
    {
        return $this->hasMany(Aula::class);
    }
}
