<?php

// ─── Namespace ─────────────────────────────────────────────────────────────
// "Endereço" desta classe dentro do projeto.
// App\Models significa: pasta app/ → subpasta Models/
namespace App\Models;

// ─── Importações ───────────────────────────────────────────────────────────
// Traz funcionalidades prontas do Laravel para dentro desta classe.
use Illuminate\Database\Eloquent\Factories\HasFactory; // cria usuários falsos para testes automatizados
use Illuminate\Database\Eloquent\SoftDeletes;           // "excluir" sem remover do banco — preenche deleted_at
use Illuminate\Foundation\Auth\User as Authenticatable; // classe base com suporte a login (sessions, tokens)
use Illuminate\Notifications\Notifiable;                 // permite enviar notificações por e-mail

// ─── Classe User ───────────────────────────────────────────────────────────
// Representa um usuário do sistema. Pode ser: admin, professor ou empresa.
// O Laravel usa esta classe automaticamente para autenticação (login/logout).
// Tabela no banco de dados: users
class User extends Authenticatable // herda tudo do Laravel para que o login funcione
{
    // "use" inclui traits — funcionalidades extras sem precisar escrever do zero
    use HasFactory,  // habilita User::factory() para criar dados de teste
        Notifiable,  // habilita $user->notify(...) para enviar e-mails
        SoftDeletes; // ao chamar $user->delete(), NÃO apaga do banco — apenas preenche deleted_at

    // Campos que podem ser preenchidos via formulário ou código
    // Proteção contra "mass assignment attack": só estes campos são aceitos
    protected $fillable = ['name', 'email', 'password', 'role', 'active'];

    // Campos que NUNCA aparecem quando o modelo é exibido como JSON/array
    // Segurança: a senha nunca é exposta em respostas da API
    protected $hidden = ['password', 'remember_token'];

    // Instrui o Laravel sobre como converter cada coluna ao ler do banco
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime', // string "2026-05-15 10:00:00" → objeto Carbon (data PHP)
            'password'          => 'hashed',    // ao salvar, criptografa automaticamente com bcrypt
            'active'            => 'boolean',   // banco guarda 0 ou 1; PHP recebe false ou true
        ];
    }

    // ─── Métodos auxiliares de verificação de perfil ───────────────────────

    // Retorna true se este usuário é o administrador
    public function isAdmin(): bool
    {
        return $this->role === 'admin'; // $this->role lê o campo "role" da tabela users
    }

    // Retorna true se este usuário é um professor
    public function isProfessor(): bool
    {
        return $this->role === 'professor';
    }

    // Retorna true se este usuário é uma empresa parceira
    public function isEmpresa(): bool
    {
        return $this->role === 'empresa';
    }

    // ─── Relacionamentos com outras tabelas ───────────────────────────────

    // Um professor pode ter várias turmas vinculadas a ele (1 usuario → N turmas)
    // O campo "professor_id" na tabela "turmas" aponta de volta para este usuário
    public function turmas()
    {
        return $this->hasMany(Turma::class, 'professor_id');
        // SQL equivalente: SELECT * FROM turmas WHERE professor_id = {id deste usuário}
    }

    // Um usuário do tipo empresa está vinculado a exatamente 1 registro na tabela "empresas"
    public function empresa()
    {
        return $this->hasOne(Empresa::class);
        // SQL equivalente: SELECT * FROM empresas WHERE user_id = {id deste usuário}
    }
}
