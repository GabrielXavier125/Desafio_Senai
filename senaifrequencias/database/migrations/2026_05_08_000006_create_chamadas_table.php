<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chamadas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aula_id')->constrained('aulas')->cascadeOnDelete();
            $table->foreignId('aluno_id')->constrained('alunos')->cascadeOnDelete();
            $table->enum('status', ['presente', 'falta'])->default('falta');
            $table->timestamp('updated_at')->nullable();
            $table->unique(['aula_id', 'aluno_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chamadas');
    }
};
