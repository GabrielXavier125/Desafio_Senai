<?php

// Namespace: este componente fica em app/Livewire/Professor/
namespace App\Livewire\Professor;

// Importações
use App\Models\Aula;     // Model da tabela "aulas"
use App\Models\Chamada;  // Model da tabela "chamadas"
use App\Models\Turma;    // Model da tabela "turmas"
use Livewire\Component;   // classe base de todo componente Livewire

// Componente Livewire: TurmaDashboard — painel principal do professor
// Exibe estatísticas da turma e lista de aulas com contagem de presentes/faltas
// Permite criar, editar e excluir aulas diretamente desta tela
class TurmaDashboard extends Component
{
    // A turma do professor logado (null se ainda não carregada ou professor sem turma)
    public ?Turma $turma = null;

    // ─── Estado do modal de Nova Aula ──────────────────────────────────────
    public bool   $showModal = false; // controla se o modal está aberto (true) ou fechado (false)
    public string $data      = '';    // campo "data" do formulário de nova aula
    public string $descricao = '';    // campo "descrição" do formulário de nova aula

    // ─── Estado do modal de Editar Aula ───────────────────────────────────
    public bool   $showEditModal  = false; // controla se o modal de edição está aberto
    public ?int   $editingAulaId  = null;  // ID da aula sendo editada (null quando não está editando)
    public string $editData       = '';    // campo data no formulário de edição
    public string $editDescricao  = '';    // campo descrição no formulário de edição

    // mount() é o construtor do componente — executado uma única vez ao carregar a página
    public function mount(): void
    {
        // Busca a turma vinculada ao professor logado
        // auth()->user() → retorna o usuário logado atualmente
        // ->turmas() → acessa o relacionamento do User com Turma
        // ->with([...]) → carrega os alunos ativos junto com a turma (evita N+1 queries)
        // ->first() → pega a primeira turma (professor tem 1 turma) ou null se não tiver
        $this->turma = auth()->user()
            ->turmas()
            ->with(['alunos' => fn ($q) => $q->where('active', true)])
            ->first();

        // Define a data padrão do campo "data" como hoje (formato aceito pelo input type="date")
        $this->data = now()->format('Y-m-d');
    }

    // ─── Modal de Nova Aula ────────────────────────────────────────────────

    // Abre o modal de criar nova aula e limpa o formulário
    public function openModal(): void
    {
        $this->resetValidation(); // limpa as mensagens de erro de validação do formulário anterior
        $this->data      = now()->format('Y-m-d'); // preenche a data com hoje como padrão
        $this->descricao = '';    // limpa o campo de descrição
        $this->showModal = true;  // exibe o modal na tela
    }

    // Fecha o modal de nova aula sem salvar
    public function closeModal(): void
    {
        $this->showModal = false; // esconde o modal
    }

    // Cria uma nova aula quando o professor submete o formulário
    public function criarAula(): void
    {
        // Valida os campos do formulário antes de salvar
        // 'required' = campo obrigatório
        // 'date' = deve ser uma data válida
        // 'min:3|max:1000' = texto entre 3 e 1000 caracteres
        $this->validate([
            'data'      => 'required|date',
            'descricao' => 'required|min:3|max:1000',
        ]);

        // Cria a aula no banco de dados com os dados do formulário
        $aula = Aula::create([
            'turma_id'  => $this->turma->id, // vincula à turma do professor
            'data'      => $this->data,
            'descricao' => $this->descricao,
        ]);

        // Cria automaticamente um registro de chamada "falta" para cada aluno da turma
        // Assim, quando o professor abrir a chamada, todos já aparecem como falta por padrão
        $alunos = $this->turma->alunos;
        foreach ($alunos as $aluno) {
            // firstOrCreate: cria o registro se não existir, não faz nada se já existir
            Chamada::firstOrCreate(
                ['aula_id' => $aula->id, 'aluno_id' => $aluno->id], // condição de busca
                ['status' => 'falta', 'updated_at' => now()]          // dados para criar se não existir
            );
        }

        $this->closeModal(); // fecha o modal

        // Redireciona para a tela de chamada da nova aula criada
        // navigate: false = usa navegação padrão (não o sistema SPA do Livewire)
        $this->redirect(route('professor.aulas.chamada', $aula), navigate: false);
    }

    // ─── Modal de Editar Aula ──────────────────────────────────────────────

    // Abre o modal de edição preenchido com os dados da aula selecionada
    public function openEditModal(int $aulaId): void
    {
        $this->resetValidation(); // limpa erros de validação anteriores

        $aula = Aula::findOrFail($aulaId); // busca a aula no banco; erro 404 se não encontrar

        $this->editingAulaId = $aulaId;                     // guarda qual aula está sendo editada
        $this->editData      = $aula->data->format('Y-m-d'); // preenche o campo data (formato ISO)
        $this->editDescricao = $aula->descricao;             // preenche o campo de descrição
        $this->showEditModal = true;                          // exibe o modal de edição
    }

    // Fecha o modal de edição sem salvar
    public function closeEditModal(): void
    {
        $this->showEditModal = false; // esconde o modal
        $this->editingAulaId = null;  // limpa o ID da aula que estava sendo editada
    }

    // Salva as alterações de uma aula editada
    public function salvarEdicao(): void
    {
        // Valida os campos do formulário de edição
        // O terceiro argumento do validate() personaliza os rótulos dos campos nas mensagens de erro
        $this->validate([
            'editData'      => 'required|date',
            'editDescricao' => 'required|min:3|max:1000',
        ], [], [
            'editData'      => 'data',       // mensagem de erro vai dizer "o campo data é obrigatório"
            'editDescricao' => 'conteúdo',   // em vez de "o campo editDescricao é obrigatório"
        ]);

        // Busca a aula e atualiza com os novos dados
        Aula::findOrFail($this->editingAulaId)->update([
            'data'      => $this->editData,
            'descricao' => $this->editDescricao,
        ]);

        $this->closeEditModal(); // fecha o modal

        // session()->flash() cria uma mensagem temporária que aparece na próxima requisição
        session()->flash('success', 'Aula atualizada.');
    }

    // ─── Excluir Aula ──────────────────────────────────────────────────────

    // Exclui uma aula e todos os seus registros de chamada
    public function excluirAula(int $aulaId): void
    {
        $aula = Aula::findOrFail($aulaId); // busca a aula; erro 404 se não existir
        $aula->chamadas()->delete();        // exclui todos os registros de chamada desta aula primeiro
        $aula->delete();                    // depois exclui a aula em si
        // Exclui as chamadas antes da aula porque o banco tem FK: chamadas.aula_id → aulas.id
        session()->flash('success', 'Aula removida.');
    }

    // render() é chamado toda vez que o componente precisa ser re-renderizado
    public function render()
    {
        // Se o professor não tiver turma vinculada, exibe a view sem dados
        if (! $this->turma) {
            return view('livewire.professor.turma-dashboard');
        }

        // Busca todas as aulas da turma com contagem de presentes e faltas
        $aulas = Aula::where('turma_id', $this->turma->id)
            ->withCount([
                // withCount cria colunas extras: $aula->presentes e $aula->faltas
                'chamadas as presentes' => fn ($q) => $q->where('status', 'presente'), // conta as presenças
                'chamadas as faltas'    => fn ($q) => $q->where('status', 'falta'),    // conta as faltas
            ])
            ->orderByDesc('data') // mais recentes primeiro
            ->get();

        // Passa as aulas para a view com os contadores já calculados
        return view('livewire.professor.turma-dashboard', compact('aulas'));
    }
}
