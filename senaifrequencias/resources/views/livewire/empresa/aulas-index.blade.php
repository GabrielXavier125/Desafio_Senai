<div>
    {{-- Filtros --}}
    <div class="flex flex-col sm:flex-row gap-3 items-end mb-6">
        @if($turmas->count() > 1)
            <div class="w-full sm:w-56">
                <label class="block text-xs font-medium text-gray-500 mb-1">Turma</label>
                <select wire:model.live="filtroTurma"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-senai">
                    <option value="">Todas as turmas</option>
                    @foreach($turmas as $t)
                        <option value="{{ $t->id }}">{{ $t->nome }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="flex-1">
            <label class="block text-xs font-medium text-gray-500 mb-1">De</label>
            <input wire:model.live="de" type="date"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-senai">
        </div>
        <div class="flex-1">
            <label class="block text-xs font-medium text-gray-500 mb-1">Até</label>
            <input wire:model.live="ate" type="date"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-senai">
        </div>
        @if($filtroTurma || $de || $ate)
            <button wire:click="$set('filtroTurma', ''); $set('de', ''); $set('ate', '')"
                    class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors whitespace-nowrap">
                Limpar filtros
            </button>
        @endif
    </div>

    {{-- Lista --}}
    @if($aulas->isEmpty())
        <div class="bg-white rounded-xl shadow-sm p-10 text-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
            </svg>
            <p class="text-gray-400">Nenhuma aula encontrada para os filtros selecionados.</p>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Data</th>
                        @if($turmas->count() > 1)
                            <th class="text-left px-5 py-3 font-semibold text-gray-600">Turma</th>
                        @endif
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Conteúdo ministrado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($aulas as $aula)
                        <tr class="hover:bg-gray-50 transition-colors align-top">
                            <td class="px-5 py-4 whitespace-nowrap">
                                <p class="font-medium text-gray-800">
                                    {{ \Carbon\Carbon::parse($aula->data)->translatedFormat('d/m/Y') }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ \Carbon\Carbon::parse($aula->data)->translatedFormat('l') }}
                                </p>
                            </td>
                            @if($turmas->count() > 1)
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="inline-block px-2 py-0.5 bg-blue-50 text-blue-700 rounded text-xs font-medium">
                                        {{ $aula->turma->nome }}
                                    </span>
                                </td>
                            @endif
                            <td class="px-5 py-4 text-gray-700 max-w-lg">
                                {{ $aula->descricao }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($aulas->hasPages())
                <div class="px-5 py-3 border-t border-gray-100">{{ $aulas->links() }}</div>
            @endif
        </div>

        <p class="text-xs text-gray-400 mt-3 text-right">
            {{ $aulas->total() }} aula{{ $aulas->total() !== 1 ? 's' : '' }} encontrada{{ $aulas->total() !== 1 ? 's' : '' }}
        </p>
    @endif
</div>
