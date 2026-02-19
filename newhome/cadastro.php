<?php
// cadastro.php
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

// Verificar tipo de cadastro (cliente ou gerente) - agora via POST
$tipo_cadastro = 'cliente'; // padrão
if(isset($_POST['tipo_cadastro'])) {
    $tipo_cadastro = $_POST['tipo_cadastro'];
} elseif(isset($_GET['tipo'])) {
    $tipo_cadastro = $_GET['tipo'];
}

if(!in_array($tipo_cadastro, ['cliente', 'gerente'])) {
    $tipo_cadastro = 'cliente';
}

// Processar cadastro
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nome'])) {
    $dados = [];
    
    if($tipo_cadastro == 'cliente') {
        // Dados para cliente
        $dados = [
            'nome' => filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING),
            'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
            'senha' => $_POST['senha'],
            'confirmar_senha' => $_POST['confirmar_senha'],
            'tipo' => 'cliente',
            'telefone' => filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING),
            'cpf' => filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING),
            'rg' => filter_input(INPUT_POST, 'rg', FILTER_SANITIZE_STRING),
            'profissao' => filter_input(INPUT_POST, 'profissao', FILTER_SANITIZE_STRING),
            'estado_civil' => $_POST['estado_civil'] ?? 'solteiro',
            'data_nascimento' => $_POST['data_nascimento'],
            'endereco' => filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_STRING),
            'cep' => filter_input(INPUT_POST, 'cep', FILTER_SANITIZE_STRING),
            'numero' => filter_input(INPUT_POST, 'numero', FILTER_SANITIZE_STRING),
            'complemento' => filter_input(INPUT_POST, 'complemento', FILTER_SANITIZE_STRING)
        ];
        
        // Validações específicas para cliente
        if(empty($dados['rg'])) {
            $mensagem = "O RG é obrigatório!";
            $tipo_mensagem = 'erro';
        }
    } else {
        // Dados para gerente (somente admin pode cadastrar)
        // Verificação se é admin
        $pode_cadastrar_gerente = false;
        if(isset($_SESSION['usuario_id']) && $_SESSION['usuario_tipo'] == 'admin') {
            $pode_cadastrar_gerente = true;
        }
        
        // Se não for admin, redireciona para login
        if(!$pode_cadastrar_gerente) {
            $_SESSION['mensagem'] = "Apenas administradores podem cadastrar gerentes.";
            $_SESSION['tipo_mensagem'] = 'erro';
            header("Location: login.php");
            exit();
        }
        
        $dados = [
            'nome' => filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING),
            'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
            'senha' => $_POST['senha'],
            'confirmar_senha' => $_POST['confirmar_senha'],
            'tipo' => 'gerente',
            'telefone' => filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING),
            'cpf' => filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING),
            'cep' => filter_input(INPUT_POST, 'cep', FILTER_SANITIZE_STRING),
            'endereco' => filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_STRING),
            'data_contratacao' => $_POST['data_contratacao'],
            'salario' => $_POST['salario'],
            'setor' => $_POST['setor']
        ];
    }
    
    // Validações comuns
    if(empty($mensagem)) {
        if($funcoes->verificarEmail($dados['email'])) {
            $mensagem = "Este email já está cadastrado!";
            $tipo_mensagem = 'erro';
        } elseif($dados['senha'] !== $dados['confirmar_senha']) {
            $mensagem = "As senhas não coincidem!";
            $tipo_mensagem = 'erro';
        } elseif(strlen($dados['senha']) < 6) {
            $mensagem = "A senha deve ter no mínimo 6 caracteres!";
            $tipo_mensagem = 'erro';
        } else {
            // Remover confirmação de senha
            unset($dados['confirmar_senha']);
            
            // Processar upload de fotos (apenas para cliente)
            if($tipo_cadastro == 'cliente') {
                if(isset($_FILES['foto_documento']) && $_FILES['foto_documento']['error'] == 0) {
                    $upload_result = $funcoes->processarUploads($dados['cpf']);
                    if($upload_result['sucesso']) {
                        $dados['foto_documento_path'] = $upload_result['foto_documento_path'];
                        $dados['selfie_documento_path'] = $upload_result['selfie_documento_path'];
                    } else {
                        $mensagem = $upload_result['mensagem'];
                        $tipo_mensagem = 'erro';
                    }
                } else {
                    $mensagem = "É necessário enviar os documentos (foto e selfie).";
                    $tipo_mensagem = 'erro';
                }
            }
            
            if(empty($mensagem)) {
                if($funcoes->cadastrarUsuario($dados, $tipo_cadastro)) {
                    if($tipo_cadastro == 'cliente') {
                        $mensagem = "Cadastro realizado com sucesso! Em breve analisaremos seus documentos.";
                        $tipo_mensagem = 'sucesso';
                    } else {
                        $mensagem = "Gerente cadastrado com sucesso!";
                        $tipo_mensagem = 'sucesso';
                    }
                    
                    // Se for cliente, redireciona para login após 5 segundos
                    if($tipo_cadastro == 'cliente') {
                        header("refresh:5;url=login.php");
                    } else {
                        // Se for gerente (admin cadastrando), redireciona para dashboard
                        header("refresh:3;url=dashboard.php");
                    }
                } else {
                    $mensagem = "Erro ao cadastrar. Tente novamente.";
                    $tipo_mensagem = 'erro';
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Home - Cadastro</title>
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
            --cor-info: #17a2b8;
            --cor-borda: #E6E2F0;
            --cor-sombra: rgba(0, 0, 0, 0.1);
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
            --cor-info: #29B6F6;
            --cor-borda: #333333;
            --cor-sombra: rgba(0, 0, 0, 0.3);
        }

        body {
            background-color: var(--cor-fundo);
            color: var(--cor-texto);
            min-height: 100vh;
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
        .cadastro-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .cadastro-logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .cadastro-logo h1 {
            font-size: 36px;
            color: var(--cor-terciaria);
            margin-bottom: 10px;
        }

        .cadastro-logo p {
            color: var(--cor-texto);
            opacity: 0.7;
        }

        /* Seleção de Tipo */
        .tipo-selecao {
            background-color: var(--cor-card);
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px var(--cor-sombra);
            border: 1px solid var(--cor-borda);
        }

        .tipo-selecao h3 {
            text-align: center;
            margin-bottom: 20px;
            color: var(--cor-texto);
        }

        .tipos-container {
            display: flex;
            gap: 20px;
            justify-content: center;
        }

        @media (max-width: 768px) {
            .tipos-container {
                flex-direction: column;
                align-items: center;
            }
        }

        .tipo-option {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            border: 2px solid var(--cor-borda);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
            width: 200px;
            text-align: center;
            background-color: var(--cor-secundaria);
        }

        .tipo-option:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px var(--cor-sombra);
            border-color: var(--cor-terciaria);
        }

        .tipo-option.selected {
            border-color: var(--cor-terciaria);
            background-color: rgba(240, 180, 41, 0.1);
            box-shadow: 0 0 0 3px rgba(240, 180, 41, 0.1);
        }

        .tipo-option i {
            font-size: 40px;
            margin-bottom: 10px;
            color: var(--cor-terciaria);
        }

        .tipo-option h4 {
            margin-bottom: 5px;
            color: var(--cor-texto);
        }

        .tipo-option p {
            font-size: 14px;
            color: var(--cor-texto);
            opacity: 0.8;
        }

        .tipo-option input[type="radio"] {
            display: none;
        }

        /* Info Box */
        .tipo-info {
            background-color: var(--cor-secundaria);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid var(--cor-borda);
            display: none;
        }

        .tipo-info.active {
            display: block;
            animation: fadeIn 0.5s ease-out;
        }

        /* Formulário */
        .formulario-container {
            background-color: var(--cor-card);
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 10px 30px var(--cor-sombra);
            border: 1px solid var(--cor-borda);
            transition: all 0.3s;
            display: none;
        }

        .formulario-container.active {
            display: block;
            animation: fadeIn 0.5s ease-out;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-row {
            display: flex;
            gap: 15px;
        }

        .form-row .form-group {
            flex: 1;
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
            background-color: var(--cor-card);
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

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23666' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
            padding-right: 40px;
        }

        [data-tema="escuro"] select.form-control {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%23E0E0E0' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
        }

        .file-upload {
            border: 2px dashed var(--cor-borda);
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            background-color: var(--cor-secundaria);
        }

        .file-upload:hover {
            border-color: var(--cor-terciaria);
            background-color: rgba(240, 180, 41, 0.05);
        }

        .file-upload input[type="file"] {
            display: none;
        }

        .file-upload-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .file-upload-label i {
            font-size: 24px;
            color: var(--cor-terciaria);
        }

        .file-upload-label span {
            color: var(--cor-texto);
            font-weight: 500;
        }

        .file-upload-label small {
            color: var(--cor-texto);
            opacity: 0.7;
        }

        .file-preview {
            margin-top: 10px;
            max-width: 200px;
            border-radius: 4px;
            display: none;
            border: 1px solid var(--cor-borda);
        }

        .cep-search {
            display: flex;
            gap: 10px;
        }

        .cep-search .form-control {
            flex: 1;
        }

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
            gap: 10px;
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

        .btn-secondary {
            background-color: var(--cor-secundaria);
            color: var(--cor-texto);
            border: 1px solid var(--cor-borda);
        }

        .btn-secondary:hover {
            background-color: var(--cor-borda);
            transform: translateY(-2px);
        }

        .btn-full {
            width: 100%;
            justify-content: center;
        }

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

        .mensagem.info {
            background-color: rgba(23, 162, 184, 0.1);
            color: var(--cor-info);
            border-color: rgba(23, 162, 184, 0.2);
        }

        .links-extras {
            margin-top: 20px;
            text-align: center;
        }

        .links-extras a {
            color: var(--cor-terciaria);
            text-decoration: none;
            font-weight: 600;
        }

        .links-extras a:hover {
            text-decoration: underline;
        }

        .hidden {
            display: none;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .form-row .form-group {
                margin-bottom: 20px;
            }
            
            .formulario-container,
            .tipo-selecao {
                padding: 20px;
            }
            
            .theme-toggle {
                top: 10px;
                right: 10px;
            }
            
            .theme-toggle button {
                width: 40px;
                height: 40px;
            }
            
            .tipo-option {
                width: 100%;
                max-width: 250px;
            }
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
    
    <div class="cadastro-container">
        <div class="cadastro-logo">
            <h1><i class="fas fa-home"></i> NewHome</h1>
            <p>Crie sua conta para começar</p>
        </div>
        
        <?php if($mensagem): ?>
        <div class="mensagem <?php echo $tipo_mensagem; ?>">
            <i class="fas fa-exclamation-circle"></i> <?php echo $mensagem; ?>
        </div>
        <?php endif; ?>
        
        <!-- Seleção de Tipo -->
        <div class="tipo-selecao">
            <h3>Como você deseja se cadastrar?</h3>
            <div class="tipos-container">
                <label class="tipo-option <?php echo $tipo_cadastro == 'cliente' ? 'selected' : ''; ?>" for="tipo-cliente">
                    <i class="fas fa-user"></i>
                    <h4>Cliente</h4>
                    <p>Procura imóveis para alugar ou comprar</p>
                    <input type="radio" id="tipo-cliente" name="tipo_cadastro" value="cliente" 
                           <?php echo $tipo_cadastro == 'cliente' ? 'checked' : ''; ?>>
                </label>
                
                <label class="tipo-option <?php echo $tipo_cadastro == 'gerente' ? 'selected' : ''; ?>" for="tipo-gerente">
                    <i class="fas fa-user-tie"></i>
                    <h4>Gerente</h4>
                    <p>Gerencia imóveis e contratos</p>
                    <input type="radio" id="tipo-gerente" name="tipo_cadastro" value="gerente"
                           <?php echo $tipo_cadastro == 'gerente' ? 'checked' : ''; ?>>
                </label>
            </div>
        </div>
        
        <!-- Info Box -->
        <div class="tipo-info <?php echo $tipo_cadastro == 'cliente' ? 'active' : ''; ?>" id="info-cliente">
            <i class="fas fa-info-circle"></i> Para cadastro como cliente, é necessário enviar documentos para verificação.
        </div>
        
        <div class="tipo-info <?php echo $tipo_cadastro == 'gerente' ? 'active' : ''; ?>" id="info-gerente">
            <i class="fas fa-info-circle"></i> Cadastro de gerente disponível apenas para administradores do sistema.
        </div>
        
        <!-- Formulário Cliente -->
        <form method="POST" action="" enctype="multipart/form-data" class="formulario-container <?php echo $tipo_cadastro == 'cliente' ? 'active' : ''; ?>" id="form-cliente">
            <input type="hidden" name="tipo_cadastro" value="cliente">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="nome" class="required"><i class="fas fa-user"></i> Nome Completo</label>
                    <input type="text" id="nome" name="nome" class="form-control" required value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="email" class="required"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email" class="form-control" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="cpf" class="required"><i class="fas fa-id-card"></i> CPF</label>
                    <input type="text" id="cpf" name="cpf" class="form-control" required value="<?php echo isset($_POST['cpf']) ? htmlspecialchars($_POST['cpf']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="rg" class="required"><i class="fas fa-id-card"></i> RG</label>
                    <input type="text" id="rg" name="rg" class="form-control" required value="<?php echo isset($_POST['rg']) ? htmlspecialchars($_POST['rg']) : ''; ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="telefone" class="required"><i class="fas fa-phone"></i> Telefone</label>
                    <input type="tel" id="telefone" name="telefone" class="form-control" required value="<?php echo isset($_POST['telefone']) ? htmlspecialchars($_POST['telefone']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="profissao"><i class="fas fa-briefcase"></i> Profissão</label>
                    <input type="text" id="profissao" name="profissao" class="form-control" value="<?php echo isset($_POST['profissao']) ? htmlspecialchars($_POST['profissao']) : ''; ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="estado_civil"><i class="fas fa-heart"></i> Estado Civil</label>
                    <select id="estado_civil" name="estado_civil" class="form-control">
                        <option value="solteiro" <?php echo (isset($_POST['estado_civil']) && $_POST['estado_civil'] == 'solteiro') ? 'selected' : ''; ?>>Solteiro(a)</option>
                        <option value="casado" <?php echo (isset($_POST['estado_civil']) && $_POST['estado_civil'] == 'casado') ? 'selected' : ''; ?>>Casado(a)</option>
                        <option value="divorciado" <?php echo (isset($_POST['estado_civil']) && $_POST['estado_civil'] == 'divorciado') ? 'selected' : ''; ?>>Divorciado(a)</option>
                        <option value="viuvo" <?php echo (isset($_POST['estado_civil']) && $_POST['estado_civil'] == 'viuvo') ? 'selected' : ''; ?>>Viúvo(a)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="data_nascimento" class="required"><i class="fas fa-calendar"></i> Data de Nascimento</label>
                    <input type="date" id="data_nascimento" name="data_nascimento" class="form-control" required value="<?php echo isset($_POST['data_nascimento']) ? htmlspecialchars($_POST['data_nascimento']) : ''; ?>">
                </div>
            </div>
            
            <!-- Endereço -->
            <div class="form-group">
                <label for="cep"><i class="fas fa-map-marker-alt"></i> CEP</label>
                <div class="cep-search">
                    <input type="text" id="cep" name="cep" class="form-control" placeholder="00000-000" value="<?php echo isset($_POST['cep']) ? htmlspecialchars($_POST['cep']) : ''; ?>">
                    <button type="button" id="buscar-cep" class="btn btn-secondary">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </div>
            
            <div class="form-group">
                <label for="endereco" class="required"><i class="fas fa-road"></i> Endereço</label>
                <input type="text" id="endereco" name="endereco" class="form-control" required value="<?php echo isset($_POST['endereco']) ? htmlspecialchars($_POST['endereco']) : ''; ?>">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="numero" class="required"><i class="fas fa-hashtag"></i> Número</label>
                    <input type="text" id="numero" name="numero" class="form-control" required value="<?php echo isset($_POST['numero']) ? htmlspecialchars($_POST['numero']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="complemento"><i class="fas fa-building"></i> Complemento</label>
                    <input type="text" id="complemento" name="complemento" class="form-control" value="<?php echo isset($_POST['complemento']) ? htmlspecialchars($_POST['complemento']) : ''; ?>">
                </div>
            </div>
            
            <!-- Upload de Documentos -->
            <div class="form-group">
                <label class="required"><i class="fas fa-file-image"></i> Foto do Documento (RG ou CNH)</label>
                <div class="file-upload">
                    <label class="file-upload-label">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>Clique para enviar foto do documento</span>
                        <small>Formatos: JPG, PNG | Máx: 2MB</small>
                        <input type="file" id="foto_documento" name="foto_documento" accept="image/*" required>
                    </label>
                    <img id="preview-documento" class="file-preview" src="" alt="Preview">
                </div>
            </div>
            
            <div class="form-group">
                <label class="required"><i class="fas fa-camera"></i> Selfie com Documento</label>
                <div class="file-upload">
                    <label class="file-upload-label">
                        <i class="fas fa-camera"></i>
                        <span>Clique para enviar selfie com documento</span>
                        <small>Deve mostrar seu rosto e o documento</small>
                        <input type="file" id="selfie_documento" name="selfie_documento" accept="image/*" required>
                    </label>
                    <img id="preview-selfie" class="file-preview" src="" alt="Preview">
                </div>
            </div>
            
            <!-- Senhas -->
            <div class="form-row">
                <div class="form-group">
                    <label for="senha" class="required"><i class="fas fa-lock"></i> Senha</label>
                    <input type="password" id="senha" name="senha" class="form-control" required minlength="6">
                </div>
                <div class="form-group">
                    <label for="confirmar_senha" class="required"><i class="fas fa-lock"></i> Confirmar Senha</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" class="form-control" required minlength="6">
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary btn-full">
                <i class="fas fa-user-plus"></i> Criar Conta de Cliente
            </button>
        </form>
        
        <!-- Formulário Gerente -->
        <form method="POST" action="" class="formulario-container <?php echo $tipo_cadastro == 'gerente' ? 'active' : ''; ?>" id="form-gerente">
            <input type="hidden" name="tipo_cadastro" value="gerente">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="nome_gerente" class="required"><i class="fas fa-user"></i> Nome Completo</label>
                    <input type="text" id="nome_gerente" name="nome" class="form-control" required value="<?php echo isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="email_gerente" class="required"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email_gerente" name="email" class="form-control" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="cpf_gerente" class="required"><i class="fas fa-id-card"></i> CPF</label>
                    <input type="text" id="cpf_gerente" name="cpf" class="form-control" required value="<?php echo isset($_POST['cpf']) ? htmlspecialchars($_POST['cpf']) : ''; ?>">
                </div>
                <div class="form-group">
                    <label for="telefone_gerente" class="required"><i class="fas fa-phone"></i> Telefone</label>
                    <input type="tel" id="telefone_gerente" name="telefone" class="form-control" required value="<?php echo isset($_POST['telefone']) ? htmlspecialchars($_POST['telefone']) : ''; ?>">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="setor" class="required"><i class="fas fa-building"></i> Setor</label>
                    <select id="setor" name="setor" class="form-control" required>
                        <option value="">Selecione o setor</option>
                        <option value="vendas" <?php echo (isset($_POST['setor']) && $_POST['setor'] == 'vendas') ? 'selected' : ''; ?>>Vendas</option>
                        <option value="locacao" <?php echo (isset($_POST['setor']) && $_POST['setor'] == 'locacao') ? 'selected' : ''; ?>>Locação</option>
                        <option value="administrativo" <?php echo (isset($_POST['setor']) && $_POST['setor'] == 'administrativo') ? 'selected' : ''; ?>>Administrativo</option>
                        <option value="financeiro" <?php echo (isset($_POST['setor']) && $_POST['setor'] == 'financeiro') ? 'selected' : ''; ?>>Financeiro</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="data_contratacao" class="required"><i class="fas fa-calendar-alt"></i> Data de Contratação</label>
                    <input type="date" id="data_contratacao" name="data_contratacao" class="form-control" required value="<?php echo isset($_POST['data_contratacao']) ? htmlspecialchars($_POST['data_contratacao']) : ''; ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="salario" class="required"><i class="fas fa-money-bill-wave"></i> Salário</label>
                <input type="text" id="salario" name="salario" class="form-control" placeholder="R$ 0,00" required value="<?php echo isset($_POST['salario']) ? htmlspecialchars($_POST['salario']) : ''; ?>">
            </div>
            
            <!-- Endereço Gerente -->
            <div class="form-group">
                <label for="cep_gerente"><i class="fas fa-map-marker-alt"></i> CEP</label>
                <div class="cep-search">
                    <input type="text" id="cep_gerente" name="cep" class="form-control" placeholder="00000-000" required value="<?php echo isset($_POST['cep']) ? htmlspecialchars($_POST['cep']) : ''; ?>">
                    <button type="button" id="buscar-cep-gerente" class="btn btn-secondary">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </div>
            
            <div class="form-group">
                <label for="endereco_gerente" class="required"><i class="fas fa-road"></i> Endereço</label>
                <input type="text" id="endereco_gerente" name="endereco" class="form-control" required value="<?php echo isset($_POST['endereco']) ? htmlspecialchars($_POST['endereco']) : ''; ?>">
            </div>
            
            <!-- Senhas -->
            <div class="form-row">
                <div class="form-group">
                    <label for="senha_gerente" class="required"><i class="fas fa-lock"></i> Senha</label>
                    <input type="password" id="senha_gerente" name="senha" class="form-control" required minlength="6">
                </div>
                <div class="form-group">
                    <label for="confirmar_senha_gerente" class="required"><i class="fas fa-lock"></i> Confirmar Senha</label>
                    <input type="password" id="confirmar_senha_gerente" name="confirmar_senha" class="form-control" required minlength="6">
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary btn-full">
                <i class="fas fa-user-tie"></i> Cadastrar Gerente
            </button>
        </form>
        
        <div class="links-extras">
            <p>Já tem uma conta? <a href="login.php">Faça login</a></p>
            <p><a href="index.php"><i class="fas fa-arrow-left"></i> Voltar para Home</a></p>
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
                const temaSalvo = localStorage.getItem('tema');
                const prefereEscuro = window.matchMedia('(prefers-color-scheme: dark)').matches;
                
                if (temaSalvo === 'escuro' || (!temaSalvo && prefereEscuro)) {
                    this.ativarTemaEscuro();
                } else {
                    this.ativarTemaClaro();
                }

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
            
            // Configurar seleção de tipo
            const tipoOptions = document.querySelectorAll('.tipo-option input[type="radio"]');
            tipoOptions.forEach(option => {
                option.addEventListener('change', function() {
                    // Remover seleção de todas as opções
                    document.querySelectorAll('.tipo-option').forEach(opt => {
                        opt.classList.remove('selected');
                    });
                    
                    // Adicionar seleção à opção atual
                    this.parentElement.classList.add('selected');
                    
                    const tipo = this.value;
                    
                    // Mostrar/ocultar info boxes
                    document.querySelectorAll('.tipo-info').forEach(info => {
                        info.classList.remove('active');
                    });
                    
                    if(tipo === 'cliente') {
                        document.getElementById('info-cliente').classList.add('active');
                    } else {
                        document.getElementById('info-gerente').classList.add('active');
                    }
                    
                    // Mostrar/ocultar formulários
                    document.querySelectorAll('.formulario-container').forEach(form => {
                        form.classList.remove('active');
                    });
                    
                    document.getElementById('form-' + tipo).classList.add('active');
                    
                    // Atualizar hidden field em ambos os formulários
                    document.querySelectorAll('input[name="tipo_cadastro"]').forEach(input => {
                        input.value = tipo;
                    });
                });
            });
            
            // Se já houver um tipo selecionado (por GET), ativar
            const tipoAtual = '<?php echo $tipo_cadastro; ?>';
            if(tipoAtual) {
                const radio = document.querySelector(`input[value="${tipoAtual}"]`);
                if(radio) {
                    radio.checked = true;
                    radio.dispatchEvent(new Event('change'));
                }
            }
        });

        // Máscaras
        function aplicarMascaraCPF(input) {
            let value = input.value.replace(/\D/g, '');
            if(value.length > 3 && value.length <= 6) {
                value = value.replace(/(\d{3})(\d+)/, '$1.$2');
            } else if(value.length > 6 && value.length <= 9) {
                value = value.replace(/(\d{3})(\d{3})(\d+)/, '$1.$2.$3');
            } else if(value.length > 9) {
                value = value.replace(/(\d{3})(\d{3})(\d{3})(\d+)/, '$1.$2.$3-$4');
            }
            input.value = value.substring(0, 14);
        }

        function aplicarMascaraTelefone(input) {
            let value = input.value.replace(/\D/g, '');
            if(value.length > 0) {
                value = '(' + value;
                if(value.length > 3) {
                    value = value.substring(0, 3) + ') ' + value.substring(3);
                }
                if(value.length > 10) {
                    value = value.substring(0, 10) + '-' + value.substring(10);
                }
            }
            input.value = value.substring(0, 15);
        }

        function aplicarMascaraCEP(input) {
            let value = input.value.replace(/\D/g, '');
            if(value.length > 5) {
                value = value.substring(0, 5) + '-' + value.substring(5);
            }
            input.value = value.substring(0, 9);
        }

        function aplicarMascaraRG(input) {
            let value = input.value.replace(/\D/g, '');
            if(value.length > 2 && value.length <= 5) {
                value = value.replace(/(\d{2})(\d+)/, '$1.$2');
            } else if(value.length > 5 && value.length <= 8) {
                value = value.replace(/(\d{2})(\d{3})(\d+)/, '$1.$2.$3');
            } else if(value.length > 8) {
                value = value.replace(/(\d{2})(\d{3})(\d{3})(\d+)/, '$1.$2.$3-$4');
            }
            input.value = value.substring(0, 12);
        }

        function aplicarMascaraSalario(input) {
            let value = input.value.replace(/\D/g, '');
            value = (parseInt(value) / 100).toLocaleString('pt-BR', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
            input.value = value ? 'R$ ' + value : '';
        }

        // Aplicar máscaras aos campos
        function inicializarMascaras() {
            // Máscaras para formulário cliente
            const cpfCliente = document.getElementById('cpf');
            if(cpfCliente) cpfCliente.addEventListener('input', function(e) { aplicarMascaraCPF(e.target); });
            
            const telefoneCliente = document.getElementById('telefone');
            if(telefoneCliente) telefoneCliente.addEventListener('input', function(e) { aplicarMascaraTelefone(e.target); });
            
            const cepCliente = document.getElementById('cep');
            if(cepCliente) cepCliente.addEventListener('input', function(e) { aplicarMascaraCEP(e.target); });
            
            const rgCliente = document.getElementById('rg');
            if(rgCliente) rgCliente.addEventListener('input', function(e) { aplicarMascaraRG(e.target); });
            
            // Máscaras para formulário gerente
            const cpfGerente = document.getElementById('cpf_gerente');
            if(cpfGerente) cpfGerente.addEventListener('input', function(e) { aplicarMascaraCPF(e.target); });
            
            const telefoneGerente = document.getElementById('telefone_gerente');
            if(telefoneGerente) telefoneGerente.addEventListener('input', function(e) { aplicarMascaraTelefone(e.target); });
            
            const cepGerente = document.getElementById('cep_gerente');
            if(cepGerente) cepGerente.addEventListener('input', function(e) { aplicarMascaraCEP(e.target); });
            
            const salario = document.getElementById('salario');
            if(salario) salario.addEventListener('input', function(e) { aplicarMascaraSalario(e.target); });
        }

        // Inicializar máscaras quando DOM carregar
        document.addEventListener('DOMContentLoaded', inicializarMascaras);

        // Preview de imagens
        const fotoDocumento = document.getElementById('foto_documento');
        if(fotoDocumento) {
            fotoDocumento.addEventListener('change', function(e) {
                const preview = document.getElementById('preview-documento');
                const file = e.target.files[0];
                if(file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                }
            });
        }

        const selfieDocumento = document.getElementById('selfie_documento');
        if(selfieDocumento) {
            selfieDocumento.addEventListener('change', function(e) {
                const preview = document.getElementById('preview-selfie');
                const file = e.target.files[0];
                if(file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                }
            });
        }

        // Busca de CEP via API
        async function buscarCEP(cep, campoEndereco) {
            try {
                cep = cep.replace(/\D/g, '');
                if(cep.length !== 8) {
                    alert('CEP inválido!');
                    return;
                }
                
                const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                const data = await response.json();
                
                if(data.erro) {
                    alert('CEP não encontrado!');
                    return;
                }
                
                campoEndereco.value = `${data.logradouro}, ${data.bairro}, ${data.localidade} - ${data.uf}`;
            } catch(error) {
                console.error('Erro ao buscar CEP:', error);
                alert('Erro ao buscar CEP. Tente novamente.');
            }
        }

        // Configurar botões de busca CEP
        const btnBuscarCEP = document.getElementById('buscar-cep');
        if(btnBuscarCEP) {
            btnBuscarCEP.addEventListener('click', function() {
                const cep = document.getElementById('cep').value;
                const endereco = document.getElementById('endereco');
                buscarCEP(cep, endereco);
            });
        }

        const btnBuscarCEPGerente = document.getElementById('buscar-cep-gerente');
        if(btnBuscarCEPGerente) {
            btnBuscarCEPGerente.addEventListener('click', function() {
                const cep = document.getElementById('cep_gerente').value;
                const endereco = document.getElementById('endereco_gerente');
                buscarCEP(cep, endereco);
            });
        }

        // Validação de idade mínima (18 anos)
        const dataNascimento = document.getElementById('data_nascimento');
        if(dataNascimento) {
            dataNascimento.addEventListener('change', function() {
                const dataNasc = new Date(this.value);
                const hoje = new Date();
                const idade = hoje.getFullYear() - dataNasc.getFullYear();
                
                // Ajuste para mês/dia
                const mes = hoje.getMonth() - dataNasc.getMonth();
                if(mes < 0 || (mes === 0 && hoje.getDate() < dataNasc.getDate())) {
                    idade--;
                }
                
                if(idade < 18) {
                    alert('É necessário ter 18 anos ou mais para se cadastrar.');
                    this.value = '';
                }
            });
        }

        // Validações de formulário
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                // Verificar se é formulário de gerente
                const isGerenteForm = this.id === 'form-gerente';
                
                // Verificar se é admin para cadastrar gerente
                if(isGerenteForm) {
                    const isAdmin = <?php echo (isset($_SESSION['usuario_tipo']) && $_SESSION['usuario_tipo'] == 'admin') ? 'true' : 'false'; ?>;
                    
                    if(!isAdmin) {
                        e.preventDefault();
                        alert('Apenas administradores podem cadastrar gerentes.\n\nFaça login como administrador primeiro.');
                        window.location.href = 'login.php';
                        return false;
                    }
                }
                
                // Validação de senha
                const senhaField = isGerenteForm ? 'senha_gerente' : 'senha';
                const confirmarSenhaField = isGerenteForm ? 'confirmar_senha_gerente' : 'confirmar_senha';
                
                const senha = document.getElementById(senhaField).value;
                const confirmarSenha = document.getElementById(confirmarSenhaField).value;
                
                if(senha !== confirmarSenha) {
                    e.preventDefault();
                    alert('As senhas não coincidem!');
                    return false;
                }
                
                if(senha.length < 6) {
                    e.preventDefault();
                    alert('A senha deve ter no mínimo 6 caracteres!');
                    return false;
                }
                
                return true;
            });
        });
    </script>
</body>
</html>