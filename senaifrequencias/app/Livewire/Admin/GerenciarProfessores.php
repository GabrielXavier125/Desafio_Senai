<?php

// Namespace: este componente fica em app/Livewire/Admin/
namespace App\Livewire\Admin;

// Importações
use App\Models\Turma;    // Model da tabela "turmas" (para vincular professor à turma)
use App\Models\User;     // Model da tabela "users" (professores são usuários com role='professor')
use Illuminate\Support\Facades\Hash; // para criptografar a senha antes de salvar
use Livewire\Attributes\Validate;    // atributo PHP 8 para validação inline
use Livewire\Component;               // classe base de todo componente Livewire
use Livewire\WithPagination;          // adiciona paginação automática

// Componente Livewire: GerenciarProfessores — CRUD de professores + vínculo com turma
// Cria/edita professores e permite vincular cada professor à sua turma
class GerenciarProfessores extends Component
{
    // Trait de paginação: habilita paginate() e resetPage()
    use WithPagination;

    // ─── Propriedades de controle da interface ────────────────────────────
    public string $search    = '';    // texto da barra de busca em tempo real
    public bool   $showModal = false; // controla se o modal está aberto
    public ?int   $editingId = null;  // ID do professor sendo editado (null = criando)

    // ─── Campos do formulário — com validação via atributo #[Validate] ────

    // #[Validate] define a regra de validação usando atributo PHP 8
    // 'required' = obrigatório | 'min:3' = mínimo 3 caracteres | 'max:150' = máximo 150
    #[Validate('required|min:3|max:150')]
    public string $nome = '';

    // 'email' = deve ser um endereço de e-mail válido
    #[Validate('required|email|max:150')]
    public string $email = '';

    // 'nullable' = pode ser vazio (ao editar, deixar em branco = não alterar a senha)
    #[Validate('nullable|min:6|max:100')]
    public string $senha = '';

    // ID da turma selecionada no dropdown ('' = sem turma)
    public string $turmaId = '';

    // Chamado automaticamente quando $search muda — reseta para a página 1
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    // Abre o modal de criar ou editar professor
    // $id = null → criando; $id = número → editando
    public function openModal(?int $id = null): void
    {
        $this->resetValidation(); // limpa mensagens de erro do formulário anterior
        $this->editingId = $id;

        if ($id) {
            // Modo edição: preenche o formulário com os dados do professor existente
            $prof = User::findOrFail($id);
            $this->nome    = $prof->name;  // name = nome completo (campo do banco)
            $this->email   = $prof->email;
            $this->senha   = '';           // nunca pré-preenche senha por segurança
            // Busca qual turma este professor está vinculado
            // ?->id = se encontrou a turma, pega o id; se não encontrou (null), retorna null
            // ?? '' = se null, usa string vazia (nenhuma turma selecionada)
            $this->turmaId = (string) (Turma::where('professor_id', $id)->first()?->id ?? '');
        } else {
            // Modo criação: limpa todos os campos
            $this->nome    = '';
            $this->email   = '';
            $this->senha   = '';
            $this->turmaId = '';
        }

        $this->showModal = true;
    }

    // Fecha o modal sem salvar
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingId = null;
    }

    // Cria ou atualiza um professor e gerencia o vínculo com a turma
    public function save(): void
    {
        // Validação manual aqui porque as regras variam conforme o contexto:
        // - E-mail precisa ser único, mas ignorar o próprio usuário na edição
        // - Senha é obrigatória na criação, mas opcional na edição
        $rules = [
            'nome'  => 'required|min:3|max:150',
            // unique:users,email = e-mail não pode existir em outro usuário
            // ,{$this->editingId} = exceto o próprio usuário que está sendo editado
            'email' => 'required|email|max:150|unique:users,email' . ($this->editingId ? ",{$this->editingId}" : ''),
            // Na criação (editingId = null): senha obrigatória
            // Na edição (editingId = número): senha opcional (nullable)
            'senha' => $this->editingId ? 'nullable|min:6|max:100' : 'required|min:6|max:100',
        ];

        $this->validate($rules);

        // Dados base para criar/atualizar o usuário
        $data = [
            'name'  => $this->nome,
            'email' => $this->email,
            'role'  => 'professor', // garante que o role seja sempre 'professor'
        ];

        // Só adiciona a senha se foi preenchida
        // Hash::make() criptografa a senha usando bcrypt — nunca salva a senha em texto puro
        if ($this->senha) {
            $data['password'] = Hash::make($this->senha);
        }

        if ($this->editingId) {
            // Edição: atualiza o usuário existente e guarda o ID
            $prof = User::findOrFail($this->editingId);
            $prof->update($data);
            $profId = $this->editingId;
        } else {
            // Criação: cria novo usuário e pega o ID gerado
            $prof = User::create($data);
            $profId = $prof->id;
        }

        // ─── Gerenciamento do vínculo professor ↔ turma ──────────────────
        // Passo 1: Remove qualquer turma que estava vinculada a este professor
        // (garante que um professor só pode ter UMA turma por vez)
        Turma::where('professor_id', $profId)->update(['professor_id' => null]);

        // Passo 2: Se uma turma foi selecionada no dropdown, vincula a ela
        if ($this->turmaId) {
            Turma::findOrFail((int) $this->turmaId)->update(['professor_id' => $profId]);
        }

        $this->closeModal();
        session()->flash('success', $this->editingId ? 'Professor atualizado.' : 'Professor cadastrado.');
    }

    // Ativa ou desativa um professor
    public function toggleAtivo(int $id): void
    {
        $prof = User::findOrFail($id);
        $prof->update(['active' => ! $prof->active]); // inverte: true → false, false → true
        session()->flash('success', $prof->active ? 'Professor reativado.' : 'Professor desativado.');
    }

    // Exclui um professor (soft delete)
    public function excluir(int $id): void
    {
        User::findOrFail($id)->delete(); // soft delete: preenche deleted_at
        session()->flash('success', 'Professor excluído.');
    }

    // render() busca os dados e renderiza a view
    public function render()
    {
        return view('livewire.admin.gerenciar-professores', [
            // Lista de professores com filtro de busca, paginada
            'professores' => User::where('role', 'professor')
                ->where(fn ($q) => $q
                    ->where('name', 'like', "%{$this->search}%")   // busca por nome
                    ->orWhere('email', 'like', "%{$this->search}%") // ou por e-mail
                )
                ->orderBy('name')
                ->paginate(10), // 10 por página

            // Lista completa de turmas para o dropdown do formulário
            'turmas' => Turma::orderBy('nome')->get(),
        ]);
    }
}
