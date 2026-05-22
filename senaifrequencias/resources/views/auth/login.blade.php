<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar — SENAI Frequências</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-hidden" style="font-family:'Inter',sans-serif; background:#0d0d0d;">

    {{-- ── Foto de fundo — tela cheia ─────────────────────────────────── --}}
    <div class="absolute inset-0">
        <img src="/images/login-bg.jpg"
             alt="Escola SENAI Luiz Varga"
             class="w-full h-full object-cover"
             style="filter:brightness(0.75) saturate(0.9);">

        {{-- Overlay: escurece mais à direita para contrastar com o card --}}
        <div class="absolute inset-0"
             style="background: linear-gradient(
                 105deg,
                 rgba(0,0,0,0.45) 0%,
                 rgba(0,0,0,0.30) 45%,
                 rgba(0,0,0,0.65) 100%
             );"></div>

        {{-- Gradiente de baixo para legibilidade da marca --}}
        <div class="absolute inset-0"
             style="background: linear-gradient(to top, rgba(0,0,0,0.80) 0%, transparent 50%);"></div>
    </div>

    {{-- ── Layout ──────────────────────────────────────────────────────── --}}
    <div class="relative z-10 flex flex-col h-screen items-center justify-center px-6 py-10">

        {{-- Marca no canto inferior esquerdo --}}
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

        {{-- Card centralizado --}}
        <div id="login-card" class="w-full max-w-[420px]">

            <div class="w-full animate-fade-up"
                 style="background:rgba(255,255,255,0.96);
                        backdrop-filter:blur(20px);
                        -webkit-backdrop-filter:blur(20px);
                        border-radius:20px;
                        box-shadow:0 25px 60px rgba(0,0,0,0.35), 0 0 0 1px rgba(255,255,255,0.15);
                        overflow:hidden;">

                {{-- Barra SENAI --}}
                <div class="h-1 w-full bg-senai"></div>

                <div class="px-8 py-8">

                    {{-- Logo mobile --}}
                    <div class="lg:hidden text-center mb-8">
                        <span class="text-senai font-black text-3xl tracking-[0.2em]">SENAI</span>
                        <p class="text-gray-400 text-xs mt-0.5">Escola Luiz Varga</p>
                    </div>

                    {{-- Cabeçalho --}}
                    <div class="mb-7">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="h-0.5 w-5 bg-senai rounded"></div>
                            <span class="text-senai text-[10px] font-bold tracking-[0.2em] uppercase">Frequências</span>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900">Acesse sua conta</h1>
                        <p class="text-gray-400 text-sm mt-1">Entre com suas credenciais institucionais.</p>
                    </div>

                    {{-- Alertas --}}
                    @if(session('status'))
                        <div class="mb-5 flex items-center gap-2 px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                            {{ session('status') }}
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="mb-5 flex items-center gap-2 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" /></svg>
                            {{ $errors->first() }}
                        </div>
                    @endif

                    {{-- Formulário --}}
                    <form id="login-form" method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label for="email" class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.18em] mb-1.5">
                                E-mail
                            </label>
                            <input id="email" type="email" name="email"
                                   value="{{ old('email') }}"
                                   required autofocus autocomplete="username"
                                   placeholder="seu@email.com"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl text-gray-800 text-sm
                                          bg-gray-50/80 focus:bg-white focus:outline-none focus:ring-2
                                          focus:ring-senai/20 focus:border-senai placeholder-gray-300
                                          transition-all duration-200
                                          @error('email') border-red-300 bg-red-50 @enderror">
                        </div>

                        <div>
                            <label for="password" class="block text-[10px] font-bold text-gray-400 uppercase tracking-[0.18em] mb-1.5">
                                Senha
                            </label>
                            <input id="password" type="password" name="password"
                                   required autocomplete="current-password"
                                   placeholder="••••••••"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl text-gray-800 text-sm
                                          bg-gray-50/80 focus:bg-white focus:outline-none focus:ring-2
                                          focus:ring-senai/20 focus:border-senai placeholder-gray-300
                                          transition-all duration-200
                                          @error('password') border-red-300 bg-red-50 @enderror">
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2 text-sm text-gray-500 cursor-pointer select-none">
                                <input type="checkbox" name="remember"
                                       class="w-4 h-4 rounded border-gray-300 text-senai focus:ring-senai/30">
                                Lembrar-me
                            </label>
                            @if(Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                   class="text-xs text-gray-400 hover:text-senai transition-colors font-medium">
                                    Esqueceu a senha?
                                </a>
                            @endif
                        </div>

                        <button type="submit"
                                class="w-full bg-senai hover:bg-red-700 active:scale-[0.98] text-white font-bold
                                       py-3.5 rounded-xl transition-all duration-200 text-sm tracking-wider
                                       uppercase shadow-md hover:shadow-lg hover:shadow-senai/25 mt-1">
                            Entrar
                        </button>
                    </form>

                    {{-- Rodapé do card --}}
                    <div class="mt-7 pt-6 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-senai font-black text-xs tracking-[0.2em]">SENAI</span>
                        <p class="text-gray-300 text-[10px]">© {{ date('Y') }} Escola Luiz Varga</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- Overlay branco que cobre a tela durante a transição de saída --}}
    {{-- Começa totalmente transparente (opacity 0) e anima até branco sólido --}}
    <div id="login-overlay"
         style="position:fixed; inset:0; background:#F0F2F5; opacity:0; pointer-events:none;
                transition:opacity 0.55s cubic-bezier(0.4,0,0.2,1); z-index:999;">
    </div>

    <script>
        (function () {
            const form    = document.getElementById('login-form');
            const card    = document.getElementById('login-card');
            const overlay = document.getElementById('login-overlay');

            // Aplica a animação de saída: card sobe até sair da tela, fundo vira branco
            function animarSaida(callback) {
                // Faz o overlay branco aparecer gradualmente (fundo vai ficando branco)
                overlay.style.opacity = '1';

                // Desloca o card para cima (fora da viewport) enquanto some
                card.style.transition = 'transform 0.5s cubic-bezier(0.4,0,0.2,1), opacity 0.4s ease';
                card.style.transform  = 'translateY(-110vh)';
                card.style.opacity    = '0';

                // Após 650ms (tempo da animação), executa o callback (navegar para o dashboard)
                setTimeout(callback, 650);
            }

            form.addEventListener('submit', async function (e) {
                e.preventDefault(); // impede o submit padrão enquanto processamos

                const formData = new FormData(form);

                let urlFinal = null;

                try {
                    // Envia as credenciais via AJAX (fetch segue o redirect automaticamente)
                    const response = await fetch(form.action, {
                        method:   'POST',
                        body:     formData,
                        redirect: 'follow', // segue o redirect do Laravel após login
                    });
                    urlFinal = response.url; // URL final após todos os redirects
                } catch (_) {
                    // Erro de rede — submete o formulário normalmente como fallback
                    form.submit();
                    return;
                }

                // Detecta se o login foi bem-sucedido:
                // Um login com ERRO redireciona DE VOLTA para /login
                // Um login com SUCESSO redireciona para /admin/dashboard, /professor/dashboard, etc.
                const loginFalhou = !urlFinal || urlFinal.includes('/login');

                if (loginFalhou) {
                    // Credenciais erradas — navega para a URL (que é o /login com erros na session)
                    // Assim o Laravel exibe a mensagem de erro normalmente
                    window.location.href = urlFinal || form.action;
                } else {
                    // Login bem-sucedido! Dispara a animação e depois navega para o dashboard
                    animarSaida(function () {
                        window.location.href = urlFinal;
                    });
                }
            });
        })();
    </script>

</body>
</html>
