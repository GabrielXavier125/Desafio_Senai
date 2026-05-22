<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration para criar a tabela "aulas"
// Cada aula representa uma aula específica acontecida em uma data — ex: "Aula de PHP, 15/05/2026"
return new class extends Migration
{
    // up() = cria a tabela
    public function up(): void
    {
        Schema::create('aulas', function (Blueprint $table) {

            // id: identificador único de cada aula
            $table->id();

            // turma_id: qual turma teve esta aula
            // ->constrained('turmas') = chave estrangeira para a tabela turmas
            // ->cascadeOnDelete() = se a turma for excluída, todas as suas aulas também são excluídas
            $table->foreignId('turma_id')->constrained('turmas')->cascadeOnDelete();

            // data: a data em que a aula aconteceu
            // Tipo DATE no MySQL: guarda apenas a data (sem hora), ex: "2026-05-15"
            $table->date('data');

            // descricao: o conteúdo ministrado na aula
            // Tipo TEXT: permite textos longos (sem limite fixo de caracteres)
            $table->text('descricao');

            // Colunas automáticas de auditoria de data/hora de criação e modificação
            $table->timestamps();
        });
    }

    // down() = exclui a tabela (desfaz a migration)
    public function down(): void
    {
        Schema::dropIfExists('aulas');
    }
};
