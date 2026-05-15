<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SENAI Frequências' }} — SENAI</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased" style="background-color:#F0F2F5;">

<div class="flex h-screen overflow-hidden"
     x-data="{ sidebarOpen: false }"
     @keydown.escape.window="sidebarOpen = false">

    {{-- ── Overlay mobile ─────────────────────────────────────────────── --}}
    <div x-show="sidebarOpen"
         x-transition:enter="transition-opacity duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/50 z-40 lg:hidden"
         style="display:none"></div>

    {{-- ── Sidebar ─────────────────────────────────────────────────────── --}}
    <aside class="fixed inset-y-0 left-0 z-50 w-64 flex-shrink-0 flex flex-col
                  transition-transform duration-300
                  lg:relative lg:z-auto lg:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           style="background-color:#141414;">

        {{-- Botão fechar (mobile) --}}
        <button @click="sidebarOpen = false"
                class="absolute top-4 right-4 lg:hidden text-gray-500 hover:text-white transition-colors p-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>

        {{-- Marca --}}
        <div class="px-5 pt-6 pb-5" style="border-bottom:1px solid rgba(255,255,255,0.07);">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-senai rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                    <span class="text-white font-black text-base tracking-wider">S</span>
                </div>
                <div class="min-w-0">
                    <p class="text-white font-extrabold text-base tracking-[0.18em] leading-none">SENAI</p>
                    <p class="text-gray-500 text-[10px] mt-0.5 truncate">Escola Luiz Varga</p>
                </div>
            </div>
            <div class="mt-4 px-3 py-1.5 rounded-md text-center" style="background:rgba(255,255,255,0.04);">
                <p class="text-[10px] text-gray-500 uppercase tracking-[0.15em]">Sistema de Frequência</p>
            </div>
        </div>

        {{-- Seção de navegação --}}
        <div class="px-4 pt-5 pb-1.5">
            <p class="text-[9px] font-bold text-gray-600 uppercase tracking-[0.2em] px-2">
                @if(auth()->user()->isAdmin())   Administração
                @elseif(auth()->user()->isProfessor()) Professor
                @else Empresa Parceira
                @endif
            </p>
        </div>

        <nav class="flex-1 px-3 space-y-0.5 overflow-y-auto pb-4">
            @if(auth()->user()->isAdmin())
                <x-sidebar-link href="{{ route('admin.dashboard') }}" :active="request()->routeIs('admin.dashboard')">
                    <x-icon name="squares-2x2" /> Dashboard
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('admin.turmas.index') }}" :active="request()->routeIs('admin.turmas.*')">
                    <x-icon name="academic-cap" /> Turmas
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('admin.alunos.index') }}" :active="request()->routeIs('admin.alunos.*')">
                    <x-icon name="user-group" /> Alunos
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('admin.professores.index') }}" :active="request()->routeIs('admin.professores.*')">
                    <x-icon name="user" /> Professores
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('admin.empresas.index') }}" :active="request()->routeIs('admin.empresas.*')">
                    <x-icon name="building-office" /> Empresas
                </x-sidebar-link>
            @elseif(auth()->user()->isProfessor())
                <x-sidebar-link href="{{ route('professor.dashboard') }}" :active="request()->routeIs('professor.dashboard')">
                    <x-icon name="squares-2x2" /> Minha Turma
                </x-sidebar-link>
            @elseif(auth()->user()->isEmpresa())
                <x-sidebar-link href="{{ route('empresa.dashboard') }}" :active="request()->routeIs('empresa.dashboard')">
                    <x-icon name="squares-2x2" /> Frequências
                </x-sidebar-link>
                <x-sidebar-link href="{{ route('empresa.aulas') }}" :active="request()->routeIs('empresa.aulas')">
                    <x-icon name="academic-cap" /> Aulas
                </x-sidebar-link>
            @endif
        </nav>

        {{-- Usuário --}}
        <div class="p-4" style="border-top:1px solid rgba(255,255,255,0.07);">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-8 h-8 rounded-full bg-senai flex items-center justify-center flex-shrink-0">
                    <span class="text-white text-xs font-bold">
                        {{ strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                    </span>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-white text-xs font-semibold truncate leading-tight">{{ auth()->user()->name }}</p>
                    <p class="text-gray-500 text-[10px] truncate mt-0.5">{{ auth()->user()->email }}</p>
                </div>
            </div>
            <div class="flex items-center justify-between">
                <a href="{{ route('perfil.senha') }}"
                   class="flex items-center gap-1.5 text-[11px] text-gray-500 hover:text-gray-300 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 0 1 21.75 8.25Z" />
                    </svg>
                    Alterar senha
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="flex items-center gap-1.5 text-[11px] text-gray-500 hover:text-red-400 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15m-3 0-3-3m0 0 3-3m-3 3H15" />
                        </svg>
                        Sair
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ── Área principal ───────────────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col overflow-hidden min-w-0">

        {{-- Topbar --}}
        <header class="bg-white flex-shrink-0 flex items-center justify-between px-4 lg:px-6"
                style="height:56px; border-bottom:2px solid #E30613; box-shadow:0 1px 4px rgba(0,0,0,0.06);">
            <div class="flex items-center gap-2 lg:gap-3">
                {{-- Hamburger (mobile) --}}
                <button @click="sidebarOpen = true"
                        class="lg:hidden p-2 -ml-1 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors flex-shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                <div class="flex items-center gap-1.5 text-gray-300">
                    <span class="text-senai font-extrabold text-sm tracking-[0.15em]">SENAI</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                </div>
                <h1 class="text-sm font-semibold text-gray-800 truncate">{{ $title ?? 'Dashboard' }}</h1>
            </div>
            <div class="flex items-center gap-3 flex-shrink-0">
                <span class="text-[10px] font-semibold uppercase tracking-widest px-2.5 py-1 rounded-full
                    @if(auth()->user()->isAdmin()) bg-red-50 text-senai
                    @elseif(auth()->user()->isProfessor()) bg-blue-50 text-blue-600
                    @else bg-green-50 text-green-600 @endif">
                    @if(auth()->user()->isAdmin()) <span class="hidden sm:inline">Administrador</span><span class="sm:hidden">Admin</span>
                    @elseif(auth()->user()->isProfessor()) Professor
                    @else Empresa @endif
                </span>
            </div>
        </header>

        {{-- Conteúdo --}}
        <main class="flex-1 overflow-y-auto p-4 lg:p-6 animate-fade-in">
            {{ $slot }}
        </main>
    </div>
</div>

{{-- ── Diálogo de confirmação global ──────────────────────────────── --}}
<div x-data
     x-show="$store.dialogo.aberto"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[200] flex items-center justify-center bg-black/60 px-4"
     style="display:none"
     @click="$store.dialogo.cancelar()"
     @keydown.escape.window="$store.dialogo.cancelar()">

    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden"
         x-show="$store.dialogo.aberto"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.stop>

        <div class="h-1 w-full bg-senai"></div>

        <div class="px-6 pt-5 pb-6">
            <div class="flex items-start gap-4">
                <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-senai" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                </div>
                <div class="flex-1 min-w-0 pt-0.5">
                    <h3 class="font-semibold text-gray-900 text-sm" x-text="$store.dialogo.titulo"></h3>
                    <p class="text-sm text-gray-500 mt-1.5 leading-relaxed" x-text="$store.dialogo.mensagem"></p>
                </div>
            </div>

            <div class="flex gap-3 mt-5">
                <button @click="$store.dialogo.cancelar()"
                        class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm text-gray-600 font-medium hover:bg-gray-50 transition-colors">
                    Cancelar
                </button>
                <button @click="$store.dialogo.confirmar()"
                        class="flex-1 px-4 py-2.5 bg-senai hover:bg-red-700 text-white rounded-xl text-sm font-semibold transition-colors shadow-sm">
                    <span x-text="$store.dialogo.textoBotao"></span>
                </button>
            </div>
        </div>
    </div>
</div>

@livewireScripts
</body>
</html>
