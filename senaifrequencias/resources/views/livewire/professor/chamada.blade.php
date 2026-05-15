<div>
    {{-- Header: data e descrição --}}
    <div class="bg-white rounded-xl shadow-sm p-5 mb-6">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">
                    {{ \Carbon\Carbon::parse($aula->data)->translatedFormat('l, d \d\e F \d\e Y') }}
                </p>
                <p class="text-gray-700 text-sm leading-relaxed">{{ $aula->descricao }}</p>
            </div>

            <div class="flex-shrink-0 flex gap-4 text-center">
                <div class="min-w-[56px]">
                    <p class="text-2xl font-bold text-green-600">{{ $this->getPresentes() }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">presentes</p>
                </div>
                <div class="min-w-[56px]">
                    <p class="text-2xl font-bold text-red-500">{{ $this->getFaltas() }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">faltas</p>
                </div>
            </div>
        </div>

        @php
            $total = count($statuses);
            $pct   = $total > 0 ? round($this->getPresentes() / $total * 100) : 0;
        @endphp
        <div class="mt-4">
            <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                <span>Presença geral</span>
                <span class="font-semibold text-gray-700">{{ $pct }}%</span>
            </div>
            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-green-400 rounded-full transition-all duration-300"
                     style="width: {{ $pct }}%"></div>
            </div>
        </div>
    </div>

    {{-- Lista de alunos --}}
    @if($aula->turma->alunos->isEmpty())
        <div class="bg-white rounded-xl shadow-sm p-10 text-center">
            <p class="text-gray-400">Nenhum aluno ativo nesta turma.</p>
        </div>
    @else
        <div class="space-y-2">
            @foreach($aula->turma->alunos as $aluno)
                @php $status = $statuses[$aluno->id] ?? 'falta'; @endphp

                <div wire:key="aluno-{{ $aluno->id }}"
                     x-data="{ presente: {{ $status === 'presente' ? 'true' : 'false' }} }"
                     class="bg-white rounded-xl shadow-sm px-5 py-4 flex items-center gap-4">

                    <div class="flex-shrink-0 w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white"
                         :class="presente ? 'bg-green-500' : 'bg-red-400'">
                        {{ strtoupper(mb_substr($aluno->nome, 0, 1)) }}
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $aluno->nome }}</p>
                        <p class="text-xs text-gray-400">RA {{ $aluno->ra }}</p>
                    </div>

                    <button type="button"
                            @click="presente = !presente; $wire.toggle({{ $aluno->id }})"
                            class="flex-shrink-0 relative inline-flex h-7 w-14 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2"
                            :class="presente ? 'bg-green-500 focus:ring-green-500' : 'bg-gray-200 focus:ring-gray-400'">
                        <span class="sr-only" x-text="presente ? 'Marcar falta' : 'Marcar presença'"></span>
                        <span class="inline-block h-5 w-5 rounded-full bg-white shadow transition-transform"
                              :class="presente ? 'translate-x-8' : 'translate-x-1'"></span>
                    </button>

                    <span class="flex-shrink-0 w-14 text-xs font-semibold text-right"
                          :class="presente ? 'text-green-600' : 'text-gray-400'"
                          x-text="presente ? 'Presente' : 'Falta'"></span>
                </div>
            @endforeach
        </div>
    @endif
</div>
