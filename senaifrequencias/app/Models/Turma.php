<?php

// Namespace: localização desta classe no projeto
namespace App\Models;

// Importações das funcionalidades do Laravel
use Illuminate\Database\Eloquent\Model;       // classe base obrigatória para todo Model
use Illuminate\Database\Eloquent\SoftDeletes; // "excluir sem apagar" do banco de dados

// Model Turma — representa uma turma da escola (ex: "Turma A - Desenvolvimento 2026")
// Tabela no banco: turmas
// Relacionamentos: pertence a 1 professor, tem N alunos, tem N aulas
class Turma extends Model
{
    // SoftDeletes: ao deletar uma turma, o campo deleted_at é preenchido
    // Os alunos, aulas e chamadas vinculados continuam existindo no banco (histórico preservado)
    use SoftDeletes;

    // Campos que o Laravel permite salvar via formulário
    protected $fillable = ['nome', 'curso', 'ano', 'professor_id'];

    // ─── Relacionamentos ───────────────────────────────────────────────────

    // Esta turma PERTENCE A um professor (N turmas → 1 professor)
    // O campo "professor_id" nesta tabela aponta para o id do usuário professor
    public function professor()
    {
        return $this->belongsTo(User::class, 'professor_id');
        // SQL: SELECT * FROM users WHERE id = {professor_id da turma}
        // O segundo argumento 'professor_id' é necessário porque a chave estrangeira
        // não segue o padrão user_id (que seria o nome automático para User)
    }

    // Esta turma TEM MUITOS alunos matriculados nela
    public function alunos()
    {
        return $this->hasMany(Aluno::class);
        // SQL: SELECT * FROM alunos WHERE turma_id = {id desta turma}
    }

    // Esta turma TEM MUITAS aulas realizadas
    public function aulas()
    {
        return $this->hasMany(Aula::class);
        // SQL: SELECT * FROM aulas WHERE turma_id = {id desta turma}
    }
}
