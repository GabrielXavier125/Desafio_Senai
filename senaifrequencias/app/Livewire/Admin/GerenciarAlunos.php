<?php

// Namespace: este componente fica em app/Livewire/Admin/
namespace App\Livewire\Admin;

// Importações
use App\Models\Aluno;     // Model da tabela "alunos"
use App\Models\Turma;     // Model da tabela "turmas" (para o dropdown de turmas)
use Livewire\Component;    // classe base de todo componente Livewire
use Livewire\WithPagination; // trait que adiciona paginação automática ao componente

// Componente Livewire: GerenciarAlunos — CRUD completo de alunos
// Exibe tabela paginada com busca em tempo real, modal de criar/editar, toggle ativo/inativo
class GerenciarAlunos extends Component
{
    // Adiciona paginação automática (métodos paginate(), resetPage(), etc.)
    use WithPagination;

    // ─── Propriedades de controle da interface ────────────────────────────

    public string $search      = '';    // texto digitado na barra de busca
    public string $filtroTurma = '';    // ID da turma selecionada no filtro ('' = todas)
    public bool   $showModal   = false; // controla se o modal está aberto
    public ?int   $editingId   = null;  // ID do aluno sendo editado (null = criando novo)

    // ─── Campos do formulário do modal ────────────────────────────────────
    public string $nome     = ''; // nome do aluno
    public string $ra       = ''; // Registro do Aluno (número único)
    public string $turma_id = ''; // ID da turma selecionada no dropdown

    // Chamado automaticamente quando $search muda
    // Volta para a página 1 da listagem para que a busca comece do início
    public function updatingSearch(): void   { $this->resetPage(); }

    // Chamado automaticamente quando $filtroTurma muda
    // Volta para a página 1 quando o filtro de turma é alterado
    public function updatingFiltroTurma(): void { $this->resetPage(); }

    // Abre o modal de criar ou editar aluno
    // $id = null → modo criação; $id = número → modo edição
    public function openModal(?int $id = null): void
    {
        $this->resetValidation(); // limpa erros de validação anteriores
        $this->editingId = $id;

        if ($id) {
            // Modo edição: preenche o formulário com os dados do aluno existente
            $a = Aluno::findOrFail($id); // busca o aluno; erro 404 se não existir
            $this->nome     = $a->nome;
            $this->ra       = $a->ra;
            $this->turma_id = (string) $a->turma_id; // converte para string pois o campo é string no Livewire
        } else {
            // Modo criação: limpa todos os campos do formulário
            $this->nome = $this->ra = $this->turma_id = '';
        }

        $this->showModal = true; // exibe o modal
    }

    // Fecha o modal sem salvar alterações
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingId = null;
    }

    // Cria ou atualiza um aluno quando o formulário é submetido
    public function save(): void
    {
        // Validação dos campos — o Laravel verifica antes de salvar
        $this->validate([
            'nome'     => 'required|min:3|max:150', // obrigatório, entre 3 e 150 caracteres
            // unique:alunos,ra = o RA não pode existir em outro aluno
            // ,{$this->editingId} = exceto no próprio aluno que está sendo editado (ignora si mesmo)
            'ra'       => 'required|max:30|unique:alunos,ra' . ($this->editingId ? ",{$this->editingId}" : ''),
            'turma_id' => 'required|exists:turmas,id', // deve existir na tabela turmas
        ]);

        // Array com os dados a salvar
        $data = [
            'nome'     => $this->nome,
            'ra'       => $this->ra,
            'turma_id' => $this->turma_id,
        ];

        if ($this->editingId) {
            // Modo edição: atualiza o aluno existente
            Aluno::findOrFail($this->editingId)->update($data);
        } else {
            // Modo criação: cria um novo aluno
            Aluno::create($data);
        }

        $this->closeModal(); // fecha o modal após salvar
        // Cria mensagem flash de sucesso — aparece brevemente na tela
        session()->flash('success', $this->editingId ? 'Aluno atualizado.' : 'Aluno cadastrado.');
    }

    // Ativa ou desativa um aluno (sem excluir — apenas muda o campo 'active')
    // Alunos desativados não aparecem na chamada do professor
    public function toggleAtivo(int $id): void
    {
        $aluno = Aluno::findOrFail($id);
        // ! $aluno->active inverte o valor: true → false, false → true
        $aluno->update(['active' => ! $aluno->active]);
        session()->flash('success', $aluno->active ? 'Aluno reativado.' : 'Aluno desativado.');
    }

    // Exclui permanentemente um aluno (soft delete — preserva histórico)
    public function excluir(int $id): void
    {
        $aluno = Aluno::findOrFail($id);
        // detach() remove os vínculos da tabela pivot empresa_aluno
        // É necessário fazer isso antes de excluir o aluno para não deixar registros órfãos
        $aluno->empresas()->detach();
        $aluno->delete(); // soft delete: preenche deleted_at, não remove do banco
        session()->flash('success', 'Aluno excluído.');
    }

    // render() é chamado toda vez que algo muda no componente
    // Executa a query e retorna a view atualizada
    public function render()
    {
        // Monta a query de forma dinâmica baseada nos filtros ativos
        $query = Aluno::with(['turma' => fn ($q) => $q->withTrashed(), 'empresas'])
            // withTrashed() inclui turmas excluídas — o aluno pode estar numa turma deletada
            ->when($this->search, fn ($q) => $q->where(fn ($qq) =>
                // Quando há texto na busca, filtra por nome OU por RA
                $qq->where('nome', 'like', "%{$this->search}%")  // % = qualquer texto antes/depois
                   ->orWhere('ra', 'like', "%{$this->search}%")
            ))
            ->when($this->filtroTurma, fn ($q) => $q->where('turma_id', $this->filtroTurma))
            // ->when(condição, callback) = aplica o filtro somente se a condição for verdadeira
            ->orderBy('nome'); // ordena em ordem alfabética

        return view('livewire.admin.gerenciar-alunos', [
            'alunos' => $query->paginate(10),         // 10 alunos por página
            'turmas' => Turma::orderBy('nome')->get(), // lista de turmas para o dropdown
        ]);
    }
}
