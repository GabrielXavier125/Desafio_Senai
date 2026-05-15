<x-senai-layout title="Frequências dos Alunos">

    @if($empresa)
        <div class="mb-5">
            <h2 class="text-2xl font-bold text-gray-800">{{ $empresa->nome }}</h2>
            <p class="text-gray-500 text-sm mt-1">CNPJ: {{ $empresa->cnpj }}</p>
        </div>

        @if($empresa->alunos->isEmpty())
            <div class="bg-white rounded-xl shadow-sm p-10 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                </svg>
                <p class="text-gray-500 font-medium">Nenhum aluno vinculado.</p>
                <p class="text-gray-400 text-sm mt-1">O administrador ainda não vinculou alunos à sua empresa.</p>
            </div>
        @else
            @php
                $alunosJson = $empresa->alunos->map(fn($a) => [
                    'nome'  => $a->nome,
                    'ra'    => $a->ra,
                    'turma' => $a->turma?->nome ?? '',
                ])->toJson();
                $turmas = $empresa->alunos->pluck('turma.nome')->filter()->unique()->sort()->values();
            @endphp

            <div x-data="{
                    busca: '',
                    turmaFiltro: '',
                    alunos: {{ $alunosJson }},
                    visivel(nome, ra, turma) {
                        const q = this.busca.toLowerCase().trim();
                        const nomeOk = !q || nome.toLowerCase().includes(q) || ra.toLowerCase().includes(q);
                        const turmaOk = !this.turmaFiltro || turma === this.turmaFiltro;
                        return nomeOk && turmaOk;
                    },
                    get temResultados() {
                        return this.alunos.some(a => this.visivel(a.nome, a.ra, a.turma));
                    }
                }">

                {{-- Barra de busca e filtro --}}
                <div class="flex flex-col sm:flex-row gap-3 mb-5">
                    <div class="relative flex-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                        <input x-model="busca"
                               type="text"
                               placeholder="Buscar por nome ou RA..."
                               class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-senai">
                    </div>

                    @if($turmas->isNotEmpty())
                        <select x-model="turmaFiltro"
                                class="w-full sm:w-52 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-senai bg-white">
                            <option value="">Todas as turmas</option>
                            @foreach($turmas as $turma)
                                <option value="{{ $turma }}">{{ $turma }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>

                {{-- Grid de alunos --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($empresa->alunos as $aluno)
                        <a href="{{ route('empresa.alunos.historico', $aluno) }}"
                           x-show="visivel({{ Js::from($aluno->nome) }}, {{ Js::from($aluno->ra) }}, {{ Js::from($aluno->turma?->nome ?? '') }})"
                           class="bg-white rounded-xl shadow-sm p-5 border border-gray-100 hover:border-senai hover:shadow-md transition-all group">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 rounded-full bg-senai/10 flex items-center justify-center text-senai font-bold text-sm flex-shrink-0">
                                    {{ strtoupper(mb_substr($aluno->nome, 0, 2)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-semibold text-gray-800 text-sm truncate">{{ $aluno->nome }}</p>
                                    <p class="text-xs text-gray-500">RA: {{ $aluno->ra }}</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="text-gray-500">{{ $aluno->turma?->nome ?? '—' }}</span>
                                <span class="text-senai font-medium group-hover:underline">Ver histórico →</span>
                            </div>
                        </a>
                    @endforeach
                </div>

                {{-- Sem resultados --}}
                <div x-show="!temResultados" x-cloak
                     class="bg-white rounded-xl shadow-sm p-10 text-center mt-2">
                    <p class="text-gray-400 text-sm">Nenhum aluno encontrado para "<span x-text="busca"></span>".</p>
                </div>
            </div>
        @endif
    @else
        <div class="bg-white rounded-xl shadow-sm p-10 text-center">
            <p class="text-gray-500">Empresa não configurada. Contate o administrador.</p>
        </div>
    @endif

</x-senai-layout>
