<div>
    {{-- Filtro de período --}}
    <div class="bg-white rounded-xl shadow-sm p-5 mb-6">
        <div class="flex flex-col sm:flex-row gap-3 items-end">
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
        </div>
    </div>

    {{-- Cards de resumo --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-senai">
            <p class="text-3xl font-bold text-gray-800">{{ $total }}</p>
            <p class="text-sm text-gray-500 mt-1">Aulas no período</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-green-500">
            <p class="text-3xl font-bold text-gray-800">{{ $presentes }}</p>
            <p class="text-sm text-gray-500 mt-1">Presenças</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm p-5 border-l-4 border-blue-500">
            <p class="text-3xl font-bold text-gray-800">{{ $pct }}%</p>
            <p class="text-sm text-gray-500 mt-1">Frequência</p>
        </div>
    </div>

    {{-- Barra de progresso --}}
    @if($total > 0)
        <div class="bg-white rounded-xl shadow-sm p-5 mb-6">
            <div class="flex items-center justify-between text-sm mb-2">
                <span class="font-medium text-gray-700">Frequência geral no período</span>
                <span class="font-bold {{ $pct >= 75 ? 'text-green-600' : 'text-red-600' }}">{{ $pct }}%</span>
            </div>
            <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all duration-500
                            {{ $pct >= 75 ? 'bg-green-400' : ($pct >= 50 ? 'bg-yellow-400' : 'bg-red-400') }}"
                     style="width: {{ $pct }}%"></div>
            </div>
            @if($pct < 75)
                <p class="text-xs text-red-500 mt-2">⚠ Frequência abaixo de 75% — risco de reprovação por falta.</p>
            @endif
        </div>
    @endif

    {{-- Lista de aulas --}}
    @if($aulas->isEmpty())
        <div class="bg-white rounded-xl shadow-sm p-10 text-center">
            <p class="text-gray-400">Nenhuma aula registrada no período selecionado.</p>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Data</th>
                        <th class="text-left px-5 py-3 font-semibold text-gray-600">Conteúdo</th>
                        <th class="text-center px-5 py-3 font-semibold text-gray-600">Presença</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($aulas as $aula)
                        @php $status = $aula->chamadas->first()?->status ?? 'falta'; @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3 text-gray-600 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($aula->data)->translatedFormat('d/m/Y') }}
                                <span class="block text-xs text-gray-400">
                                    {{ \Carbon\Carbon::parse($aula->data)->translatedFormat('l') }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-700 max-w-xs">
                                <p class="line-clamp-2">{{ $aula->descricao }}</p>
                            </td>
                            <td class="px-5 py-3 text-center">
                                @if($status === 'presente')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Presente
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        Falta
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
