// assets/js/tema.js

class TemaManager {
    constructor() {
        this.temaToggle = document.getElementById('themeToggle');
        this.iconSol = this.temaToggle?.querySelector('.fa-sun');
        this.iconLua = this.temaToggle?.querySelector('.fa-moon');
        this.init();
    }

    init() {
        // Verificar tema salvo ou preferência do sistema
        const temaSalvo = localStorage.getItem('tema');
        const prefereEscuro = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        if (temaSalvo === 'escuro' || (!temaSalvo && prefereEscuro)) {
            this.ativarTemaEscuro();
        } else {
            this.ativarTemaClaro();
        }

        // Adicionar evento de clique
        if (this.temaToggle) {
            this.temaToggle.addEventListener('click', () => this.alternarTema());
        }
    }

    ativarTemaEscuro() {
        document.documentElement.setAttribute('data-tema', 'escuro');
        localStorage.setItem('tema', 'escuro');
        
        if (this.iconSol && this.iconLua) {
            this.iconSol.style.display = 'none';
            this.iconLua.style.display = 'inline-block';
        }
    }

    ativarTemaClaro() {
        document.documentElement.setAttribute('data-tema', 'claro');
        localStorage.setItem('tema', 'claro');
        
        if (this.iconSol && this.iconLua) {
            this.iconSol.style.display = 'inline-block';
            this.iconLua.style.display = 'none';
        }
    }

    alternarTema() {
        if (document.documentElement.getAttribute('data-tema') === 'escuro') {
            this.ativarTemaClaro();
        } else {
            this.ativarTemaEscuro();
        }
    }
}

// Inicializar quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', () => {
    new TemaManager();
});