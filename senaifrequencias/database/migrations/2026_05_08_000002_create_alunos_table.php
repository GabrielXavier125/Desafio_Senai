<?php

use Illuminate\Database\Migrations\Migration; // classe base de toda migration
use Illuminate\Database\Schema\Blueprint;     // define as colunas da tabela
use Illuminate\Support\Facades\Schema;        // executa operações no banco de dados

// Migration para criar a tabela "alunos"
// Esta tabela guarda todos os alunos matriculados nas turmas
return new class extends Migration
{
    // up() = cria a tabela quando você roda "php artisan migrate"
    public function up(): void
    {
        Schema::create('alunos', function (Blueprint $table) {

            // id: número único que identifica cada aluno (1, 2, 3...)
            // Gerado automaticamente pelo banco de dados ao inserir um novo aluno
            $table->id();

            // nome: nome completo do aluno, até 150 caracteres
            $table->string('nome', 150);

            // ra: Registro do Aluno — número de matrícula único de cada aluno
            // ->unique() cria um índice UNIQUE no banco: dois alunos não podem ter o mesmo RA
            // Se tentar inserir um RA repetido, o banco retorna um erro de constraint
            $table->string('ra', 30)->unique();

            // turma_id: chave estrangeira — aponta para o id da tabela "turmas"
            // ->constrained('turmas') cria a foreign key: turma_id REFERENCES turmas(id)
            // ->cascadeOnDelete() = se a turma for excluída, os alunos dela também são excluídos
            $table->foreignId('turma_id')->constrained('turmas')->cascadeOnDelete();

            // active: indica se o aluno está ativo (true) ou desativado (false)
            // ->default(true) = todo aluno começa ativo quando é cadastrado
            // Alunos desativados não aparecem na chamada, mas seus dados são preservados
            $table->boolean('active')->default(true);

            // Cria as colunas created_at (quando foi criado) e updated_at (quando foi modificado)
            $table->timestamps();
        });
    }

    // down() = DESFAZ a migration — exclui a tabela "alunos"
    public function down(): void
    {
        Schema::dropIfExists('alunos');
    }
};
