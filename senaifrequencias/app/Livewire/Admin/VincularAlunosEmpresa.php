<?php

namespace App\Livewire\Admin;

use App\Models\Aluno;
use App\Models\Empresa;
use Livewire\Component;

class VincularAlunosEmpresa extends Component
{
    public Empresa $empresa;
    public array $selecionados = [];
    public string $search = '';

    public function mount(Empresa $empresa): void
    {
        $this->empresa    = $empresa;
        $this->selecionados = $empresa->alunos->pluck('id')->map(fn ($id) => (string) $id)->toArray();
    }

    public function salvar(): void
    {
        $this->empresa->alunos()->sync($this->selecionados);
        session()->flash('success', 'Vínculos atualizados com sucesso.');
    }

    public function render()
    {
        return view('livewire.admin.vincular-alunos-empresa', [
            'alunos' => Aluno::where('active', true)
                ->where(fn ($q) => $q
                    ->whereDoesntHave('empresas')
                    ->orWhereHas('empresas', fn ($qe) => $qe->where('empresa_id', $this->empresa->id))
                )
                ->when($this->search, fn ($q) => $q->where(fn ($qq) =>
                    $qq->where('nome', 'like', "%{$this->search}%")
                       ->orWhere('ra', 'like', "%{$this->search}%")
                ))
                ->with('turma')
                ->orderBy('nome')
                ->get(),
        ]);
    }
}
