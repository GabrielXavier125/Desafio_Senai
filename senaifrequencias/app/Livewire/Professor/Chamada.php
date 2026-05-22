<?php

// Namespace: este componente fica em app/Livewire/Professor/
namespace App\Livewire\Professor;

// Importações
use App\Models\Aula;                      // Model da tabela "aulas"
use App\Models\Chamada as ChamadaModel;   // Model da tabela "chamadas" — "as" evita conflito com o nome da classe
use Livewire\Component;                    // classe base de todo componente Livewire

// Componente Livewire: Chamada — tela de chamada de presença do professor
// Exibe a lista de alunos da turma com toggles Presente/Falta
// Atualiza o banco de dados em tempo real sem recarregar a página
class Chamada extends Component
{
    // Propriedade pública: o objeto Aula que está sendo chamada
    // Livewire serializa/deserializa esta propriedade automaticamente entre requisições
    public Aula $aula;

    // Array que guarda o status atual de cada aluno
    // Formato: [aluno_id => 'presente'|'falta']
    // Exemplo: [3 => 'presente', 7 => 'falta', 12 => 'presente']
    public array $statuses = [];

    // mount() é chamado uma única vez quando o componente é carregado na página
    // É como o "construtor" do componente — inicializa os dados
    public function mount(Aula $aula): void
    {
        // Carrega a aula com sua turma e os alunos ATIVOS da turma
        // ->load([...]) faz um único SQL com JOIN ao invés de múltiplas consultas separadas
        // fn ($q) => $q->where('active', true) filtra só os alunos ativos
        $this->aula = $aula->load(['turma.alunos' => fn ($q) => $q->where('active', true)]);

        // Busca todos os registros de chamada existentes para esta aula
        // ->pluck('status', 'aluno_id') cria um array associativo:
        // chave = aluno_id, valor = status → [3 => 'presente', 7 => 'falta']
        $chamadas = ChamadaModel::where('aula_id', $aula->id)
            ->pluck('status', 'aluno_id')
            ->toArray(); // converte de Collection do Laravel para array PHP nativo

        // Para cada aluno da turma, define o status inicial
        foreach ($this->aula->turma->alunos as $aluno) {
            // Se existe registro de chamada para este aluno, usa ele
            // Se não existe ($chamadas[$aluno->id] não está definido), usa 'falta' como padrão
            // ?? é o operador "null coalescing": "se não existir, use este valor"
            $this->statuses[$aluno->id] = $chamadas[$aluno->id] ?? 'falta';
        }
    }

    // toggle() é chamado quando o professor clica no botão de um aluno
    // int $alunoId = o ID do aluno cujo status está sendo alterado
    public function toggle(int $alunoId): void
    {
        // Inverte o status: se era 'presente' vira 'falta', se era 'falta' vira 'presente'
        // Operador ternário: condição ? valor_se_verdade : valor_se_falso
        $novo = $this->statuses[$alunoId] === 'presente' ? 'falta' : 'presente';

        // updateOrCreate(condições de busca, dados para salvar)
        // 1. Tenta encontrar um registro com aula_id=X e aluno_id=Y
        // 2. Se encontrar: atualiza o status
        // 3. Se não encontrar: cria um novo registro
        // Isso é necessário porque a tabela tem restrição UNIQUE(aula_id, aluno_id)
        // — não pode inserir duplicata, então precisa atualizar se já existe
        ChamadaModel::updateOrCreate(
            ['aula_id' => $this->aula->id, 'aluno_id' => $alunoId], // condição de busca
            ['status' => $novo, 'updated_at' => now()]               // dados a salvar/atualizar
        );

        // Atualiza o array local para refletir o novo status no PHP
        $this->statuses[$alunoId] = $novo;

        // Dispara um evento JavaScript chamado 'status-saved'
        // A view pode escutar este evento para dar feedback visual
        $this->dispatch('status-saved', alunoId: $alunoId);
    }

    // Conta quantos alunos estão marcados como presentes
    // array_filter filtra apenas os valores iguais a 'presente'
    // count() conta quantos sobraram
    public function getPresentes(): int
    {
        return count(array_filter($this->statuses, fn ($s) => $s === 'presente'));
    }

    // Conta quantos alunos estão marcados como falta
    public function getFaltas(): int
    {
        return count(array_filter($this->statuses, fn ($s) => $s === 'falta'));
    }

    // render() é chamado automaticamente toda vez que o componente precisa ser re-renderizado
    // Retorna a view Blade que representa este componente na tela
    public function render()
    {
        return view('livewire.professor.chamada');
        // O Livewire passa automaticamente todas as propriedades públicas para a view:
        // $aula, $statuses estarão disponíveis no .blade.php
    }
}
