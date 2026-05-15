<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class GerenciarProfessores extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $editingId = null;

    #[Validate('required|min:3|max:150')]
    public string $nome = '';

    #[Validate('required|email|max:150')]
    public string $email = '';

    #[Validate('nullable|min:6|max:100')]
    public string $senha = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openModal(?int $id = null): void
    {
        $this->resetValidation();
        $this->editingId = $id;

        if ($id) {
            $prof = User::findOrFail($id);
            $this->nome  = $prof->name;
            $this->email = $prof->email;
            $this->senha = '';
        } else {
            $this->nome  = '';
            $this->email = '';
            $this->senha = '';
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
        $rules = [
            'nome'  => 'required|min:3|max:150',
            'email' => 'required|email|max:150|unique:users,email' . ($this->editingId ? ",{$this->editingId}" : ''),
            'senha' => $this->editingId ? 'nullable|min:6|max:100' : 'required|min:6|max:100',
        ];

        $this->validate($rules);

        $data = [
            'name'  => $this->nome,
            'email' => $this->email,
            'role'  => 'professor',
        ];

        if ($this->senha) {
            $data['password'] = Hash::make($this->senha);
        }

        if ($this->editingId) {
            User::findOrFail($this->editingId)->update($data);
        } else {
            User::create($data);
        }

        $this->closeModal();
        session()->flash('success', $this->editingId ? 'Professor atualizado.' : 'Professor cadastrado.');
    }

    public function toggleAtivo(int $id): void
    {
        $prof = User::findOrFail($id);
        $prof->update(['active' => ! $prof->active]);
        session()->flash('success', $prof->active ? 'Professor reativado.' : 'Professor desativado.');
    }

    public function excluir(int $id): void
    {
        User::findOrFail($id)->delete();
        session()->flash('success', 'Professor excluído.');
    }

    public function render()
    {
        return view('livewire.admin.gerenciar-professores', [
            'professores' => User::where('role', 'professor')
                ->where(fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%"))
                ->orderBy('name')
                ->paginate(10),
        ]);
    }
}
