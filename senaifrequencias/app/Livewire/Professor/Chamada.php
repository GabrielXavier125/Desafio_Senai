<?php

namespace App\Livewire\Professor;

use App\Models\Aula;
use App\Models\Chamada as ChamadaModel;
use Livewire\Component;

class Chamada extends Component
{
    public Aula $aula;

    /** @var array<int, string> aluno_id → 'presente'|'falta' */
    public array $statuses = [];

    public function mount(Aula $aula): void
    {
        $this->aula = $aula->load(['turma.alunos' => fn ($q) => $q->where('active', true)]);

        $chamadas = ChamadaModel::where('aula_id', $aula->id)
            ->pluck('status', 'aluno_id')
            ->toArray();

        foreach ($this->aula->turma->alunos as $aluno) {
            $this->statuses[$aluno->id] = $chamadas[$aluno->id] ?? 'falta';
        }
    }

    public function toggle(int $alunoId): void
    {
        $novo = $this->statuses[$alunoId] === 'presente' ? 'falta' : 'presente';

        ChamadaModel::updateOrCreate(
            ['aula_id' => $this->aula->id, 'aluno_id' => $alunoId],
            ['status' => $novo, 'updated_at' => now()]
        );

        $this->statuses[$alunoId] = $novo;

        $this->dispatch('status-saved', alunoId: $alunoId);
    }

    public function getPresentes(): int
    {
        return count(array_filter($this->statuses, fn ($s) => $s === 'presente'));
    }

    public function getFaltas(): int
    {
        return count(array_filter($this->statuses, fn ($s) => $s === 'falta'));
    }

    public function render()
    {
        return view('livewire.professor.chamada');
    }
}
