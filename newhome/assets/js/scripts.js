// assets/js/scripts.js

// ===== FUNÇÕES GERAIS =====
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tema
    initTheme();
    
    // Inicializar máscaras
    initMasks();
    
    // Inicializar dropdowns
    initDropdowns();
    
    // Inicializar filtros de imóveis
    initPropertyFilters();
    
    // Inicializar validação de formulários
    initFormValidation();
});

// ===== TEMA CLARO/ESCURO =====
function initTheme() {
    const themeToggle = document.getElementById('themeToggle');
    const body = document.body;
    
    if(!themeToggle) return;
    
    // Verificar preferência salva
    const savedTheme = localStorage.getItem('theme') || 'light';
    if (savedTheme === 'dark') {
        body.classList.add('dark-theme');
    }
    
    themeToggle.addEventListener('click', () => {
        body.classList.toggle('dark-theme');
        const currentTheme = body.classList.contains('dark-theme') ? 'dark' : 'light';
        localStorage.setItem('theme', currentTheme);
        
        // Disparar evento personalizado
        document.dispatchEvent(new CustomEvent('themeChanged', { detail: { theme: currentTheme } }));
    });
}

// ===== MÁSCARAS DE FORMULÁRIO =====
function initMasks() {
    // Máscara para CPF
    const cpfInput = document.getElementById('cpf');
    if(cpfInput) {
        cpfInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if(value.length > 3 && value.length <= 6) {
                value = value.replace(/(\d{3})(\d+)/, '$1.$2');
            } else if(value.length > 6 && value.length <= 9) {
                value = value.replace(/(\d{3})(\d{3})(\d+)/, '$1.$2.$3');
            } else if(value.length > 9) {
                value = value.replace(/(\d{3})(\d{3})(\d{3})(\d+)/, '$1.$2.$3-$4');
            }
            e.target.value = value;
        });
    }
    
    // Máscara para telefone
    const telefoneInput = document.getElementById('telefone');
    if(telefoneInput) {
        telefoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if(value.length > 0) {
                value = '(' + value;
                if(value.length > 3) {
                    value = value.substring(0, 3) + ') ' + value.substring(3);
                }
                if(value.length > 10) {
                    value = value.substring(0, 10) + '-' + value.substring(10);
                }
            }
            e.target.value = value.substring(0, 15);
        });
    }
    
    // Máscara para CEP
    const cepInput = document.getElementById('cep');
    if(cepInput) {
        cepInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if(value.length > 5) {
                value = value.replace(/(\d{5})(\d+)/, '$1-$2');
            }
            e.target.value = value.substring(0, 9);
        });
    }
    
    // Máscara para valor monetário
    const valorInput = document.getElementById('valor');
    if(valorInput) {
        valorInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = (value/100).toFixed(2) + '';
            value = value.replace(".", ",");
            value = value.replace(/(\d)(\d{3})(\d{3}),/g, "$1.$2.$3,");
            value = value.replace(/(\d)(\d{3}),/g, "$1.$2,");
            e.target.value = 'R$ ' + value;
        });
    }
}

// ===== DROPDOWNS =====
function initDropdowns() {
    // Dropdown do usuário
    const userBtn = document.querySelector('.user-btn');
    if(userBtn) {
        userBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdown = this.nextElementSibling;
            dropdown.classList.toggle('show');
        });
        
        // Fechar dropdown ao clicar fora
        document.addEventListener('click', function(e) {
            if(!e.target.closest('.user-dropdown')) {
                document.querySelectorAll('.dropdown-menu.show').forEach(dropdown => {
                    dropdown.classList.remove('show');
                });
            }
        });
    }
    
    // Dropdowns de filtro
    document.querySelectorAll('.filter-dropdown').forEach(dropdown => {
        const btn = dropdown.querySelector('.filter-btn');
        const menu = dropdown.querySelector('.filter-menu');
        
        if(btn && menu) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                menu.classList.toggle('show');
            });
        }
    });
}

// ===== FILTROS DE IMÓVEIS =====
function initPropertyFilters() {
    const filterForm = document.getElementById('filterForm');
    if(!filterForm) return;
    
    // Aplicar filtros
    filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        applyFilters();
    });
    
    // Limpar filtros
    const clearBtn = document.getElementById('clearFilters');
    if(clearBtn) {
        clearBtn.addEventListener('click', function() {
            filterForm.reset();
            applyFilters();
        });
    }
}

function applyFilters() {
    const form = document.getElementById('filterForm');
    const formData = new FormData(form);
    const filters = {};
    
    for(let [key, value] of formData.entries()) {
        if(value) filters[key] = value;
    }
    
    // Aqui você faria uma requisição AJAX para filtrar os imóveis
    console.log('Filtros aplicados:', filters);
    
    // Mostrar mensagem
    showAlert('Filtros aplicados com sucesso!', 'success');
    
    // Em um sistema real, você faria:
    // fetch('api/filter_properties.php', {
    //     method: 'POST',
    //     body: JSON.stringify(filters)
    // })
    // .then(response => response.json())
    // .then(data => updatePropertiesGrid(data));
}

// ===== VALIDAÇÃO DE FORMULÁRIOS =====
function initFormValidation() {
    // Validação de cadastro
    const cadastroForm = document.getElementById('cadastroForm');
    if(cadastroForm) {
        cadastroForm.addEventListener('submit', function(e) {
            if(!validateCadastroForm()) {
                e.preventDefault();
            }
        });
    }
    
    // Validação de login
    const loginForm = document.getElementById('loginForm');
    if(loginForm) {
        loginForm.addEventListener('submit', function(e) {
            if(!validateLoginForm()) {
                e.preventDefault();
            }
        });
    }
}

