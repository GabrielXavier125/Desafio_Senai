<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration para criar a tabela "chamadas"
// Esta é a tabela mais importante do sistema!
// Cada linha representa: "O aluno X estava PRESENTE ou FALTA na aula Y"
// Exemplo de registro: aula_id=5, aluno_id=12, status='presente'
return new class extends Migration
{
    // up() = cria a tabela
    public function up(): void
    {
        Schema::create('chamadas', function (Blueprint $table) {

            // aula_id: qual aula este registro pertence
            // ->constrained('aulas') = chave estrangeira para a tabela "aulas"
            // ->cascadeOnDelete() = se a aula for excluída, todos os registros de chamada dela também são
            $table->foreignId('aula_id')->constrained('aulas')->cascadeOnDelete();

            // aluno_id: qual aluno este registro pertence
            // ->constrained('alunos') = chave estrangeira para a tabela "alunos"
            // ->cascadeOnDelete() = se o aluno for excluído, seus registros de chamada também são
            $table->foreignId('aluno_id')->constrained('alunos')->cascadeOnDelete();

            // status: o resultado da chamada para este aluno nesta aula
            // ->enum() = campo que só aceita valores da lista especificada
            // Somente 'presente' ou 'falta' são aceitos — qualquer outro valor gera erro
            // ->default('falta') = todo aluno começa como falta; o professor marca quem veio
            $table->enum('status', ['presente', 'falta'])->default('falta');

            // updated_at: registra quando o status foi alterado pela última vez
            // ->nullable() = pode ser null quando o registro acabou de ser criado
            // Nota: esta tabela NÃO tem created_at nem id automático (por isso não usa timestamps() nem id())
            $table->timestamp('updated_at')->nullable();

            // Restrição ÚNICA: não pode existir dois registros com o mesmo par (aula_id + aluno_id)
            // Isso garante que cada aluno aparece apenas UMA VEZ por aula
            // Se tentar inserir duplicata, o banco retorna erro — por isso usamos updateOrCreate() no código
            $table->unique(['aula_id', 'aluno_id']);
        });
    }

    // down() = exclui a tabela (desfaz a migration)
    public function down(): void
    {
        Schema::dropIfExists('chamadas');
    }
};
