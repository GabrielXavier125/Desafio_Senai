<?php

namespace App\Livewire\Admin;

use App\Models\Aula;
use App\Models\Turma;
use Livewire\Component;
use Livewire\WithPagination;

class AulasTurma extends Component
{
    use WithPagination;

    public Turma $turma;

    public string $de  = '';
    public string $ate = '';

    public function updatingDe(): void  { $this->resetPage(); }
    public function updatingAte(): void { $this->resetPage(); }

    public function render()
    {
        $aulas = Aula::where('turma_id', $this->turma->id)
            ->when($this->de,  fn ($q) => $q->whereDate('data', '>=', $this->de))
            ->when($this->ate, fn ($q) => $q->whereDate('data', '<=', $this->ate))
            ->withCount([
                'chamadas as presentes' => fn ($q) => $q->where('status', 'presente'),
                'chamadas as faltas'    => fn ($q) => $q->where('status', 'falta'),
            ])
            ->orderByDesc('data')
            ->paginate(20);

        return view('livewire.admin.aulas-turma', compact('aulas'));
    }
}
