<?php

// Namespace: localização desta classe no projeto
namespace App\Models;

// Importação da classe base de todos os models do Laravel
use Illuminate\Database\Eloquent\Model;

// Model Empresa — representa uma empresa parceira do SENAI
// Cada empresa tem um login no sistema para consultar a frequência dos seus aprendizes
// Tabela no banco: empresas
// Relacionamentos: pertence a 1 usuário (login), tem N alunos vinculados
class Empresa extends Model
{
    // Campos que podem ser salvos via formulário
    protected $fillable = ['nome', 'cnpj', 'user_id'];

    // ─── Relacionamentos ───────────────────────────────────────────────────

    // Esta empresa PERTENCE A um usuário (o login da empresa)
    // O campo "user_id" aponta para o id do usuário com role='empresa'
    public function user()
    {
        return $this->belongsTo(User::class);
        // SQL: SELECT * FROM users WHERE id = {user_id da empresa}
    }

    // Esta empresa pode acompanhar VÁRIOS alunos (relação muitos para muitos)
    // A tabela intermediária "empresa_aluno" registra quais alunos cada empresa monitora
    public function alunos()
    {
        return $this->belongsToMany(Aluno::class, 'empresa_aluno');
        // SQL: SELECT alunos.* FROM alunos
        //      JOIN empresa_aluno ON empresa_aluno.aluno_id = alunos.id
        //      WHERE empresa_aluno.empresa_id = {id desta empresa}
    }
}
