<?php
// includes/header.php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Home - Sistema de Imóveis</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/estilo.css">
    <script src="../assets/js/scripts.js" defer></script>
</head>
<body>
    <!-- Botão Tema -->
    <div class="theme-toggle">
        <button id="themeToggle" aria-label="Alternar tema">
            <i class="fas fa-sun"></i>
            <i class="fas fa-moon"></i>
        </button>
    </div>

    <!-- Header Principal -->
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <!-- Logo -->
                <div class="logo">
                    <a href="../index.php">
                        <i class="fas fa-home"></i>
                        <span>New<span>Home</span></span>
                    </a>
                </div>

                <!-- Navegação Principal -->
                <nav class="main-nav">
                    <ul>
                        <li><a href="../index.php"><i class="fas fa-home"></i> Home</a></li>
                        <li><a href="../imoveis.php"><i class="fas fa-building"></i> Imóveis</a></li>
                        
                        <?php if(isset($_SESSION['usuario_id'])): ?>
                            <li><a href="../dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                            
                            <?php if($_SESSION['usuario_tipo'] == 'admin'): ?>
                                <li><a href="../admin/index.php"><i class="fas fa-cog"></i> Admin</a></li>
                            <?php endif; ?>
                            
                            <?php if($_SESSION['usuario_tipo'] == 'gerente'): ?>
                                <li><a href="../admin/imoveis.php"><i class="fas fa-home"></i> Gerenciar</a></li>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <li><a href="#"><i class="fas fa-info-circle"></i> Sobre</a></li>
                        <li><a href="#"><i class="fas fa-envelope"></i> Contato</a></li>
                    </ul>
                </nav>

                <!-- Área do Usuário -->
                <div class="user-area">
                    <?php if(isset($_SESSION['usuario_id'])): ?>
                        <div class="user-dropdown">
                            <button class="user-btn">
                                <div class="user-avatar">
                                    <?php echo strtoupper(substr($_SESSION['usuario_nome'], 0, 1)); ?>
                                </div>
                                <span><?php echo $_SESSION['usuario_nome']; ?></span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <div class="dropdown-menu">
                                <a href="../dashboard.php"><i class="fas fa-user"></i> Meu Perfil</a>
                                <a href="#"><i class="fas fa-cog"></i> Configurações</a>
                                <a href="../includes/logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="auth-buttons">
                            <a href="../login.php" class="btn btn-outline">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </a>
                            <a href="../cadastro.php" class="btn btn-primary">
                                <i class="fas fa-user-plus"></i> Cadastrar
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>