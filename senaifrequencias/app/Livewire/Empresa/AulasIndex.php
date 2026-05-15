<?php

namespace App\Livewire\Empresa;

use App\Models\Aula;
use App\Models\Turma;
use Livewire\Component;
use Livewire\WithPagination;

class AulasIndex extends Component
{
    use WithPagination;

    public string $filtroTurma = '';
    public string $de          = '';
    public string $ate         = '';

    /** @var array<int> IDs das turmas cujos alunos pertencem à empresa */
    protected array $turmaIds = [];

    public function mount(): void
    {
        $empresa = auth()->user()->empresa()->with('alunos')->first();

        $this->turmaIds = $empresa
            ? $empresa->alunos->pluck('turma_id')->filter()->unique()->values()->all()
            : [];
    }

    public function updatingFiltroTurma(): void { $this->resetPage(); }
    public function updatingDe(): void          { $this->resetPage(); }
    public function updatingAte(): void         { $this->resetPage(); }

    public function render()
    {
        // Recalcula turmaIds a cada render (Livewire não persiste protected entre requests)
        $empresa = auth()->user()->empresa()->with('alunos')->first();
        $turmaIds = $empresa
            ? $empresa->alunos->pluck('turma_id')->filter()->unique()->values()->all()
            : [];

        $aulas = Aula::whereIn('turma_id', $turmaIds)
            ->with('turma')
            ->when($this->filtroTurma, fn ($q) => $q->where('turma_id', $this->filtroTurma))
            ->when($this->de,         fn ($q) => $q->whereDate('data', '>=', $this->de))
            ->when($this->ate,        fn ($q) => $q->whereDate('data', '<=', $this->ate))
            ->orderByDesc('data')
            ->paginate(15);

        $turmas = Turma::whereIn('id', $turmaIds)->orderBy('nome')->get();

        return view('livewire.empresa.aulas-index', compact('aulas', 'turmas'));
    }
}
