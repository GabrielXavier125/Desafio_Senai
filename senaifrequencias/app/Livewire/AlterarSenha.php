<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Livewire\Component;

class AlterarSenha extends Component
{
    public string $senha_atual      = '';
    public string $nova_senha       = '';
    public string $nova_senha_confirmation = '';

    public bool $sucesso = false;

    public function salvar(): void
    {
        $this->validate([
            'senha_atual'            => 'required',
            'nova_senha'             => ['required', 'confirmed', Password::min(8)],
        ], [
            'nova_senha.min'       => 'A nova senha deve ter pelo menos 8 caracteres.',
            'nova_senha.confirmed' => 'A confirmação de senha não confere.',
        ]);

        if (! Hash::check($this->senha_atual, Auth::user()->password)) {
            $this->addError('senha_atual', 'A senha atual está incorreta.');
            return;
        }

        Auth::user()->update(['password' => Hash::make($this->nova_senha)]);

        $this->reset('senha_atual', 'nova_senha', 'nova_senha_confirmation');
        $this->sucesso = true;
    }

    public function render()
    {
        return view('livewire.alterar-senha');
    }
}
