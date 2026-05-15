<div>
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="mb-4 flex items-center gap-2 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between mb-5">
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            <div class="relative w-full sm:w-64">
                <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar nome ou RA..."
                       class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-senai">
            </div>
            <select wire:model.live="filtroTurma"
                    class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-senai">
                <option value="">Todas as turmas</option>
                @foreach($turmas as $t)
                    <option value="{{ $t->id }}">{{ $t->nome }}</option>
                @endforeach
            </select>
        </div>
        <button wire:click="openModal()" class="inline-flex items-center gap-2 bg-senai hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors whitespace-nowrap">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            Novo Aluno
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-x-auto">
        <table class="w-full text-sm min-w-[720px]">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">Nome</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">RA</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">Turma</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">Empresa</th>
                    <th class="text-center px-5 py-3 font-semibold text-gray-600">Status</th>
                    <th class="text-right px-5 py-3 font-semibold text-gray-600">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($alunos as $aluno)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-senai/10 flex items-center justify-center text-senai text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($aluno->nome, 0, 2)) }}
                                </div>
                                <span class="font-medium text-gray-800">{{ $aluno->nome }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3 text-gray-600 font-mono">{{ $aluno->ra }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $aluno->turma?->nome ?? '—' }}</td>
                        <td class="px-5 py-3">
                            @if($aluno->empresas->isEmpty())
                                <span class="text-gray-400">—</span>
                            @else
                                <div class="flex flex-wrap gap-1">
                                    @foreach($aluno->empresas as $empresa)
                                        <span class="inline-block px-2 py-0.5 bg-blue-50 text-blue-700 rounded text-xs font-medium">
                                            {{ $empresa->nome }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center">
                            <button wire:click="toggleAtivo({{ $aluno->id }})"
                                    wire:confirm="{{ $aluno->active ? 'Desativar este aluno?' : 'Reativar este aluno?' }}"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium transition-colors
                                    {{ $aluno->active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $aluno->active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                {{ $aluno->active ? 'Ativo' : 'Inativo' }}
                            </button>
                        </td>
                        <td class="px-5 py-3 text-right flex items-center justify-end gap-1">
                            <button wire:click="openModal({{ $aluno->id }})"
                                    class="text-gray-500 hover:text-senai transition-colors p-1 rounded" title="Editar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z" /></svg>
                            </button>
                            <button @click="$store.dialogo.perguntar('Excluir aluno', 'Os registros de chamada serão mantidos, mas o aluno será removido do sistema.', 'Excluir', () => $wire.excluir({{ $aluno->id }}))"
                                    class="text-gray-500 hover:text-red-500 transition-colors p-1 rounded" title="Excluir">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-gray-400">Nenhum aluno encontrado.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($alunos->hasPages())
            <div class="px-5 py-3 border-t border-gray-100">{{ $alunos->links() }}</div>
        @endif
    </div>

    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
             x-data x-on:keydown.escape.window="$wire.closeModal()">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md" @click.stop>
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                    <h3 class="font-semibold text-gray-800">{{ $editingId ? 'Editar Aluno' : 'Novo Aluno' }}</h3>
                    <button wire:click="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
                    </button>
                </div>

                <form wire:submit="save" class="px-6 py-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome completo</label>
                        <input wire:model="nome" type="text" placeholder="Nome do aluno"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-senai @error('nome') border-red-400 @enderror">
                        @error('nome') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">RA (Registro do Aluno)</label>
                        <input wire:model="ra" type="text" placeholder="Ex: 2026001"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono focus:outline-none focus:ring-2 focus:ring-senai @error('ra') border-red-400 @enderror">
                        @error('ra') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Turma</label>
                        <select wire:model="turma_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-senai @error('turma_id') border-red-400 @enderror">
                            <option value="">— Selecione —</option>
                            @foreach($turmas as $t)
                                <option value="{{ $t->id }}">{{ $t->nome }} ({{ $t->ano }})</option>
                            @endforeach
                        </select>
                        @error('turma_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" wire:click="closeModal()"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="flex-1 px-4 py-2 bg-senai hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors">
                            {{ $editingId ? 'Salvar' : 'Cadastrar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
