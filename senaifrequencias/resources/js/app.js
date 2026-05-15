import './bootstrap';

document.addEventListener('alpine:init', () => {
    Alpine.store('dialogo', {
        aberto: false,
        titulo: '',
        mensagem: '',
        textoBotao: 'Confirmar',
        _acao: null,
        perguntar(titulo, mensagem, textoBotao, acao) {
            this.titulo     = titulo;
            this.mensagem   = mensagem;
            this.textoBotao = textoBotao ?? 'Confirmar';
            this._acao      = acao;
            this.aberto     = true;
        },
        confirmar() {
            if (this._acao) this._acao();
            this.aberto = false;
            this._acao  = null;
        },
        cancelar() {
            this.aberto = false;
            this._acao  = null;
        },
    });
});
