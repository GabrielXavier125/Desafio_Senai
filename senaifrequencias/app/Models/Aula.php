<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    protected $fillable = ['turma_id', 'data', 'descricao'];

    protected function casts(): array
    {
        return ['data' => 'date'];
    }

    public function turma()
    {
        return $this->belongsTo(Turma::class);
    }

    public function chamadas()
    {
        return $this->hasMany(Chamada::class);
    }
}
