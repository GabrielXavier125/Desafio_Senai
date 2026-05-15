<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha — SENAI Frequências</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-hidden" style="font-family:'Inter',sans-serif; background:#0d0d0d;">

    {{-- Foto de fundo --}}
    <div class="absolute inset-0">
        <img src="/images/login-bg.jpg"
             alt="Escola SENAI Luiz Varga"
             class="w-full h-full object-cover"
             style="filter:brightness(0.75) saturate(0.9);">
        <div class="absolute inset-0"
             style="background: linear-gradient(105deg, rgba(0,0,0,0.45) 0%, rgba(0,0,0,0.30) 45%, rgba(0,0,0,0.65) 100%);"></div>
        <div class="absolute inset-0"
             style="background: linear-gradient(to top, rgba(0,0,0,0.80) 0%, transparent 50%);"></div>
    </div>

    <div class="relative z-10 flex flex-col h-screen items-center justify-center px-6 py-10">

        {{-- Marca desktop --}}
        <div class="hidden lg:block absolute bottom-12 left-12 animate-fade-up">
            <div class="flex items-end gap-4 mb-4">
                <span class="text-white font-black leading-none"
                      style="font-size:4rem; letter-spacing:0.15em; text-shadow:0 4px 30px rgba(0,0,0,0.5);">
                    SENAI
                </span>
                <div class="mb-1.5 pl-4 border-l-2 border-white/25">
                    <p class="text-white/90 text-sm font-semibold">Escola Luiz Varga</p>
                    <p class="text-white/45 text-xs mt-0.5">Serviço Nacional de Aprendizagem Industrial</p>
                </div>
            </div>
            <div class="h-px w-72"
                 style="background:linear-gradient(to right, #E30613, rgba(255,255,255,0.15), transparent);"></div>
            <p class="text-white/35 text-[10px] tracking-[0.25em] uppercase mt-3">
                Sistema de Controle de Frequência
            </p>
        </div>

        {{-- Card --}}
        <div class="w-full max-w-[420px]">
            <div class="w-full animate-fade-up"
                 style="background:rgba(255,255,255,0.96);
                        backdrop-filter:blur(20px);
                        -webkit-backdrop-filter:blur(20px);
                        border-radius:20px;
                        box-shadow:0 25px 60px rgba(0,0,0,0.35), 0 0 0 1px rgba(255,255,255,0.15);
                        overflow:hidden;">

                <div class="h-1 w-full bg-senai"></div>

                <div class="px-8 py-8">

                    {{-- Logo mobile --}}
                    <div class="lg:hidden text-center mb-8">
                        <span class="text-senai font-black text-3xl tracking-[0.2em]">SENAI</span>
                        <p class="text-gray-400 text-xs mt-0.5">Escola Luiz Varga</p>
                    </div>

                    {{-- Cabeçalho --}}
                    <div class="mb-6">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="h-0.5 w-5 bg-senai rounded"></div>
                            <span class="text-senai text-[10px] font-bold tracking-[0.2em] uppercase">Frequências</span>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900">Recuperar senha</h1>
                        <p class="text-gray-400 text-sm mt-1 leading-relaxed">
                            Digite seu e-mail institucional e enviaremos um link para criar uma nova senha.
                        </p>
                    </div>

                    {{-- Status (link enviado) --}}
                    @if(session('status'))
                        <div class="mb-5 flex items-start gap-3 px-4 py-3.5 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    {{-- Erros --}}
                    @if($errors->any())
                        <div class="mb-5 flex items-center gap-2 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                            </svg>
                            {{ $errors->first() }}
                        </div>
                    @endif

                    {{-- Formulário --}}
                    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label for="email" class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.18em] mb-1.5">
                                E-mail
                            </label>
                            <input id="email" type="email" name="email"
                                   value="{{ old('email') }}"
                                   required autofocus autocomplete="email"
                                   placeholder="seu@email.com"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl text-gray-800 text-sm
                                          bg-gray-50/80 focus:bg-white focus:outline-none focus:ring-2
                                          focus:ring-senai/20 focus:border-senai placeholder-gray-300
                                          transition-all duration-200
                                          @error('email') border-red-300 bg-red-50 @enderror">
                        </div>

                        <button type="submit"
                                class="w-full bg-senai hover:bg-red-700 active:scale-[0.98] text-white font-bold
                                       py-3.5 rounded-xl transition-all duration-200 text-sm tracking-wider
                                       uppercase shadow-md hover:shadow-lg hover:shadow-senai/25">
                            Enviar link de recuperação
                        </button>
                    </form>

                    {{-- Voltar --}}
                    <div class="mt-6 text-center">
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-senai transition-colors font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                            </svg>
                            Voltar ao login
                        </a>
                    </div>

                    {{-- Rodapé --}}
                    <div class="mt-7 pt-6 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-senai font-black text-xs tracking-[0.2em]">SENAI</span>
                        <p class="text-gray-300 text-[10px]">© {{ date('Y') }} Escola Luiz Varga</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
