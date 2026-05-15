<x-senai-layout title="Aulas — {{ $turma->nome }}">

    <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="{{ route('admin.turmas.index') }}" class="hover:text-senai transition-colors">Turmas</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
        <span class="text-gray-700 font-medium">{{ $turma->nome }}</span>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-5 mb-6 border-l-4 border-senai flex items-center justify-between gap-4">
        <div>
            <p class="font-bold text-gray-800 text-lg">{{ $turma->nome }}</p>
            <p class="text-sm text-gray-500">
                {{ $turma->curso }} &bull; {{ $turma->ano }}
                @if($turma->professor)
                    &bull; Prof. {{ $turma->professor->name }}
                @endif
            </p>
        </div>
        <a href="{{ route('admin.turmas.exportar', $turma) }}"
           class="inline-flex items-center gap-2 border border-gray-300 hover:border-senai hover:text-senai text-gray-600 text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
            </svg>
            Exportar CSV
        </a>
    </div>

    @livewire('admin.aulas-turma', ['turma' => $turma])

</x-senai-layout>
