<?php

// Namespace: "endereço" desta classe dentro do projeto
namespace App\Models;

// Importações: traz funcionalidades do Laravel
use Illuminate\Database\Eloquent\Model;      // classe base que todo Model do Laravel precisa herdar
use Illuminate\Database\Eloquent\SoftDeletes; // permite "excluir" sem apagar — preserva histórico

// Model Aluno — representa um aluno matriculado no SENAI
// Tabela no banco: alunos
// Relacionamentos: pertence a 1 turma, tem N chamadas, pode estar em N empresas
class Aluno extends Model
{
    // SoftDeletes: ao chamar $aluno->delete(), o registro NÃO é removido do banco
    // O campo "deleted_at" é preenchido com a data/hora da exclusão
    // Assim, o histórico de chamadas do aluno é preservado mesmo após "excluí-lo"
    use SoftDeletes;

    // Campos que podem ser salvos via código ou formulário
    protected $fillable = ['nome', 'ra', 'turma_id', 'active'];

    // Conversões automáticas ao ler do banco de dados
    protected function casts(): array
    {
        return [
            'active' => 'boolean', // banco guarda 0 ou 1; PHP lê false ou true
        ];
    }

    // ─── Relacionamentos ───────────────────────────────────────────────────

    // Este aluno PERTENCE A uma turma (muitos alunos → 1 turma)
    // O campo "turma_id" nesta tabela aponta para o id da turma
    public function turma()
    {
        return $this->belongsTo(Turma::class);
        // SQL: SELECT * FROM turmas WHERE id = {turma_id do aluno}
    }

    // Este aluno TEM MUITOS registros de chamada — um para cada aula
    // Se foi marcado presente na aula 3, existe uma linha: aula_id=3, aluno_id={id}, status='presente'
    public function chamadas()
    {
        return $this->hasMany(Chamada::class);
        // SQL: SELECT * FROM chamadas WHERE aluno_id = {id do aluno}
    }

    // Este aluno pode estar vinculado a VÁRIAS empresas (relação muitos para muitos)
    // A tabela "empresa_aluno" guarda os pares: empresa_id + aluno_id
    public function empresas()
    {
        return $this->belongsToMany(Empresa::class, 'empresa_aluno');
        // SQL: SELECT empresas.* FROM empresas
        //      JOIN empresa_aluno ON empresa_aluno.empresa_id = empresas.id
        //      WHERE empresa_aluno.aluno_id = {id do aluno}
    }
}
