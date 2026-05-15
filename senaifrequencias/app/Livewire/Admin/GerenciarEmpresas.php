<?php

namespace App\Livewire\Admin;

use App\Models\Empresa;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\WithPagination;

class GerenciarEmpresas extends Component
{
    use WithPagination;

    public string $search    = '';
    public bool   $showModal = false;
    public ?int   $editingId = null;

    public string $nome  = '';
    public string $cnpj  = '';
    public string $email = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openModal(?int $id = null): void
    {
        $this->resetValidation();
        $this->editingId = $id;

        if ($id) {
            $e = Empresa::with('user')->findOrFail($id);
            $this->nome  = $e->nome;
            $this->cnpj  = $e->cnpj;
            $this->email = $e->user->email;
        } else {
            $this->nome = $this->cnpj = $this->email = '';
        }

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingId = null;
    }

    public function save(): void
    {
        $emailRule = $this->editingId
            ? 'required|email|max:150|unique:users,email,' . Empresa::findOrFail($this->editingId)->user_id
            : 'required|email|max:150|unique:users,email';

        $this->validate([
            'nome'  => 'required|min:2|max:200',
            'cnpj'  => 'required|max:18|unique:empresas,cnpj' . ($this->editingId ? ",{$this->editingId}" : ''),
            'email' => $emailRule,
        ]);

        if ($this->editingId) {
            $empresa = Empresa::findOrFail($this->editingId);
            $empresa->update(['nome' => $this->nome, 'cnpj' => $this->cnpj]);
            $empresa->user->update(['name' => $this->nome, 'email' => $this->email]);
        } else {
            $user = User::create([
                'name'     => $this->nome,
                'email'    => $this->email,
                'password' => Hash::make('senai@empresa'),
                'role'     => 'empresa',
                'active'   => true,
            ]);

            Empresa::create([
                'nome'    => $this->nome,
                'cnpj'    => $this->cnpj,
                'user_id' => $user->id,
            ]);
        }

        $this->closeModal();
        session()->flash('success', $this->editingId
            ? 'Empresa atualizada.'
            : 'Empresa cadastrada. Senha provisória: senai@empresa');
    }

    public function toggleAtivo(int $id): void
    {
        $empresa = Empresa::with('user')->findOrFail($id);
        $empresa->user->update(['active' => ! $empresa->user->active]);
        session()->flash('success', $empresa->user->active ? 'Empresa reativada.' : 'Empresa desativada.');
    }

    public function excluir(int $id): void
    {
        $empresa = Empresa::with('user')->findOrFail($id);
        $empresa->alunos()->detach();
        $empresa->user->delete();
        $empresa->delete();
        session()->flash('success', 'Empresa excluída.');
    }

    public function render()
    {
        return view('livewire.admin.gerenciar-empresas', [
            'empresas' => Empresa::with('user')
                ->when($this->search, fn ($q) => $q->where(fn ($qq) =>
                    $qq->where('nome', 'like', "%{$this->search}%")
                       ->orWhere('cnpj', 'like', "%{$this->search}%")
                       ->orWhereHas('user', fn ($u) =>
                           $u->where('name', 'like', "%{$this->search}%")
                             ->orWhere('email', 'like', "%{$this->search}%")
                       )
                ))
                ->orderBy('nome')
                ->paginate(10),
        ]);
    }
}
