<?php

namespace App\Livewire\Professor;

use App\Models\Aula;
use App\Models\Chamada;
use App\Models\Turma;
use Livewire\Component;

class TurmaDashboard extends Component
{
    public ?Turma $turma = null;

    // Modal nova aula
    public bool $showModal = false;
    public string $data      = '';
    public string $descricao = '';

    // Modal edição
    public bool $showEditModal = false;
    public ?int  $editingAulaId = null;
    public string $editData      = '';
    public string $editDescricao = '';

    public function mount(): void
    {
        $this->turma = auth()->user()
            ->turmas()
            ->with(['alunos' => fn ($q) => $q->where('active', true)])
            ->first();

        $this->data = now()->format('Y-m-d');
    }

    // ── Nova aula ────────────────────────────────────────────
    public function openModal(): void
    {
        $this->resetValidation();
        $this->data      = now()->format('Y-m-d');
        $this->descricao = '';
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function criarAula(): void
    {
        $this->validate([
            'data'      => 'required|date',
            'descricao' => 'required|min:3|max:1000',
        ]);

        $aula = Aula::create([
            'turma_id'  => $this->turma->id,
            'data'      => $this->data,
            'descricao' => $this->descricao,
        ]);

        $alunos = $this->turma->alunos;
        foreach ($alunos as $aluno) {
            Chamada::firstOrCreate(
                ['aula_id' => $aula->id, 'aluno_id' => $aluno->id],
                ['status' => 'falta', 'updated_at' => now()]
            );
        }

        $this->closeModal();
        $this->redirect(route('professor.aulas.chamada', $aula), navigate: false);
    }

    // ── Editar aula ──────────────────────────────────────────
    public function openEditModal(int $aulaId): void
    {
        $this->resetValidation();
        $aula = Aula::findOrFail($aulaId);

        $this->editingAulaId = $aulaId;
        $this->editData      = $aula->data->format('Y-m-d');
        $this->editDescricao = $aula->descricao;
        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal  = false;
        $this->editingAulaId  = null;
    }

    public function salvarEdicao(): void
    {
        $this->validate([
            'editData'      => 'required|date',
            'editDescricao' => 'required|min:3|max:1000',
        ], [], [
            'editData'      => 'data',
            'editDescricao' => 'conteúdo',
        ]);

        Aula::findOrFail($this->editingAulaId)->update([
            'data'      => $this->editData,
            'descricao' => $this->editDescricao,
        ]);

        $this->closeEditModal();
        session()->flash('success', 'Aula atualizada.');
    }

    // ── Excluir aula ─────────────────────────────────────────
    public function excluirAula(int $aulaId): void
    {
        $aula = Aula::findOrFail($aulaId);
        $aula->chamadas()->delete();
        $aula->delete();
        session()->flash('success', 'Aula removida.');
    }

    public function render()
    {
        if (! $this->turma) {
            return view('livewire.professor.turma-dashboard');
        }

        $aulas = Aula::where('turma_id', $this->turma->id)
            ->withCount([
                'chamadas as presentes' => fn ($q) => $q->where('status', 'presente'),
                'chamadas as faltas'    => fn ($q) => $q->where('status', 'falta'),
            ])
            ->orderByDesc('data')
            ->get();

        return view('livewire.professor.turma-dashboard', compact('aulas'));
    }
}
