<div>
    {{-- Filtro de período --}}
    <div class="flex flex-col sm:flex-row gap-3 items-end mb-6">
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
        @if($de || $ate)
            <button wire:click="$set('de', ''); $set('ate', '')"
                    class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Limpar
            </button>
        @endif
    </div>

    @if($aulas->isEmpty())
        <div class="bg-white rounded-xl shadow-sm p-10 text-center">
            <p class="text-gray-400">Nenhuma aula registrada{{ ($de || $ate) ? ' no período selecionado' : '' }}.</p>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm overflow-x-auto">
            <table class="w-full text-sm min-w-[600px]">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Data</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Conteúdo</th>
                        <th class="text-center px-5 py-3 font-semibold text-gray-600">Presentes</th>
                        <th class="text-center px-5 py-3 font-semibold text-gray-600">Faltas</th>
                        <th class="text-center px-5 py-3 font-semibold text-gray-600">Frequência</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($aulas as $aula)
                        @php
                            $total  = $aula->presentes + $aula->faltas;
                            $pct    = $total > 0 ? round($aula->presentes / $total * 100) : 0;
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors align-top">
                            <td class="px-5 py-4 whitespace-nowrap">
                                <p class="font-medium text-gray-800">
                                    {{ \Carbon\Carbon::parse($aula->data)->translatedFormat('d/m/Y') }}
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    {{ \Carbon\Carbon::parse($aula->data)->translatedFormat('l') }}
                                </p>
                            </td>
                            <td class="px-5 py-4 text-gray-700 max-w-sm">
                                <p class="line-clamp-2">{{ $aula->descricao }}</p>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center gap-1 text-green-700 font-semibold">
                                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                    {{ $aula->presentes }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center gap-1 text-red-600 font-semibold">
                                    <span class="w-2 h-2 rounded-full bg-red-400"></span>
                                    {{ $aula->faltas }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="text-sm font-bold {{ $pct >= 75 ? 'text-green-600' : 'text-red-500' }}">
                                    {{ $pct }}%
                                </span>
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
