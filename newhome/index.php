<?php
// index.php - Página inicial pública
session_start();
require_once 'config/funcoes.php';

$funcoes = new Funcoes();

// Obter imóveis em destaque
$imoveis_destaque = $funcoes->listarImoveis(['destaque' => 1, 'status' => 'disponivel']);
$imoveis_destaque = array_slice($imoveis_destaque, 0, 3); // Limitar a 3 imóveis

// Obter estatísticas
$estatisticas = $funcoes->getEstatisticasImoveis();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Home - Sistema de Gerenciamento de Imóveis</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ===== VARIÁVEIS E RESET ===== */
        :root {
            /* Cores tema claro */
            --cor-fundo: #F8F6F3;
            --cor-secundaria: #E6E2F0;
            --cor-terciaria: #F0B429;
            --cor-texto: #333333;
            --cor-texto-secundario: #666666;
            --cor-borda: #DDDDDD;
            --cor-card: #FFFFFF;
            --sombra: rgba(0, 0, 0, 0.05);
            --sombra-forte: rgba(0, 0, 0, 0.1);
        }

        .dark-theme {
            /* Cores tema escuro */
            --cor-fundo: #0B0F1A;
            --cor-secundaria: #1A1E2E;
            --cor-terciaria: #F0B429;
            --cor-texto: #F8F6F3;
            --cor-texto-secundario: #CCCCCC;
            --cor-borda: #2A2E3E;
            --cor-card: #1A1E2E;
            --sombra: rgba(0, 0, 0, 0.2);
            --sombra-forte: rgba(0, 0, 0, 0.4);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--cor-fundo);
            color: var(--cor-texto);
            transition: all 0.3s ease;
            line-height: 1.6;
        }

        /* ===== LAYOUT ===== */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        section {
            padding: 80px 0;
        }

        /* ===== BOTÕES ===== */
        .btn {
            padding: 14px 28px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .btn-primary {
            background-color: var(--cor-terciaria);
            color: #000;
        }

        .btn-primary:hover {
            background-color: #e0a010;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px var(--sombra-forte);
        }

        .btn-secondary {
            background-color: transparent;
            color: var(--cor-terciaria);
            border: 2px solid var(--cor-terciaria);
        }

        .btn-secondary:hover {
            background-color: var(--cor-terciaria);
            color: #000;
        }

        .btn-large {
            padding: 16px 32px;
            font-size: 18px;
        }

        /* ===== HEADER ===== */
        .main-header {
            background-color: var(--cor-secundaria);
            padding: 15px 0;
            box-shadow: 0 4px 12px var(--sombra);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .logo-icon {
            color: var(--cor-terciaria);
            font-size: 32px;
        }

        .logo-text {
            font-size: 28px;
            font-weight: 700;
            color: var(--cor-texto);
        }

        .logo-text span {
            color: var(--cor-terciaria);
        }

        .main-nav ul {
            display: flex;
            list-style: none;
            gap: 30px;
        }

        .main-nav a {
            text-decoration: none;
            color: var(--cor-texto);
            font-weight: 600;
            font-size: 16px;
            transition: color 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .main-nav a:hover {
            color: var(--cor-terciaria);
        }

        .user-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        /* Botão tema */
        .theme-toggle {
            display: flex;
            align-items: center;
        }

        .theme-btn {
            background-color: var(--cor-terciaria);
            border: none;
            width: 50px;
            height: 26px;
            border-radius: 13px;
            position: relative;
            cursor: pointer;
            display: flex;
            align-items: center;
            padding: 0 3px;
            transition: all 0.3s;
        }

        .theme-btn::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            background-color: var(--cor-fundo);
            border-radius: 50%;
            transition: all 0.3s;
            left: 3px;
        }

        .dark-theme .theme-btn::after {
            transform: translateX(24px);
        }

        .theme-icon {
            font-size: 12px;
            color: var(--cor-texto);
            z-index: 1;
        }

        .theme-icon.sun {
            margin-right: auto;
        }

        .theme-icon.moon {
            margin-left: auto;
        }

        /* ===== HERO SECTION ===== */
        .hero {
            background: linear-gradient(135deg, var(--cor-secundaria) 0%, var(--cor-fundo) 100%);
            padding: 120px 0;
            position: relative;
            overflow: hidden;
        }

        .hero-content {
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }

        .hero-title {
            font-size: 56px;
            line-height: 1.2;
            margin-bottom: 20px;
            color: var(--cor-texto);
        }

        .hero-title span {
            color: var(--cor-terciaria);
        }

        .hero-subtitle {
            font-size: 20px;
            color: var(--cor-texto-secundario);
            margin-bottom: 40px;
            line-height: 1.6;
        }

        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 60px;
            flex-wrap: wrap;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 36px;
            font-weight: 700;
            color: var(--cor-terciaria);
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 14px;
            color: var(--cor-texto-secundario);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* ===== FEATURES SECTION ===== */
        .features {
            background-color: var(--cor-fundo);
        }

        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-title {
            font-size: 42px;
            margin-bottom: 15px;
            color: var(--cor-texto);
        }

        .section-subtitle {
            font-size: 18px;
            color: var(--cor-texto-secundario);
            max-width: 700px;
            margin: 0 auto;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 40px;
        }

        .feature-card {
            background-color: var(--cor-card);
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 8px 24px var(--sombra);
            transition: all 0.3s;
            border: 1px solid var(--cor-borda);
            text-align: center;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 16px 40px var(--sombra-forte);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background-color: var(--cor-terciaria);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 32px;
            color: #000;
        }

        .feature-title {
            font-size: 24px;
            margin-bottom: 15px;
            color: var(--cor-texto);
        }

        .feature-description {
            color: var(--cor-texto-secundario);
            line-height: 1.6;
        }

        /* ===== PROPERTIES SECTION ===== */
        .properties {
            background-color: var(--cor-secundaria);
        }

        .properties-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 50px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .properties-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
        }

        .property-card {
            background-color: var(--cor-card);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 24px var(--sombra);
            transition: all 0.3s;
        }

        .property-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 16px 40px var(--sombra-forte);
        }

        .property-image {
            height: 220px;
            width: 100%;
            background-color: var(--cor-terciaria);
            position: relative;
            overflow: hidden;
        }

        .property-tag {
            position: absolute;
            top: 20px;
            right: 20px;
            background-color: var(--cor-terciaria);
            color: #000;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .property-featured {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: rgba(0,0,0,0.7);
            color: var(--cor-terciaria);
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
        }

        .property-content {
            padding: 30px;
        }

        .property-price {
            font-size: 28px;
            font-weight: 700;
            color: var(--cor-terciaria);
            margin-bottom: 10px;
        }

        .property-title {
            font-size: 22px;
            margin-bottom: 10px;
            color: var(--cor-texto);
        }

        .property-location {
            color: var(--cor-texto-secundario);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .property-features {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            border-top: 1px solid var(--cor-borda);
            padding-top: 20px;
            margin-top: 20px;
        }

        .property-feature {
            text-align: center;
        }

        .property-feature i {
            color: var(--cor-terciaria);
            font-size: 18px;
            margin-bottom: 5px;
        }

        .property-feature span {
            font-size: 14px;
            color: var(--cor-texto-secundario);
        }

        /* ===== USER TYPES SECTION ===== */
        .user-types {
            background-color: var(--cor-fundo);
        }

        .types-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
        }

        .type-card {
            background-color: var(--cor-card);
            border-radius: 16px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 8px 24px var(--sombra);
            transition: all 0.3s;
            border: 2px solid transparent;
        }

        .type-card:hover {
            border-color: var(--cor-terciaria);
            transform: translateY(-5px);
        }

        .type-icon {
            width: 80px;
            height: 80px;
            background-color: var(--cor-terciaria);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            font-size: 32px;
            color: #000;
        }

        .type-title {
            font-size: 24px;
            margin-bottom: 15px;
            color: var(--cor-texto);
        }

        .type-list {
            list-style: none;
            text-align: left;
            margin-bottom: 25px;
        }

        .type-list li {
            margin-bottom: 10px;
            color: var(--cor-texto-secundario);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .type-list i {
            color: var(--cor-terciaria);
        }

        /* ===== CTA SECTION ===== */
        .cta-section {
            background: linear-gradient(135deg, var(--cor-terciaria) 0%, #e0a010 100%);
            text-align: center;
            padding: 100px 0;
        }

        .cta-content {
            max-width: 700px;
            margin: 0 auto;
        }

        .cta-title {
            font-size: 42px;
            margin-bottom: 20px;
            color: #000;
        }

        .cta-text {
            font-size: 18px;
            margin-bottom: 40px;
            color: rgba(0,0,0,0.8);
            line-height: 1.6;
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .cta-buttons .btn {
            min-width: 200px;
        }

        .cta-buttons .btn-primary {
            background-color: #000;
            color: var(--cor-terciaria);
        }

        .cta-buttons .btn-primary:hover {
            background-color: #333;
        }

        .cta-buttons .btn-secondary {
            background-color: transparent;
            color: #000;
            border: 2px solid #000;
        }

        .cta-buttons .btn-secondary:hover {
            background-color: #000;
            color: var(--cor-terciaria);
        }

        /* ===== FOOTER ===== */
        .main-footer {
            background-color: var(--cor-secundaria);
            padding: 80px 0 30px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 50px;
            margin-bottom: 50px;
        }

        .footer-col h3 {
            font-size: 20px;
            margin-bottom: 25px;
            color: var(--cor-texto);
        }

        .footer-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            font-size: 28px;
            font-weight: 700;
            color: var(--cor-texto);
        }

        .footer-logo span {
            color: var(--cor-terciaria);
        }

        .footer-description {
            color: var(--cor-texto-secundario);
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .social-links {
            display: flex;
            gap: 15px;
        }

        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: var(--cor-fundo);
            color: var(--cor-texto);
            border-radius: 50%;
            text-decoration: none;
            transition: all 0.3s;
        }

        .social-links a:hover {
            background-color: var(--cor-terciaria);
            color: #000;
            transform: translateY(-3px);
        }

        .footer-links {
            list-style: none;
        }

        .footer-links li {
            margin-bottom: 15px;
        }

        .footer-links a {
            color: var(--cor-texto-secundario);
            text-decoration: none;
            transition: color 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .footer-links a:hover {
            color: var(--cor-terciaria);
        }

        .contact-info {
            list-style: none;
        }

        .contact-info li {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 20px;
            color: var(--cor-texto-secundario);
        }

        .contact-info i {
            color: var(--cor-terciaria);
            margin-top: 3px;
            min-width: 20px;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid var(--cor-borda);
            color: var(--cor-texto-secundario);
            font-size: 14px;
        }

        /* ===== RESPONSIVIDADE ===== */
        @media (max-width: 992px) {
            .hero-title {
                font-size: 42px;
            }
            
            .section-title {
                font-size: 36px;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
            }
            
            .main-nav ul {
                gap: 20px;
            }
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 20px;
            }
            
            .main-nav ul {
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .hero {
                padding: 80px 0;
            }
            
            .hero-title {
                font-size: 32px;
            }
            
            .hero-stats {
                gap: 20px;
            }
            
            .stat-number {
                font-size: 28px;
            }
            
            .section-title {
                font-size: 28px;
            }
            
            .properties-header {
                flex-direction: column;
                text-align: center;
            }
            
            .properties-grid {
                grid-template-columns: 1fr;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .cta-buttons .btn {
                width: 100%;
                max-width: 300px;
            }
        }

        @media (max-width: 480px) {
            .hero-title {
                font-size: 28px;
            }
            
            .hero-subtitle {
                font-size: 16px;
            }
            
            .feature-card,
            .type-card {
                padding: 25px;
            }
            
            .btn {
                padding: 12px 24px;
                font-size: 14px;
            }
        }

        /* ===== ANIMAÇÕES ===== */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate {
            animation: fadeIn 0.8s ease-out;
        }

        /* ===== STATUS DO USUÁRIO ===== */
        .user-status {
            margin-top: 30px;
            padding: 20px;
            background-color: var(--cor-card);
            border-radius: 12px;
            box-shadow: 0 4px 12px var(--sombra);
            display: inline-block;
        }

        .user-status p {
            margin-bottom: 10px;
        }

        .user-type-badge {
            background-color: var(--cor-terciaria);
            color: #000;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <!-- ===== HEADER ===== -->
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <!-- Logo -->
                <a href="index.php" class="logo">
                    <i class="fas fa-home logo-icon"></i>
                    <div class="logo-text">New<span>Home</span></div>
                </a>

                <!-- Navegação -->
                <nav class="main-nav">
                    <ul>
                        <li><a href="#home"><i class="fas fa-home"></i> Início</a></li>
                        <li><a href="#features"><i class="fas fa-star"></i> Recursos</a></li>
                        <li><a href="#properties"><i class="fas fa-building"></i> Imóveis</a></li>
                        <li><a href="#types"><i class="fas fa-users"></i> Para Quem</a></li>
                        <li><a href="#contact"><i class="fas fa-envelope"></i> Contato</a></li>
                    </ul>
                </nav>

                <!-- Ações do Usuário -->
                <div class="user-actions">
                    <!-- Botão Tema -->
                    <div class="theme-toggle">
                        <button class="theme-btn" id="themeToggle" aria-label="Alternar tema">
                            <i class="fas fa-sun theme-icon sun"></i>
                            <i class="fas fa-moon theme-icon moon"></i>
                        </button>
                    </div>

                    <!-- Botões de Login/Cadastro -->
                    <?php if(isset($_SESSION['usuario_id'])): ?>
                        <div class="user-status">
                            <p>Olá, <strong><?php echo htmlspecialchars($_SESSION['usuario_nome']); ?></strong>!</p>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <a href="dashboard.php" class="btn btn-primary">
                                    <i class="fas fa-tachometer-alt"></i> Dashboard
                                </a>
                                <a href="includes/logout.php" class="btn btn-secondary">
                                    <i class="fas fa-sign-out-alt"></i> Sair
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div style="display: flex; gap: 10px;">
                            <a href="login.php" class="btn btn-secondary">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </a>
                            <a href="cadastro.php" class="btn btn-primary">
                                <i class="fas fa-user-plus"></i> Cadastrar
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- ===== HERO SECTION ===== -->
    <section id="home" class="hero">
        <div class="container">
            <div class="hero-content animate">
                <h1 class="hero-title">
                    Encontre e Gerencie seu <span>Imóvel Ideal</span>
                </h1>
                <p class="hero-subtitle">
                    Plataforma completa para compra, venda e aluguel de imóveis. 
                    Conectamos proprietários, corretores e clientes em um só lugar.
                </p>
                
                <div style="display: flex; gap: 20px; justify-content: center; margin-top: 40px; flex-wrap: wrap;">
                    <a href="cadastro.php" class="btn btn-primary btn-large">
                        <i class="fas fa-rocket"></i> Começar Agora
                    </a>
                    <a href="#features" class="btn btn-secondary btn-large">
                        <i class="fas fa-play-circle"></i> Ver Demonstração
                    </a>
                </div>

                <!-- Estatísticas -->
                <div class="hero-stats">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo $estatisticas['total'] ?? '0'; ?></div>
                        <div class="stat-label">Imóveis Cadastrados</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo $estatisticas['disponiveis'] ?? '0'; ?></div>
                        <div class="stat-label">Disponíveis</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo number_format($estatisticas['media_valor'] ?? 0, 0, ',', '.'); ?></div>
                        <div class="stat-label">Valor Médio (R$)</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Suporte</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== FEATURES SECTION ===== -->
    <section id="features" class="features">
        <div class="container">
            <div class="section-header animate">
                <h2 class="section-title">Recursos Exclusivos</h2>
                <p class="section-subtitle">
                    Tudo que você precisa para gerenciar imóveis com eficiência e praticidade
                </p>
            </div>

            <div class="features-grid">
                <div class="feature-card animate">
                    <div class="feature-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3 class="feature-title">Busca Inteligente</h3>
                    <p class="feature-description">
                        Encontre imóveis com filtros avançados por localização, preço, tamanho e muito mais.
                    </p>
                </div>

                <div class="feature-card animate">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="feature-title">Dashboard Completo</h3>
                    <p class="feature-description">
                        Acompanhe estatísticas, finanças e desempenho dos seus imóveis em tempo real.
                    </p>
                </div>

                <div class="feature-card animate">
                    <div class="feature-icon">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <h3 class="feature-title">Gestão de Contratos</h3>
                    <p class="feature-description">
                        Crie, gerencie e renove contratos automaticamente com alertas de vencimento.
                    </p>
                </div>

                <div class="feature-card animate">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3 class="feature-title">Agendamentos</h3>
                    <p class="feature-description">
                        Agende visitas e reuniões diretamente pela plataforma com confirmação automática.
                    </p>
                </div>

                <div class="feature-card animate">
                    <div class="feature-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3 class="feature-title">Chat Integrado</h3>
                    <p class="feature-description">
                        Comunique-se diretamente com proprietários, corretores e clientes.
                    </p>
                </div>

                <div class="feature-card animate">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3 class="feature-title">Totalmente Responsivo</h3>
                    <p class="feature-description">
                        Acesse de qualquer dispositivo: computador, tablet ou smartphone.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== PROPERTIES SECTION ===== -->
    <section id="properties" class="properties">
        <div class="container">
            <div class="properties-header animate">
                <div>
                    <h2 class="section-title">Imóveis em Destaque</h2>
                    <p class="section-subtitle" style="text-align: left; margin: 10px 0 0 0;">
                        Confira algumas de nossas melhores oportunidades
                    </p>
                </div>
                <a href="imoveis.php" class="btn btn-primary">
                    <i class="fas fa-eye"></i> Ver Todos os Imóveis
                </a>
            </div>

            <?php if(count($imoveis_destaque) > 0): ?>
            <div class="properties-grid">
                <?php foreach($imoveis_destaque as $imovel): ?>
                <div class="property-card animate">
                    <div class="property-image">
                        <div class="property-tag"><?php echo ucfirst($imovel['tipo']); ?></div>
                        <div class="property-featured">
                            <i class="fas fa-star"></i> Destaque
                        </div>
                    </div>
                    <div class="property-content">
                        <div class="property-price">
                            R$ <?php echo number_format($imovel['valor'], 2, ',', '.'); ?>
                        </div>
                        <h3 class="property-title"><?php echo htmlspecialchars($imovel['titulo']); ?></h3>
                        <div class="property-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo htmlspecialchars($imovel['cidade']); ?>, <?php echo htmlspecialchars($imovel['estado']); ?>
                        </div>
                        <div class="property-features">
                            <div class="property-feature">
                                <i class="fas fa-bed"></i>
                                <span><?php echo $imovel['quartos']; ?> Quartos</span>
                            </div>
                            <div class="property-feature">
                                <i class="fas fa-bath"></i>
                                <span><?php echo $imovel['banheiros']; ?> Banheiros</span>
                            </div>
                            <div class="property-feature">
                                <i class="fas fa-car"></i>
                                <span><?php echo $imovel['vagas']; ?> Vagas</span>
                            </div>
                        </div>
                        <a href="#" class="btn btn-secondary" style="width: 100%; margin-top: 20px;">
                            <i class="fas fa-info-circle"></i> Ver Detalhes
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="alert" style="text-align: center; padding: 40px; background-color: var(--cor-card); border-radius: 12px;">
                <i class="fas fa-home" style="font-size: 48px; color: var(--cor-terciaria); margin-bottom: 20px;"></i>
                <h3>Nenhum imóvel em destaque no momento</h3>
                <p style="margin: 15px 0;">Volte em breve para conferir nossas oportunidades!</p>
                <a href="cadastro.php" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Cadastre-se para ser notificado
                </a>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- ===== USER TYPES SECTION ===== -->
    <section id="types" class="user-types">
        <div class="container">
            <div class="section-header animate">
                <h2 class="section-title">Para Todos os Perfis</h2>
                <p class="section-subtitle">
                    Oferecemos soluções específicas para cada tipo de usuário
                </p>
            </div>

            <div class="types-grid">
                <div class="type-card animate">
                    <div class="type-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3 class="type-title">Para Clientes</h3>
                    <ul class="type-list">
                        <li><i class="fas fa-check"></i> Encontre o imóvel ideal</li>
                        <li><i class="fas fa-check"></i> Agende visitas online</li>
                        <li><i class="fas fa-check"></i> Acompanhe contratos</li>
                        <li><i class="fas fa-check"></i> Receba alertas personalizados</li>
                        <li><i class="fas fa-check"></i> Chat direto com corretores</li>
                    </ul>
                    <a href="cadastro.php?tipo=cliente" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Cadastrar como Cliente
                    </a>
                </div>

                <div class="type-card animate">
                    <div class="type-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <h3 class="type-title">Para Gerentes</h3>
                    <ul class="type-list">
                        <li><i class="fas fa-check"></i> Gerencie múltiplos imóveis</li>
                        <li><i class="fas fa-check"></i> Controle de contratos</li>
                        <li><i class="fas fa-check"></i> Relatórios financeiros</li>
                        <li><i class="fas fa-check"></i> Gestão de manutenções</li>
                        <li><i class="fas fa-check"></i> Comunicação com clientes</li>
                    </ul>
                    <a href="cadastro.php?tipo=gerente" class="btn btn-primary">
                        <i class="fas fa-briefcase"></i> Cadastrar como Gerente
                    </a>
                </div>

                <div class="type-card animate">
                    <div class="type-icon">
                        <i class="fas fa-cog"></i>
                    </div>
                    <h3 class="type-title">Para Administradores</h3>
                    <ul class="type-list">
                        <li><i class="fas fa-check"></i> Controle total do sistema</li>
                        <li><i class="fas fa-check"></i> Gerenciamento de usuários</li>
                        <li><i class="fas fa-check"></i> Relatórios avançados</li>
                        <li><i class="fas fa-check"></i> Configurações do sistema</li>
                        <li><i class="fas fa-check"></i> Suporte técnico</li>
                    </ul>
                    <a href="login.php" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Acessar Painel Admin
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== CTA SECTION ===== -->
    <section class="cta-section">
        <div class="container">
            <div class="cta-content animate">
                <h2 class="cta-title">Pronto para Transformar sua Experiência Imobiliária?</h2>
                <p class="cta-text">
                    Junte-se a milhares de usuários que já descobriram a maneira mais fácil 
                    e eficiente de gerenciar imóveis. Experimente grátis por 30 dias.
                </p>
                <div class="cta-buttons">
                    <a href="cadastro.php" class="btn btn-primary btn-large">
                        <i class="fas fa-rocket"></i> Começar Gratuitamente
                    </a>
                    <a href="#contact" class="btn btn-secondary btn-large">
                        <i class="fas fa-phone"></i> Falar com Especialista
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ===== FOOTER ===== -->
    <footer id="contact" class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-col">
                    <div class="footer-logo">
                        <i class="fas fa-home"></i> New<span>Home</span>
                    </div>
                    <p class="footer-description">
                        Sistema completo para gerenciamento de imóveis. 
                        Conectamos proprietários, corretores e clientes em uma plataforma segura e intuitiva.
                    </p>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
                        <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>

                <div class="footer-col">
                    <h3>Links Rápidos</h3>
                    <ul class="footer-links">
                        <li><a href="index.php"><i class="fas fa-chevron-right"></i> Home</a></li>
                        <li><a href="#features"><i class="fas fa-chevron-right"></i> Recursos</a></li>
                        <li><a href="imoveis.php"><i class="fas fa-chevron-right"></i> Imóveis</a></li>
                        <li><a href="#types"><i class="fas fa-chevron-right"></i> Para Quem</a></li>
                        <li><a href="login.php"><i class="fas fa-chevron-right"></i> Login</a></li>
                        <li><a href="cadastro.php"><i class="fas fa-chevron-right"></i> Cadastro</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h3>Recursos</h3>
                    <ul class="footer-links">
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Blog</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> FAQ</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Tutoriais</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> API</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i Status do Sistema</a></li>
                        <li><a href="#"><i class="fas fa-chevron-right"></i> Central de Ajuda</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h3>Contato</h3>
                    <ul class="contact-info">
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <div>
                                <strong>Endereço</strong><br>
                                Av. Paulista, 1000<br>
                                São Paulo - SP, 01310-100
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-phone"></i>
                            <div>
                                <strong>Telefone</strong><br>
                                (11) 9999-9999
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <div>
                                <strong>Email</strong><br>
                                contato@newhome.com
                            </div>
                        </li>
                        <li>
                            <i class="fas fa-clock"></i>
                            <div>
                                <strong>Horário</strong><br>
                                Seg-Sex: 9h-18h<br>
                                Sáb: 9h-13h
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> New Home - Sistema de Gerenciamento de Imóveis. Todos os direitos reservados.</p>
                <p style="margin-top: 10px; font-size: 12px;">
                    <a href="#" style="color: var(--cor-texto-secundario);">Política de Privacidade</a> | 
                    <a href="#" style="color: var(--cor-texto-secundario);">Termos de Uso</a> | 
                    <a href="#" style="color: var(--cor-texto-secundario);">Cookies</a>
                </p>
            </div>
        </div>
    </footer>

    <!-- ===== SCRIPTS ===== -->
    <script>
        // Tema claro/escuro
        const themeToggle = document.getElementById('themeToggle');
        const body = document.body;
        
        // Verificar preferência salva
        const savedTheme = localStorage.getItem('theme') || 'light';
        if (savedTheme === 'dark') {
            body.classList.add('dark-theme');
        }
        
        themeToggle.addEventListener('click', () => {
            body.classList.toggle('dark-theme');
            const currentTheme = body.classList.contains('dark-theme') ? 'dark' : 'light';
            localStorage.setItem('theme', currentTheme);
            
            // Atualizar ícone
            const icon = themeToggle.querySelector('.theme-icon');
            if(currentTheme === 'dark') {
                themeToggle.querySelector('.sun').style.display = 'none';
                themeToggle.querySelector('.moon').style.display = 'block';
            } else {
                themeToggle.querySelector('.sun').style.display = 'block';
                themeToggle.querySelector('.moon').style.display = 'none';
            }
        });

        // Rolagem suave para links âncora
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if(targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if(targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Animações ao rolar
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if(entry.isIntersecting) {
                    entry.target.classList.add('animate');
                }
            });
        }, observerOptions);

        // Observar elementos para animação
        document.querySelectorAll('.animate').forEach(el => {
            observer.observe(el);
        });

        // Contador animado para estatísticas
        function animateCounter(element, target, duration = 2000) {
            let start = 0;
            const increment = target / (duration / 16);
            const timer = setInterval(() => {
                start += increment;
                if(start >= target) {
                    element.textContent = target;
                    clearInterval(timer);
                } else {
                    element.textContent = Math.floor(start);
                }
            }, 16);
        }

        // Iniciar contadores quando visíveis
        const statObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if(entry.isIntersecting) {
                    const statNumber = entry.target.querySelector('.stat-number');
                    if(statNumber) {
                        const target = parseInt(statNumber.textContent);
                        if(!isNaN(target)) {
                            animateCounter(statNumber, target);
                        }
                    }
                    statObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        // Observar estatísticas
        document.querySelectorAll('.stat-item').forEach(item => {
            statObserver.observe(item);
        });

        // Formatação de números
        function formatNumber(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }

        // Atualizar números formatados
        document.querySelectorAll('.stat-number').forEach(el => {
            const num = parseInt(el.textContent);
            if(!isNaN(num) && num > 999) {
                el.textContent = formatNumber(num);
            }
        });
    </script>
</body>
</html>