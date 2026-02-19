<?php
// login.php
session_start();
require_once 'config/funcoes.php';

$mensagem = '';
$tipo_mensagem = '';
$funcoes = new Funcoes();

// Se já estiver logado, redireciona para dashboard
if(isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Processar login
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];
    
    $usuario = $funcoes->login($email, $senha);
    
    if($usuario) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nome'] = $usuario['nome'];
        $_SESSION['usuario_email'] = $usuario['email'];
        $_SESSION['usuario_tipo'] = $usuario['tipo'];
        
        header("Location: dashboard.php");
        exit();
    } else {
        $mensagem = "Email ou senha incorretos!";
        $tipo_mensagem = 'erro';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Home - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        :root {
            /* Tema Claro (Padrão) */
            --cor-fundo: #F8F6F3;
            --cor-secundaria: #E6E2F0;
            --cor-terciaria: #F0B429;
            --cor-texto: #333333;
            --cor-card: #FFFFFF;
            --cor-erro: #dc3545;
            --cor-sucesso: #28a745;
            --cor-borda: #E6E2F0;
            --cor-sombra: rgba(0, 0, 0, 0.1);
            --cor-input-fundo: #FFFFFF;
        }

        [data-tema="escuro"] {
            /* Tema Escuro */
            --cor-fundo: #121212;
            --cor-secundaria: #1E1E1E;
            --cor-terciaria: #F0B429;
            --cor-texto: #E0E0E0;
            --cor-card: #1E1E1E;
            --cor-erro: #ff6b6b;
            --cor-sucesso: #4CAF50;
            --cor-borda: #333333;
            --cor-sombra: rgba(0, 0, 0, 0.3);
            --cor-input-fundo: #2D2D2D;
        }

        body {
            background-color: var(--cor-fundo);
            color: var(--cor-texto);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            transition: background-color 0.3s, color 0.3s;
        }

        /* Botão Tema */
        .theme-toggle {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }

        .theme-toggle button {
            background: var(--cor-card);
            border: 2px solid var(--cor-borda);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            box-shadow: 0 2px 10px var(--cor-sombra);
        }

        .theme-toggle button:hover {
            transform: rotate(30deg);
            box-shadow: 0 4px 15px var(--cor-sombra);
        }

        .theme-toggle i {
            font-size: 20px;
            color: var(--cor-texto);
        }

        .theme-toggle .fa-moon {
            display: none;
        }

        [data-tema="escuro"] .theme-toggle .fa-sun {
            display: none;
        }

        [data-tema="escuro"] .theme-toggle .fa-moon {
            display: inline-block;
        }

        /* Container Principal */
        .login-container {
            width: 100%;
            max-width: 400px;
        }

        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-logo h1 {
            font-size: 36px;
            color: var(--cor-terciaria);
            margin-bottom: 10px;
        }

        .login-logo p {
            color: var(--cor-texto);
            opacity: 0.7;
        }

        /* Card de Login */
        .login-card {
            background-color: var(--cor-card);
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 10px 30px var(--cor-sombra);
            border: 1px solid var(--cor-borda);
            transition: all 0.3s;
        }

        /* Formulário */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--cor-texto);
        }

        .form-group label.required::after {
            content: " *";
            color: var(--cor-erro);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            background-color: var(--cor-input-fundo);
            color: var(--cor-texto);
            border: 2px solid var(--cor-borda);
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--cor-terciaria);
            box-shadow: 0 0 0 3px rgba(240, 180, 41, 0.1);
        }

        /* Botão */
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
        }

        .btn-primary {
            background-color: var(--cor-terciaria);
            color: #000;
        }

        .btn-primary:hover {
            background-color: #e0a010;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(240, 180, 41, 0.3);
        }

        /* Mensagens */
        .mensagem {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
            border: 1px solid transparent;
            background-color: rgba(0, 0, 0, 0.05);
        }

        .mensagem.erro {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--cor-erro);
            border-color: rgba(220, 53, 69, 0.2);
        }

        .mensagem.sucesso {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--cor-sucesso);
            border-color: rgba(40, 167, 69, 0.2);
        }

        /* Links */
        .links-extras {
            margin-top: 20px;
            text-align: center;
        }

        .links-extras a {
            color: var(--cor-terciaria);
            text-decoration: none;
            font-weight: 600;
            display: block;
            margin: 10px 0;
            transition: color 0.3s;
        }

        .links-extras a:hover {
            text-decoration: underline;
            color: #e0a010;
        }

        .links-extras .divider {
            margin: 20px 0;
            position: relative;
            text-align: center;
            color: var(--cor-texto);
            opacity: 0.5;
        }

        .links-extras .divider::before {
            content: "";
            position: absolute;
            left: 0;
            top: 50%;
            width: 45%;
            height: 1px;
            background-color: var(--cor-borda);
        }

        .links-extras .divider::after {
            content: "";
            position: absolute;
            right: 0;
            top: 50%;
            width: 45%;
            height: 1px;
            background-color: var(--cor-borda);
        }

        /* Checkbox Lembrar-me */
        .lembrar-me {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 15px 0;
        }

        .lembrar-me input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: var(--cor-terciaria);
        }

        .lembrar-me label {
            color: var(--cor-texto);
            font-size: 14px;
            cursor: pointer;
        }

        /* Responsivo */
        @media (max-width: 480px) {
            .login-card {
                padding: 30px 20px;
            }
            
            .theme-toggle {
                top: 10px;
                right: 10px;
            }
            
            .theme-toggle button {
                width: 40px;
                height: 40px;
            }
            
            .login-logo h1 {
                font-size: 28px;
            }
        }

        /* Animação de entrada */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-card {
            animation: fadeIn 0.5s ease-out;
        }

        /* Input com ícone */
        .input-with-icon {
            position: relative;
        }

        .input-with-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--cor-texto);
            opacity: 0.7;
        }

        .input-with-icon .form-control {
            padding-left: 45px;
        }
    </style>
