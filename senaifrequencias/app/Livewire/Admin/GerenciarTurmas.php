<?php

// Namespace: este componente fica em app/Livewire/Admin/
namespace App\Livewire\Admin;

// Importações
use App\Models\Turma;    // Model da tabela "turmas"
use App\Models\User;     // Model da tabela "users" (para listar professores no dropdown)
use Livewire\Component;   // classe base de todo componente Livewire
use Livewire\WithPagination; // adiciona paginação automática

// Componente Livewire: GerenciarTurmas — CRUD completo de turmas
// Exibe tabela paginada com busca em tempo real e modal de criar/editar
class GerenciarTurmas extends Component
{
    // Trait de paginação: habilita paginate() e resetPage()
    use WithPagination;

    // ─── Propriedades de controle da interface ────────────────────────────
    public string $search    = '';    // texto da barra de busca
    public bool   $showModal = false; // controla visibilidade do modal
    public ?int   $editingId = null;  // ID da turma sendo editada (null = criando)

    // ─── Campos do formulário ─────────────────────────────────────────────
    public string $nome         = ''; // nome da turma (ex: "Turma A 2026")
    public string $curso        = ''; // nome do curso (ex: "Desenvolvimento de Sistemas")
    public string $ano          = ''; // ano letivo (ex: "2026")
    public string $professor_id = ''; // ID do professor selecionado no dropdown ('' = sem professor)

    // Chamado automaticamente quando $search muda
    // Retorna para a página 1 para que a busca recomece do início
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    // Abre o modal de criar ou editar turma
    // $id = null → criando nova; $id = número → editando existente
    public function openModal(?int $id = null): void
    {
        $this->resetValidation(); // limpa erros de formulário anteriores
        $this->editingId = $id;

        if ($id) {
            // Modo edição: carrega os dados da turma existente no formulário
            $t = Turma::findOrFail($id);
            $this->nome         = $t->nome;
            $this->curso        = $t->curso;
            $this->ano          = (string) $t->ano; // (string) converte int para string
            $this->professor_id = (string) ($t->professor_id ?? ''); // ?? '' se for null, usa string vazia
        } else {
            // Modo criação: limpa todos os campos
            $this->nome = $this->curso = $this->ano = $this->professor_id = '';
        }

        $this->showModal = true;
    }

    // Fecha o modal sem salvar
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingId = null; // limpa o ID para não "contaminar" a próxima abertura do modal
    }

    // Cria ou atualiza uma turma
    public function save(): void
    {
        // Validação dos dados do formulário
        $this->validate([
            'nome'         => 'required|min:3|max:150',
            'curso'        => 'required|min:3|max:150',
            'ano'          => 'required|integer|min:2000|max:2099', // deve ser um número entre 2000 e 2099
            'professor_id' => 'nullable|exists:users,id', // opcional; se informado, deve existir na tabela users
        ]);

        // Monta os dados para salvar
        $data = [
            'nome'         => $this->nome,
            'curso'        => $this->curso,
            'ano'          => $this->ano,
            // ?: retorna null se professor_id for '' ou 0 (vazio falso)
            // Isso garante que o banco recebe NULL e não uma string vazia
            'professor_id' => $this->professor_id ?: null,
        ];

        if ($this->editingId) {
            // Edição: atualiza a turma existente com os novos dados
            Turma::findOrFail($this->editingId)->update($data);
        } else {
            // Criação: insere nova turma no banco
            Turma::create($data);
        }

        $this->closeModal();
        session()->flash('success', $this->editingId ? 'Turma atualizada.' : 'Turma cadastrada.');
    }

    // Exclui uma turma (soft delete)
    public function deletar(int $id): void
    {
        // findOrFail(id) busca pelo id; se não existir, lança erro 404 automaticamente
        Turma::findOrFail($id)->delete(); // soft delete: preenche deleted_at
        session()->flash('success', 'Turma removida.');
    }

    // render() executa a query e renderiza a view
    public function render()
    {
        return view('livewire.admin.gerenciar-turmas', [
            // Busca turmas com o professor relacionado (withTrashed = inclui professores deletados)
            'turmas' => Turma::with(['professor' => fn ($q) => $q->withTrashed()])
                ->when($this->search, fn ($q) => $q->where(fn ($qq) =>
                    // Filtra por nome OU por nome do curso quando há texto na busca
                    $qq->where('nome', 'like', "%{$this->search}%")
                       ->orWhere('curso', 'like', "%{$this->search}%")
                ))
                ->orderBy('nome')
                ->paginate(10), // 10 registros por página

            // Lista de professores ativos para o dropdown do formulário
            'professores' => User::where('role', 'professor')->where('active', true)->orderBy('name')->get(),
        ]);
    }
}
