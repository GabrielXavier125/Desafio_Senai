<div>
    {{-- ─────────────────────────────────────────────────────────────────────
         Card do cabeçalho: exibe data, descrição da aula e contadores
         ───────────────────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm p-5 mb-6">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
                {{-- Carbon::parse() converte a data do banco para objeto com métodos de formatação --}}
                {{-- translatedFormat() formata em português: "Quinta-feira, 15 de Maio de 2026" --}}
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">
                    {{ \Carbon\Carbon::parse($aula->data)->translatedFormat('l, d \d\e F \d\e Y') }}
                </p>
                {{-- $aula->descricao = texto salvo no banco pelo administrador --}}
                <p class="text-gray-700 text-sm leading-relaxed">{{ $aula->descricao }}</p>
            </div>

            {{-- Contadores de presentes e faltas — $this->getPresentes() chama o método PHP do componente --}}
            <div class="flex-shrink-0 flex gap-4 text-center">
                <div class="min-w-[56px]">
                    {{-- getPresentes() conta quantos alunos têm status='presente' no array $statuses --}}
                    <p class="text-2xl font-bold text-green-600">{{ $this->getPresentes() }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">presentes</p>
                </div>
                <div class="min-w-[56px]">
                    {{-- getFaltas() conta quantos alunos têm status='falta' no array $statuses --}}
                    <p class="text-2xl font-bold text-red-500">{{ $this->getFaltas() }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">faltas</p>
                </div>
            </div>
        </div>

        {{-- ─── Barra de progresso de frequência ──────────────────────────── --}}
        @php
            {{-- Calcula o percentual de presença --}}
            $total = count($statuses); {{-- conta o total de alunos --}}
            {{-- Se total > 0, calcula a porcentagem; senão usa 0 (evita divisão por zero) --}}
            $pct   = $total > 0 ? round($this->getPresentes() / $total * 100) : 0;
        @endphp
        <div class="mt-4">
            <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                <span>Presença geral</span>
                <span class="font-semibold text-gray-700">{{ $pct }}%</span>
            </div>
            {{-- Barra cinza de fundo --}}
            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                {{-- Barra verde que cresce conforme o percentual --}}
                {{-- style="width: X%" define o tamanho dinamicamente via PHP --}}
                {{-- transition-all duration-300 = animação suave quando o percentual muda --}}
                <div class="h-full bg-green-400 rounded-full transition-all duration-300"
                     style="width: {{ $pct }}%"></div>
            </div>
        </div>
    </div>

    {{-- ─────────────────────────────────────────────────────────────────────
         Lista de alunos com toggles de presença
         ───────────────────────────────────────────────────────────────────── --}}
    {{-- Verifica se a turma tem alunos ativos --}}
    @if($aula->turma->alunos->isEmpty())
        {{-- Estado vazio: nenhum aluno ativo na turma --}}
        <div class="bg-white rounded-xl shadow-sm p-10 text-center">
            <p class="text-gray-400">Nenhum aluno ativo nesta turma.</p>
        </div>
    @else
        {{-- space-y-2 = espaçamento vertical de 0.5rem entre cada card de aluno --}}
        <div class="space-y-2">
            {{-- Loop por cada aluno ativo da turma --}}
            @foreach($aula->turma->alunos as $aluno)
                {{-- Pega o status atual deste aluno: 'presente' ou 'falta' --}}
                {{-- ?? 'falta' = se não tiver registro no array, usa 'falta' como padrão --}}
                @php $status = $statuses[$aluno->id] ?? 'falta'; @endphp

                {{-- wire:key = identificador único para o Livewire saber qual elemento é qual
                     Necessário em loops para o Livewire fazer atualização correta do DOM --}}
                {{-- x-data = inicializa um "mini componente" Alpine para este aluno
                     'presente' é uma variável local que guarda o estado visual do toggle
                     O PHP injeta true ou false conforme o status do banco --}}
                <div wire:key="aluno-{{ $aluno->id }}"
                     x-data="{ presente: {{ $status === 'presente' ? 'true' : 'false' }} }"
                     class="bg-white rounded-xl shadow-sm px-5 py-4 flex items-center gap-4">

                    {{-- Avatar circular com a inicial do nome do aluno --}}
                    {{-- :class com dois pontos = class reativa ao Alpine (muda conforme 'presente') --}}
                    {{-- 'bg-green-500' quando presente, 'bg-red-400' quando falta --}}
                    <div class="flex-shrink-0 w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold text-white"
                         :class="presente ? 'bg-green-500' : 'bg-red-400'">
                        {{-- strtoupper = maiúsculo | mb_substr = pega 1 caractere (suporte a UTF-8/acentos) --}}
                        {{ strtoupper(mb_substr($aluno->nome, 0, 1)) }}
                    </div>

                    {{-- Nome e RA do aluno --}}
                    <div class="flex-1 min-w-0">
                        {{-- truncate = corta o texto com "..." se for muito longo --}}
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $aluno->nome }}</p>
                        <p class="text-xs text-gray-400">RA {{ $aluno->ra }}</p>
                    </div>

                    {{-- ─── Botão Toggle (interruptor) ──────────────────── --}}
                    <button type="button"
                            {{-- @click = evento de clique do Alpine.js
                                 Duas coisas acontecem SIMULTANEAMENTE ao clicar:
                                 1) presente = !presente → inverte o estado visual IMEDIATAMENTE (sem esperar servidor)
                                    Isso é "UI Otimista" — o visual muda na hora para parecer rápido
                                 2) $wire.toggle(id) → chama o método toggle() do PHP via Livewire em segundo plano
                                    O banco de dados é atualizado de forma assíncrona (sem travar a tela) --}}
                            @click="presente = !presente; $wire.toggle({{ $aluno->id }})"
                            class="flex-shrink-0 relative inline-flex h-7 w-14 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2"
                            {{-- Cor do fundo do toggle: verde quando presente, cinza quando falta --}}
                            :class="presente ? 'bg-green-500 focus:ring-green-500' : 'bg-gray-200 focus:ring-gray-400'">

                        {{-- sr-only = texto visível apenas para leitores de tela (acessibilidade) --}}
                        {{-- x-text = define o texto do elemento dinamicamente --}}
                        <span class="sr-only" x-text="presente ? 'Marcar falta' : 'Marcar presença'"></span>

                        {{-- O "bolinho" branco que desliza dentro do toggle --}}
                        {{-- :class muda a posição horizontal conforme o estado:
                             translate-x-8 = desloca 2rem para direita (posição "ligado"/presente)
                             translate-x-1 = desloca apenas 0.25rem (posição "desligado"/falta) --}}
                        <span class="inline-block h-5 w-5 rounded-full bg-white shadow transition-transform"
                              :class="presente ? 'translate-x-8' : 'translate-x-1'"></span>
                    </button>

                    {{-- Texto de status ao lado do toggle --}}
                    {{-- x-text = Alpine define o texto conforme o estado (sem recarregar) --}}
                    <span class="flex-shrink-0 w-14 text-xs font-semibold text-right"
                          :class="presente ? 'text-green-600' : 'text-gray-400'"
                          x-text="presente ? 'Presente' : 'Falta'"></span>
                </div>
            @endforeach
        </div>
    @endif
</div>
