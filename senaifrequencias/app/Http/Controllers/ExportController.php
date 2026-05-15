<?php

namespace App\Http\Controllers;

use App\Models\Aluno;
use App\Models\Aula;
use App\Models\Turma;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /** Admin: exporta frequência de uma turma específica */
    public function adminTurma(Turma $turma): StreamedResponse
    {
        return $this->exportTurma($turma);
    }

    /** Professor: exporta frequência da própria turma */
    public function professorTurma(Request $request): StreamedResponse
    {
        $turma = $request->user()->turmas()->firstOrFail();
        return $this->exportTurma($turma);
    }

    private function exportTurma(Turma $turma): StreamedResponse
    {
        $aulas = Aula::where('turma_id', $turma->id)
            ->orderBy('data')
            ->get();

        $alunos = Aluno::where('turma_id', $turma->id)
            ->orderBy('nome')
            ->get();

        // Índice: aluno_id → aula_id → status
        $chamadas = \App\Models\Chamada::whereIn('aula_id', $aulas->pluck('id'))
            ->get()
            ->groupBy('aluno_id');

        $filename = 'frequencia_' . \Illuminate\Support\Str::slug($turma->nome) . '_' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($aulas, $alunos, $chamadas) {
            $handle = fopen('php://output', 'w');

            // BOM para Excel reconhecer UTF-8
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Cabeçalho
            $header = ['Aluno', 'RA'];
            foreach ($aulas as $aula) {
                $header[] = \Carbon\Carbon::parse($aula->data)->format('d/m/Y');
            }
            $header[] = 'Total Presenças';
            $header[] = 'Total Faltas';
            $header[] = '% Frequência';
            fputcsv($handle, $header, ';');

            // Linhas por aluno
            foreach ($alunos as $aluno) {
                $row = [$aluno->nome, $aluno->ra];
                $presencas = 0;
                $faltas    = 0;

                foreach ($aulas as $aula) {
                    $status = $chamadas->get($aluno->id)?->firstWhere('aula_id', $aula->id)?->status ?? 'falta';
                    $row[] = $status === 'presente' ? 'P' : 'F';
                    $status === 'presente' ? $presencas++ : $faltas++;
                }

                $total = $presencas + $faltas;
                $pct   = $total > 0 ? round($presencas / $total * 100) . '%' : '0%';

                $row[] = $presencas;
                $row[] = $faltas;
                $row[] = $pct;
                fputcsv($handle, $row, ';');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
