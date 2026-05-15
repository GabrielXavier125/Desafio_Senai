<x-senai-layout title="Vincular Alunos — {{ $empresa->nome }}">

    <div class="mb-5 flex items-center gap-3">
        <a href="{{ route('admin.empresas.index') }}"
           class="text-gray-500 hover:text-gray-700 transition-colors flex items-center gap-1 text-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
            Empresas
        </a>
        <span class="text-gray-300">/</span>
        <span class="text-sm text-gray-700 font-medium">{{ $empresa->nome }}</span>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-5 mb-5 flex items-center gap-4 border-l-4 border-senai">
        <div class="text-senai">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" /></svg>
        </div>
        <div>
            <p class="font-bold text-gray-800 text-lg">{{ $empresa->nome }}</p>
            <p class="text-sm text-gray-500">CNPJ: {{ $empresa->cnpj }} &bull; Acesso: {{ $empresa->user?->email }}</p>
        </div>
    </div>

    @livewire('admin.vincular-alunos-empresa', ['empresa' => $empresa])

</x-senai-layout>
