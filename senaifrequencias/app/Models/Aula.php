<?php

// Namespace: localização desta classe dentro da pasta Models
namespace App\Models;

// Importação da classe base de todos os models do Laravel
use Illuminate\Database\Eloquent\Model;

// Model Aula — representa uma aula realizada por uma turma
// Exemplo de registro: Turma A, data 15/05/2026, descrição "Introdução ao PHP"
// Tabela no banco: aulas
// Relacionamentos: pertence a 1 turma, tem N registros de chamada
class Aula extends Model
{
    // Campos que podem ser salvos via formulário ou código
    protected $fillable = ['turma_id', 'data', 'descricao'];

    // Conversões automáticas de tipos ao ler do banco
    protected function casts(): array
    {
        return [
            'data' => 'date', // converte a string "2026-05-15" do banco para objeto Carbon
                               // permite usar $aula->data->format('d/m/Y') para formatar
        ];
    }

    // ─── Relacionamentos ───────────────────────────────────────────────────

    // Esta aula PERTENCE A uma turma (N aulas → 1 turma)
    public function turma()
    {
        return $this->belongsTo(Turma::class);
        // SQL: SELECT * FROM turmas WHERE id = {turma_id da aula}
    }

    // Esta aula TEM MUITOS registros de chamada — um para cada aluno da turma
    // Cada linha em "chamadas" representa: "o aluno X estava presente/ausente nesta aula"
    public function chamadas()
    {
        return $this->hasMany(Chamada::class);
        // SQL: SELECT * FROM chamadas WHERE aula_id = {id desta aula}
    }
}