function validateCadastroForm() {
    const senha = document.getElementById('senha');
    const confirmarSenha = document.getElementById('confirmar_senha');
    const cpf = document.getElementById('cpf');
    
    let isValid = true;
    
    // Validar senhas
    if(senha && confirmarSenha && senha.value !== confirmarSenha.value) {
        showAlert('As senhas não coincidem!', 'danger');
        isValid = false;
    }
    
    // Validar CPF
    if(cpf && !validateCPF(cpf.value.replace(/\D/g, ''))) {
        showAlert('CPF inválido!', 'danger');
        isValid = false;
    }
    
    return isValid;
}

function validateLoginForm() {
    const email = document.getElementById('email');
    const senha = document.getElementById('senha');
    
    let isValid = true;
    
    if(email && !validateEmail(email.value)) {
        showAlert('Email inválido!', 'danger');
        isValid = false;
    }
    
    if(senha && senha.value.length < 6) {
        showAlert('Senha deve ter no mínimo 6 caracteres!', 'danger');
        isValid = false;
    }
    
    return isValid;
}

// ===== VALIDAÇÕES =====
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validateCPF(cpf) {
    if(cpf.length !== 11) return false;
    
    // Validação simplificada de CPF
    let sum = 0;
    let remainder;
    
    // Primeiro dígito verificador
    for(let i = 1; i <= 9; i++) {
        sum += parseInt(cpf.substring(i-1, i)) * (11 - i);
    }
    remainder = (sum * 10) % 11;
    if((remainder === 10) || (remainder === 11)) remainder = 0;
    if(remainder !== parseInt(cpf.substring(9, 10))) return false;
    
    // Segundo dígito verificador
    sum = 0;
    for(let i = 1; i <= 10; i++) {
        sum += parseInt(cpf.substring(i-1, i)) * (12 - i);
    }
    remainder = (sum * 10) % 11;
    if((remainder === 10) || (remainder === 11)) remainder = 0;
    if(remainder !== parseInt(cpf.substring(10, 11))) return false;
    
    return true;
}

// ===== FUNÇÕES AUXILIARES =====
function showAlert(message, type = 'info') {
    // Remover alertas anteriores
    const existingAlert = document.querySelector('.alert');
    if(existingAlert) {
        existingAlert.remove();
    }
    
    // Criar novo alerta
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.innerHTML = `
        ${message}
        <button class="alert-close" onclick="this.parentElement.remove()">&times;</button>
    `;
    
    // Adicionar ao topo da página
    const container = document.querySelector('.container') || document.body;
    container.insertBefore(alertDiv, container.firstChild);
    
    // Remover automaticamente após 5 segundos
    setTimeout(() => {
        if(alertDiv.parentElement) {
            alertDiv.remove();
        }
    }, 5000);
}

// ===== IMÓVEIS - FAVORITOS =====
function toggleFavorite(propertyId) {
    const favorites = JSON.parse(localStorage.getItem('favorites') || '[]');
    const index = favorites.indexOf(propertyId);
    
    if(index === -1) {
        favorites.push(propertyId);
        showAlert('Imóvel adicionado aos favoritos!', 'success');
    } else {
        favorites.splice(index, 1);
        showAlert('Imóvel removido dos favoritos!', 'info');
    }
    
    localStorage.setItem('favorites', JSON.stringify(favorites));
    updateFavoriteButtons();
}

function updateFavoriteButtons() {
    const favorites = JSON.parse(localStorage.getItem('favorites') || '[]');
    
    document.querySelectorAll('.favorite-btn').forEach(btn => {
        const propertyId = btn.dataset.propertyId;
        const icon = btn.querySelector('i');
        
        if(favorites.includes(parseInt(propertyId))) {
            icon.className = 'fas fa-heart';
            btn.style.color = '#dc3545';
        } else {
            icon.className = 'far fa-heart';
            btn.style.color = '';
        }
    });
}

// ===== LOADING SPINNER =====
function showLoading() {
    const spinner = document.createElement('div');
    spinner.className = 'loading-spinner';
    spinner.innerHTML = '<div class="spinner"></div>';
    document.body.appendChild(spinner);
}

function hideLoading() {
    const spinner = document.querySelector('.loading-spinner');
    if(spinner) {
        spinner.remove();
    }
}

// ===== FORMATAÇÃO =====
function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR');
}

// ===== AJAX REQUESTS =====
function makeRequest(url, method = 'GET', data = null) {
    showLoading();
    
    return fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
        },
        body: data ? JSON.stringify(data) : null
    })
    .then(response => {
        hideLoading();
        if(!response.ok) {
            throw new Error('Erro na requisição');
        }
        return response.json();
    })
    .catch(error => {
        hideLoading();
        showAlert('Erro: ' + error.message, 'danger');
        console.error('Erro:', error);
    });
}

// ===== ANIMAÇÕES =====
function animateOnScroll() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if(entry.isIntersecting) {
                entry.target.classList.add('animate');
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });
    
    // Observar elementos com classe 'animate-on-scroll'
    document.querySelectorAll('.animate-on-scroll').forEach(el => {
        observer.observe(el);
    });
}

// Inicializar animações quando o DOM carregar
document.addEventListener('DOMContentLoaded', animateOnScroll);