<?php

namespace App\Livewire\Admin;

use App\Models\Turma;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class GerenciarTurmas extends Component
{
    use WithPagination;

    public string $search = '';
    public bool $showModal = false;
    public ?int $editingId = null;

    public string $nome   = '';
    public string $curso  = '';
    public string $ano    = '';
    public string $professor_id = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openModal(?int $id = null): void
    {
        $this->resetValidation();
        $this->editingId = $id;

        if ($id) {
            $t = Turma::findOrFail($id);
            $this->nome         = $t->nome;
            $this->curso        = $t->curso;
            $this->ano          = (string) $t->ano;
            $this->professor_id = (string) ($t->professor_id ?? '');
        } else {
            $this->nome = $this->curso = $this->ano = $this->professor_id = '';
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
        $this->validate([
            'nome'         => 'required|min:3|max:150',
            'curso'        => 'required|min:3|max:150',
            'ano'          => 'required|integer|min:2000|max:2099',
            'professor_id' => 'nullable|exists:users,id',
        ]);

        $data = [
            'nome'         => $this->nome,
            'curso'        => $this->curso,
            'ano'          => $this->ano,
            'professor_id' => $this->professor_id ?: null,
        ];

        if ($this->editingId) {
            Turma::findOrFail($this->editingId)->update($data);
        } else {
            Turma::create($data);
        }

        $this->closeModal();
        session()->flash('success', $this->editingId ? 'Turma atualizada.' : 'Turma cadastrada.');
    }

    public function deletar(int $id): void
    {
        Turma::findOrFail($id)->delete();
        session()->flash('success', 'Turma removida.');
    }

    public function render()
    {
        return view('livewire.admin.gerenciar-turmas', [
            'turmas'      => Turma::with(['professor' => fn ($q) => $q->withTrashed()])
                ->when($this->search, fn ($q) => $q->where(fn ($qq) =>
                    $qq->where('nome', 'like', "%{$this->search}%")
                       ->orWhere('curso', 'like', "%{$this->search}%")
                ))
                ->orderBy('nome')
                ->paginate(10),
            'professores' => User::where('role', 'professor')->where('active', true)->orderBy('name')->get(),
        ]);
    }
}
