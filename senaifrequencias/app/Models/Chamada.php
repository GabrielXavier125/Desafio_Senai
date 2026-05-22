<?php

// Namespace: localização desta classe dentro da pasta Models
namespace App\Models;

// Importação da classe base de todos os models do Laravel
use Illuminate\Database\Eloquent\Model;

// Model Chamada — representa o registro de presença de UM aluno em UMA aula
// Exemplo de registro: aula_id=5, aluno_id=12, status='presente'
// Tabela no banco: chamadas
// Esta tabela NÃO tem coluna "id" própria — a chave é o par (aula_id + aluno_id)
class Chamada extends Model
{
    // Desativa os timestamps automáticos (created_at e updated_at)
    // A tabela "chamadas" só tem "updated_at" manual — não tem "created_at"
    public $timestamps = false;

    // Campos que podem ser salvos via código
    // "updated_at" está aqui porque precisamos salvá-lo manualmente (sem timestamps automáticos)
    protected $fillable = ['aula_id', 'aluno_id', 'status', 'updated_at'];

    // ─── Relacionamentos ───────────────────────────────────────────────────

    // Esta chamada PERTENCE A uma aula específica
    public function aula()
    {
        return $this->belongsTo(Aula::class);
        // SQL: SELECT * FROM aulas WHERE id = {aula_id da chamada}
    }

    // Esta chamada PERTENCE A um aluno específico
    public function aluno()
    {
        return $this->belongsTo(Aluno::class);
        // SQL: SELECT * FROM alunos WHERE id = {aluno_id da chamada}
    }
}
