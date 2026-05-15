<x-senai-layout title="Chamada">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="{{ route('professor.dashboard') }}" class="hover:text-senai transition-colors">Minha Turma</a>
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
        </svg>
        <span class="text-gray-700 font-medium">Chamada</span>
    </div>

    @livewire('professor.chamada', ['aula' => $aula])

</x-senai-layout>
