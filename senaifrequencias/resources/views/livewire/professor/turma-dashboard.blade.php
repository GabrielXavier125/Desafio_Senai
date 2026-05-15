<div>
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="mb-4 flex items-center gap-2 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
            {{ session('success') }}
        </div>
    @endif

    @if(!$turma)
        <div class="bg-white rounded-xl shadow-sm p-10 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 3.741-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
            </svg>
            <p class="text-gray-500 font-medium">Nenhuma turma atribuída.</p>
            <p class="text-gray-400 text-sm mt-1">Solicite ao administrador que vincule você a uma turma.</p>
        </div>
    @else
        {{-- Cards de resumo --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-senai">
                <p class="text-3xl font-bold text-gray-800">{{ $turma->alunos->count() }}</p>
                <p class="text-sm text-gray-500 mt-1">Alunos ativos</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-blue-500">
                <p class="text-3xl font-bold text-gray-800">{{ $aulas->count() }}</p>
                <p class="text-sm text-gray-500 mt-1">Aulas registradas</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-green-500">
                @php
                    $totalPresencas = $aulas->sum('presentes');
                    $totalPossivel  = $aulas->sum(fn($a) => $a->presentes + $a->faltas);
                    $pct = $totalPossivel > 0 ? round($totalPresencas / $totalPossivel * 100) : 0;
                @endphp
                <p class="text-3xl font-bold text-gray-800">{{ $pct }}%</p>
                <p class="text-sm text-gray-500 mt-1">Presença geral</p>
            </div>
        </div>

        {{-- Cabeçalho da lista --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Histórico de Aulas</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('professor.exportar') }}"
                   class="inline-flex items-center gap-2 border border-gray-300 hover:border-senai hover:text-senai text-gray-600 text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    <span class="hidden sm:inline">Exportar CSV</span>
                    <span class="sm:hidden">CSV</span>
                </a>
                <button wire:click="openModal"
                        class="inline-flex items-center gap-2 bg-senai hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Nova Aula
                </button>
            </div>
        </div>

        {{-- Lista de aulas --}}
        @if($aulas->isEmpty())
            <div class="bg-white rounded-xl shadow-sm p-10 text-center">
                <p class="text-gray-400">Nenhuma aula registrada ainda. Clique em <strong>Nova Aula</strong> para começar.</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($aulas as $aula)
                    @php
                        $total   = $aula->presentes + $aula->faltas;
                        $pctAula = $total > 0 ? round($aula->presentes / $total * 100) : 0;
                    @endphp
                    <div class="bg-white rounded-xl shadow-sm p-5 hover:shadow-md border border-transparent hover:border-gray-200 transition-all group">
                        <div class="flex items-start justify-between gap-4">
                            {{-- Info principal (clicável) --}}
                            <a href="{{ route('professor.aulas.chamada', $aula) }}" class="flex-1 min-w-0 block">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wide">
                                        {{ \Carbon\Carbon::parse($aula->data)->translatedFormat('d \d\e F \d\e Y') }}
                                    </span>
                                </div>
                                <p class="text-gray-700 text-sm leading-snug line-clamp-2">{{ $aula->descricao }}</p>
                                {{-- Estatísticas em mobile (inline) --}}
                                <div class="flex gap-3 mt-1.5 sm:hidden text-xs">
                                    <span class="inline-flex items-center gap-1 text-green-600 font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        {{ $aula->presentes }}P
                                    </span>
                                    <span class="inline-flex items-center gap-1 text-red-500 font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                                        {{ $aula->faltas }}F
                                    </span>
                                    <span class="font-bold text-gray-700">{{ $pctAula }}%</span>
                                </div>
                            </a>

                            {{-- Estatísticas em desktop --}}
                            <div class="hidden sm:block flex-shrink-0 text-right">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-bold text-gray-800">{{ $pctAula }}%</span>
                                </div>
                                <div class="flex gap-2 text-xs">
                                    <span class="inline-flex items-center gap-1 text-green-600 font-medium">
                                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                        {{ $aula->presentes }} presente{{ $aula->presentes !== 1 ? 's' : '' }}
                                    </span>
                                    <span class="inline-flex items-center gap-1 text-red-500 font-medium">
                                        <span class="w-2 h-2 rounded-full bg-red-400"></span>
                                        {{ $aula->faltas }} falta{{ $aula->faltas !== 1 ? 's' : '' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Ações --}}
                            <div class="flex-shrink-0 flex items-center gap-1 self-center">
                                <button wire:click="openEditModal({{ $aula->id }})"
                                        class="p-1.5 text-gray-400 hover:text-senai hover:bg-red-50 rounded-lg transition-colors" title="Editar aula">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" /></svg>
                                </button>
                                <button @click="$store.dialogo.perguntar('Excluir aula', 'Todas as chamadas registradas nesta aula também serão removidas.', 'Excluir', () => $wire.excluirAula({{ $aula->id }}))"
                                        class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Excluir aula">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                                </button>
                                <a href="{{ route('professor.aulas.chamada', $aula) }}"
                                   class="p-1.5 text-gray-300 group-hover:text-senai transition-colors" title="Abrir chamada">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
                                </a>
                            </div>
                        </div>

                        {{-- Mini progress bar --}}
                        <div class="mt-3 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-green-400 rounded-full transition-all"
                                 style="width: {{ $pctAula }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif

    {{-- Modal nova aula --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
             x-data x-on:keydown.escape.window="$wire.closeModal()">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md" @click.stop>
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Nova Aula</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form wire:submit="criarAula" class="px-6 py-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data da aula</label>
                        <input wire:model="data" type="date"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-senai @error('data') border-red-400 @enderror">
                        @error('data') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Conteúdo ministrado</label>
                        <textarea wire:model="descricao" rows="4"
                                  placeholder="Descreva o conteúdo abordado nesta aula..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-senai resize-none @error('descricao') border-red-400 @enderror"></textarea>
                        @error('descricao') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex gap-3 pt-1">
                        <button type="button" wire:click="closeModal"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition-colors">Cancelar</button>
                        <button type="submit" wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-wait"
                                class="flex-1 px-4 py-2 bg-senai hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors">
                            <span wire:loading.remove>Iniciar chamada →</span>
                            <span wire:loading class="flex items-center justify-center gap-2">
                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                Criando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal editar aula --}}
    @if($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
             x-data x-on:keydown.escape.window="$wire.closeEditModal()">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md" @click.stop>
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">Editar Aula</h3>
                    <button wire:click="closeEditModal" class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form wire:submit="salvarEdicao" class="px-6 py-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data da aula</label>
                        <input wire:model="editData" type="date"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-senai @error('editData') border-red-400 @enderror">
                        @error('editData') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Conteúdo ministrado</label>
                        <textarea wire:model="editDescricao" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-senai resize-none @error('editDescricao') border-red-400 @enderror"></textarea>
                        @error('editDescricao') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex gap-3 pt-1">
                        <button type="button" wire:click="closeEditModal"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition-colors">Cancelar</button>
                        <button type="submit"
                                class="flex-1 px-4 py-2 bg-senai hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors">
                            Salvar alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
