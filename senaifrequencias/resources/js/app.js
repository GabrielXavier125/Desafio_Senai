// ─── Importações ───────────────────────────────────────────────────────────
// Importa o arquivo bootstrap.js que configura o Axios (biblioteca HTTP)
// O Axios é usado pelo Livewire para fazer as requisições AJAX ao servidor
import './bootstrap';

// ─── IMPORTANTE: Por que não importamos Alpine aqui? ───────────────────────
// O Livewire 4 já inclui e gerencia o Alpine.js internamente.
// Se importássemos "import Alpine from 'alpinejs'" aqui, criaríamos DOIS instances do Alpine:
// - Um do npm (este arquivo) que inicia como ES module deferido
// - Um do Livewire que já está rodando
// Isso quebraria o $wire (comunicação Alpine ↔ Livewire) e nada funcionaria.
// Solução: deixar o Livewire gerenciar o Alpine e registrar nosso código via 'alpine:init'.

// ─── Inicialização do Alpine.js ────────────────────────────────────────────
// 'alpine:init' é um evento disparado pelo Livewire quando o Alpine está pronto para receber configurações
// É o momento certo para registrar stores globais, diretivas customizadas, etc.
document.addEventListener('alpine:init', () => {

    // Alpine.store('nome', objeto) cria uma variável global compartilhada por toda a aplicação
    // Qualquer componente Alpine na página pode acessar com: $store.dialogo
    Alpine.store('dialogo', {

        // Estado do modal de confirmação
        aberto: false,       // controla se o modal está visível (true) ou escondido (false)
        titulo: '',          // título exibido no cabeçalho do modal (ex: "Excluir professor")
        mensagem: '',        // texto explicativo do modal (ex: "Esta ação não pode ser desfeita.")
        textoBotao: 'Confirmar', // texto do botão de ação (ex: "Excluir", "Desativar")
        _acao: null,         // função que será executada se o usuário confirmar
                             // _acao começa com _ por convenção: é "privado" (não deve ser acessado diretamente)

        // Método para abrir o modal com as informações corretas
        // Chamado assim: $store.dialogo.perguntar('Título', 'Mensagem', 'Botão', () => $wire.excluir(5))
        perguntar(titulo, mensagem, textoBotao, acao) {
            this.titulo     = titulo;        // define o título do modal
            this.mensagem   = mensagem;      // define a mensagem do modal
            this.textoBotao = textoBotao ?? 'Confirmar'; // ?? = se textoBotao for null/undefined, usa 'Confirmar'
            this._acao      = acao;          // guarda a função para executar se confirmar
            this.aberto     = true;          // exibe o modal
        },

        // Método chamado quando o usuário clica no botão de confirmação
        confirmar() {
            if (this._acao) this._acao(); // executa a função guardada (ex: chama $wire.excluir(5))
            this.aberto = false;          // fecha o modal
            this._acao  = null;           // limpa a ação para não executar acidentalmente depois
        },

        // Método chamado quando o usuário clica em "Cancelar" ou fecha o modal
        cancelar() {
            this.aberto = false; // fecha o modal sem executar nada
            this._acao  = null;  // limpa a ação guardada
        },
    });
});
