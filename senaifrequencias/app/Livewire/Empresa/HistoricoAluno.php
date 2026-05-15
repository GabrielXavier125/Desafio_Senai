<?php

namespace App\Livewire\Empresa;

use App\Models\Aluno;
use App\Models\Aula;
use Livewire\Component;

class HistoricoAluno extends Component
{
    public Aluno $aluno;

    public string $de  = '';
    public string $ate = '';

    public function mount(Aluno $aluno): void
    {
        $this->aluno = $aluno->load('turma');
        $this->de    = now()->startOfMonth()->format('Y-m-d');
        $this->ate   = now()->format('Y-m-d');
    }

    public function render()
    {
        $aulas = Aula::where('turma_id', $this->aluno->turma_id)
            ->when($this->de,  fn ($q) => $q->whereDate('data', '>=', $this->de))
            ->when($this->ate, fn ($q) => $q->whereDate('data', '<=', $this->ate))
            ->with(['chamadas' => fn ($q) => $q->where('aluno_id', $this->aluno->id)])
            ->orderByDesc('data')
            ->get();

        $total    = $aulas->count();
        $presentes = $aulas->filter(fn ($a) => $a->chamadas->first()?->status === 'presente')->count();
        $pct      = $total > 0 ? round($presentes / $total * 100) : 0;

        return view('livewire.empresa.historico-aluno', compact('aulas', 'total', 'presentes', 'pct'));
    }
}
