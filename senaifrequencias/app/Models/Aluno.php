<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Aluno extends Model
{
    use SoftDeletes;

    protected $fillable = ['nome', 'ra', 'turma_id', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function turma()
    {
        return $this->belongsTo(Turma::class);
    }

    public function chamadas()
    {
        return $this->hasMany(Chamada::class);
    }

    public function empresas()
    {
        return $this->belongsToMany(Empresa::class, 'empresa_aluno');
    }
}