</head>
<body>
    <!-- Botão Tema -->
    <div class="theme-toggle">
        <button id="themeToggle" aria-label="Alternar tema">
            <i class="fas fa-sun"></i>
            <i class="fas fa-moon"></i>
        </button>
    </div>

    <div class="login-container">
        <div class="login-logo">
            <h1><i class="fas fa-home"></i> NewHome</h1>
            <p>Faça login para continuar</p>
        </div>
        
        <?php if($mensagem): ?>
        <div class="mensagem <?php echo $tipo_mensagem; ?>">
            <i class="fas fa-exclamation-circle"></i> <?php echo $mensagem; ?>
        </div>
        <?php endif; ?>
        
        <div class="login-card">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="email" class="required"><i class="fas fa-envelope"></i> Email</label>
                    <div class="input-with-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" class="form-control" placeholder="seu@email.com" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="senha" class="required"><i class="fas fa-lock"></i> Senha</label>
                    <div class="input-with-icon">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="senha" name="senha" class="form-control" placeholder="Sua senha" required>
                    </div>
                </div>
                
                <div class="lembrar-me">
                    <input type="checkbox" id="lembrar" name="lembrar">
                    <label for="lembrar">Lembrar-me</label>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i> Entrar
                </button>
            </form>
            
            <div class="links-extras">
                <a href="cadastro.php?tipo=cliente">
                    <i class="fas fa-user-plus"></i> Criar conta de cliente
                </a>
                
                <?php if(isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] == 'admin'): ?>
                <a href="cadastro.php?tipo=gerente">
                    <i class="fas fa-user-tie"></i> Cadastrar gerente
                </a>
                <?php endif; ?>
                
                <div class="divider">ou</div>
                
                <a href="#">
                    <i class="fas fa-key"></i> Esqueci minha senha
                </a>
            </div>
        </div>
    </div>

    <script>
        // Sistema de Tema
        class TemaManager {
            constructor() {
                this.temaToggle = document.getElementById('themeToggle');
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
            }

            ativarTemaClaro() {
                document.documentElement.setAttribute('data-tema', 'claro');
                localStorage.setItem('tema', 'claro');
            }

            alternarTema() {
                if (document.documentElement.getAttribute('data-tema') === 'escuro') {
                    this.ativarTemaClaro();
                } else {
                    this.ativarTemaEscuro();
                }
            }
        }

        // Inicializar tema
        document.addEventListener('DOMContentLoaded', () => {
            new TemaManager();
            
            // Verificar se há email salvo para lembrar-me
            const emailSalvo = localStorage.getItem('lembrarEmail');
            if(emailSalvo) {
                document.getElementById('email').value = emailSalvo;
                document.getElementById('lembrar').checked = true;
            }
        });

        // Salvar email se lembrar-me estiver marcado
        document.querySelector('form').addEventListener('submit', function(e) {
            const lembrarCheckbox = document.getElementById('lembrar');
            const emailInput = document.getElementById('email');
            
            if(lembrarCheckbox.checked && emailInput.value) {
                localStorage.setItem('lembrarEmail', emailInput.value);
            } else {
                localStorage.removeItem('lembrarEmail');
            }
        });
    </script>
</body>
</html>