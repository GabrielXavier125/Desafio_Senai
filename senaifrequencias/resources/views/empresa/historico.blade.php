<x-senai-layout title="Histórico de Frequência">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="{{ route('empresa.dashboard') }}" class="hover:text-senai transition-colors">Frequências</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
        <span class="text-gray-700 font-medium">{{ $aluno->nome }}</span>
    </div>

    {{-- Cabeçalho do aluno --}}
    <div class="bg-white rounded-xl shadow-sm p-5 mb-6 flex items-center gap-4 border-l-4 border-senai">
        <div class="w-12 h-12 rounded-full bg-senai/10 flex items-center justify-center text-senai font-bold text-lg flex-shrink-0">
            {{ strtoupper(mb_substr($aluno->nome, 0, 2)) }}
        </div>
        <div>
            <p class="font-bold text-gray-800 text-lg">{{ $aluno->nome }}</p>
            <p class="text-sm text-gray-500">RA: {{ $aluno->ra }} &bull; Turma: {{ $aluno->turma?->nome ?? '—' }}</p>
        </div>
    </div>

    @livewire('empresa.historico-aluno', ['aluno' => $aluno])

</x-senai-layout>
