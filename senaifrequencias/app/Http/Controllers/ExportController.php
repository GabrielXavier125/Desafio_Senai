<?php

// Namespace: este controller fica diretamente em app/Http/Controllers/
namespace App\Http\Controllers;

// Importações
use App\Models\Aluno;    // Model da tabela alunos
use App\Models\Aula;     // Model da tabela aulas
use App\Models\Turma;    // Model da tabela turmas
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse; // tipo de resposta para download de arquivo

// ExportController — responsável por gerar e fazer o download dos arquivos CSV de frequência
// CSV = Comma-Separated Values (arquivo que o Excel consegue abrir)
class ExportController extends Controller
{
    // Exportação para o ADMIN: recebe a turma como parâmetro da URL
    // URL: GET /admin/turmas/{turma}/exportar
    // O Laravel injeta o objeto Turma automaticamente pelo ID na URL
    public function adminTurma(Turma $turma): StreamedResponse
    {
        return $this->exportTurma($turma); // delega para o método privado que faz o trabalho
    }

    // Exportação para o PROFESSOR: usa a turma do professor logado
    // URL: GET /professor/exportar
    public function professorTurma(Request $request): StreamedResponse
    {
        // Busca a turma vinculada ao professor logado
        // firstOrFail() → erro 404 se o professor não tiver turma
        $turma = $request->user()->turmas()->firstOrFail();
        return $this->exportTurma($turma);
    }

    // Método privado que realmente gera o CSV — usado pelos dois métodos acima
    // "private" significa que só pode ser chamado dentro desta classe (não pelas rotas)
    private function exportTurma(Turma $turma): StreamedResponse
    {
        // Busca todas as aulas da turma em ordem cronológica
        $aulas = Aula::where('turma_id', $turma->id)->orderBy('data')->get();

        // Busca todos os alunos da turma em ordem alfabética
        $alunos = Aluno::where('turma_id', $turma->id)->orderBy('nome')->get();

        // Busca todos os registros de chamada de todas as aulas desta turma de uma vez
        // Agrupa por aluno_id para facilitar a busca: $chamadas[aluno_id] = [lista de chamadas]
        $chamadas = \App\Models\Chamada::whereIn('aula_id', $aulas->pluck('id'))
            ->get()
            ->groupBy('aluno_id');

        // Monta o nome do arquivo com slug da turma e data atual
        // Str::slug transforma "Turma A 2026" em "turma-a-2026" (sem espaços, minúsculas)
        $filename = 'frequencia_' . \Illuminate\Support\Str::slug($turma->nome) . '_' . now()->format('Y-m-d') . '.csv';

        // response()->streamDownload() envia o arquivo como download ao navegador sem salvar no servidor
        return response()->streamDownload(function () use ($aulas, $alunos, $chamadas) {

            // Abre um "arquivo" na memória para escrita (php://output = saída direta para o navegador)
            $handle = fopen('php://output', 'w');

            // BOM (Byte Order Mark) — 3 bytes especiais no início do arquivo
            // Necessário para que o Excel abra o CSV com acentos corretamente (UTF-8)
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // ─── Linha de cabeçalho ─────────────────────────────────────────
            $header = ['Aluno', 'RA']; // primeiras duas colunas fixas

            // Para cada aula, adiciona uma coluna com a data formatada (ex: "15/05/2026")
            foreach ($aulas as $aula) {
                $header[] = \Carbon\Carbon::parse($aula->data)->format('d/m/Y');
            }

            $header[] = 'Total Presenças'; // coluna de totais ao final
            $header[] = 'Total Faltas';
            $header[] = '% Frequência';

            // fputcsv escreve uma linha no CSV separando os valores com ';'
            // Ponto-e-vírgula porque Excel brasileiro usa ';' como separador
            fputcsv($handle, $header, ';');

            // ─── Uma linha por aluno ────────────────────────────────────────
            foreach ($alunos as $aluno) {
                $row = [$aluno->nome, $aluno->ra]; // começa com nome e RA do aluno
                $presencas = 0; // contador de presenças
                $faltas    = 0; // contador de faltas

                // Para cada aula, verifica o status deste aluno
                foreach ($aulas as $aula) {
                    // Busca o registro de chamada: $chamadas[aluno_id]->firstWhere('aula_id', ...)
                    // ?-> é "optional chaining": se não encontrar, retorna null sem dar erro
                    // ?? 'falta' → se não existir registro, considera falta
                    $status = $chamadas->get($aluno->id)?->firstWhere('aula_id', $aula->id)?->status ?? 'falta';

                    $row[] = $status === 'presente' ? 'P' : 'F'; // P = Presente, F = Falta
                    $status === 'presente' ? $presencas++ : $faltas++; // incrementa o contador correto
                }

                // Calcula o percentual de frequência
                $total = $presencas + $faltas;
                $pct   = $total > 0 ? round($presencas / $total * 100) . '%' : '0%';
                // round() arredonda para inteiro. Ex: 0.857 → 86%

                // Adiciona os totais ao final da linha
                $row[] = $presencas;
                $row[] = $faltas;
                $row[] = $pct;

                fputcsv($handle, $row, ';'); // escreve a linha no arquivo
            }

            fclose($handle); // fecha o arquivo (libera memória)

        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8', // informa ao navegador que é um arquivo CSV
        ]);
    }
}
