<div>
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             class="mb-4 flex items-center gap-2 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between mb-5">
        <div class="relative w-full sm:w-72">
            <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar aluno..."
                   class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-senai">
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm text-gray-500">{{ count($selecionados) }} selecionado(s)</span>
            <button wire:click="salvar"
                    class="inline-flex items-center gap-2 bg-senai hover:bg-red-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                Salvar vínculos
            </button>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-3 bg-blue-50 border-b border-blue-100 text-sm text-blue-700">
            Marque os alunos que trabalham em <strong>{{ $empresa->nome }}</strong>. Clique em "Salvar vínculos" para confirmar.
        </div>

        <div class="divide-y divide-gray-100">
            @forelse($alunos as $aluno)
                @php $checked = in_array((string)$aluno->id, $this->selecionados); @endphp
                <label class="flex items-center gap-4 px-5 py-3 hover:bg-gray-50 cursor-pointer transition-colors
                              {{ $checked ? 'bg-red-50' : '' }}">
                    <input type="checkbox"
                           wire:model.live="selecionados"
                           value="{{ $aluno->id }}"
                           class="w-4 h-4 rounded border-gray-300 text-senai focus:ring-senai">
                    <div class="w-8 h-8 rounded-full bg-senai/10 flex items-center justify-center text-senai text-xs font-bold flex-shrink-0">
                        {{ strtoupper(substr($aluno->nome, 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-800 text-sm">{{ $aluno->nome }}</p>
                        <p class="text-xs text-gray-500">RA: {{ $aluno->ra }} &bull; {{ $aluno->turma?->nome ?? '—' }}</p>
                    </div>
                    @if($checked)
                        <span class="text-xs text-senai font-medium">Vinculado</span>
                    @endif
                </label>
            @empty
                <div class="px-5 py-10 text-center text-gray-400">Nenhum aluno ativo encontrado.</div>
            @endforelse
        </div>
    </div>
</div>
