<?php

namespace App\Livewire\Admin;

use App\Models\Aluno;
use App\Models\Turma;
use Livewire\Component;
use Livewire\WithPagination;

class GerenciarAlunos extends Component
{
    use WithPagination;

    public string $search   = '';
    public string $filtroTurma = '';
    public bool   $showModal = false;
    public ?int   $editingId = null;

    public string $nome     = '';
    public string $ra       = '';
    public string $turma_id = '';

    public function updatingSearch(): void   { $this->resetPage(); }
    public function updatingFiltroTurma(): void { $this->resetPage(); }

    public function openModal(?int $id = null): void
    {
        $this->resetValidation();
        $this->editingId = $id;

        if ($id) {
            $a = Aluno::findOrFail($id);
            $this->nome     = $a->nome;
            $this->ra       = $a->ra;
            $this->turma_id = (string) $a->turma_id;
        } else {
            $this->nome = $this->ra = $this->turma_id = '';
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
            'nome'     => 'required|min:3|max:150',
            'ra'       => 'required|max:30|unique:alunos,ra' . ($this->editingId ? ",{$this->editingId}" : ''),
            'turma_id' => 'required|exists:turmas,id',
        ]);

        $data = [
            'nome'     => $this->nome,
            'ra'       => $this->ra,
            'turma_id' => $this->turma_id,
        ];

        if ($this->editingId) {
            Aluno::findOrFail($this->editingId)->update($data);
        } else {
            Aluno::create($data);
        }

        $this->closeModal();
        session()->flash('success', $this->editingId ? 'Aluno atualizado.' : 'Aluno cadastrado.');
    }

    public function toggleAtivo(int $id): void
    {
        $aluno = Aluno::findOrFail($id);
        $aluno->update(['active' => ! $aluno->active]);
        session()->flash('success', $aluno->active ? 'Aluno reativado.' : 'Aluno desativado.');
    }

    public function excluir(int $id): void
    {
        $aluno = Aluno::findOrFail($id);
        $aluno->empresas()->detach();
        $aluno->delete();
        session()->flash('success', 'Aluno excluído.');
    }

    public function render()
    {
        $query = Aluno::with(['turma' => fn ($q) => $q->withTrashed(), 'empresas'])
            ->when($this->search, fn ($q) => $q->where(fn ($qq) =>
                $qq->where('nome', 'like', "%{$this->search}%")
                   ->orWhere('ra', 'like', "%{$this->search}%")
            ))
            ->when($this->filtroTurma, fn ($q) => $q->where('turma_id', $this->filtroTurma))
            ->orderBy('nome');

        return view('livewire.admin.gerenciar-alunos', [
            'alunos'  => $query->paginate(10),
            'turmas'  => Turma::orderBy('nome')->get(),
        ]);
    }
}
