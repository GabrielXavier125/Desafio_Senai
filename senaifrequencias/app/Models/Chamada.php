<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chamada extends Model
{
    public $timestamps = false;

    protected $fillable = ['aula_id', 'aluno_id', 'status', 'updated_at'];

    public function aula()
    {
        return $this->belongsTo(Aula::class);
    }

    public function aluno()
    {
        return $this->belongsTo(Aluno::class);
    }
}
