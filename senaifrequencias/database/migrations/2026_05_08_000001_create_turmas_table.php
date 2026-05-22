<?php

// Importações necessárias para criar migrations
use Illuminate\Database\Migrations\Migration; // classe base de toda migration
use Illuminate\Database\Schema\Blueprint;     // permite definir as colunas da tabela
use Illuminate\Support\Facades\Schema;        // fachada para executar operações no banco

// Uma migration é como uma "receita" para criar/modificar tabelas no banco de dados
// Para executar: php artisan migrate
// Para desfazer: php artisan migrate:rollback
return new class extends Migration
{
    // up() é chamado ao rodar "php artisan migrate" — CRIA a tabela
    public function up(): void
    {
        // Schema::create('nome_da_tabela', função que define as colunas)
        Schema::create('turmas', function (Blueprint $table) {

            // Cria a coluna "id": inteiro, auto-incremento, chave primária
            // Equivalente SQL: id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->id();

            // Coluna "nome": texto de até 150 caracteres, obrigatório
            // Equivalente SQL: nome VARCHAR(150) NOT NULL
            $table->string('nome', 150);

            // Coluna "curso": nome do curso da turma, até 150 caracteres
            $table->string('curso', 150);

            // Coluna "ano": armazena apenas o ano (ex: 2026)
            // Equivalente SQL: ano YEAR NOT NULL
            $table->year('ano');

            // Coluna "professor_id": chave estrangeira que aponta para a tabela "users"
            // ->nullable() = pode ser null (turma sem professor vinculado)
            // ->constrained('users') = cria a FK: professor_id REFERENCES users(id)
            // ->nullOnDelete() = se o professor for excluído, professor_id vira null (não exclui a turma)
            $table->foreignId('professor_id')->nullable()->constrained('users')->nullOnDelete();

            // Cria automaticamente as colunas created_at e updated_at
            // O Laravel preenche essas colunas sozinho ao criar/atualizar registros
            $table->timestamps();
        });
    }

    // down() é chamado ao rodar "php artisan migrate:rollback" — DESFAZ a migration
    public function down(): void
    {
        // dropIfExists: exclui a tabela se ela existir (não dá erro se não existir)
        Schema::dropIfExists('turmas');
    }
};
