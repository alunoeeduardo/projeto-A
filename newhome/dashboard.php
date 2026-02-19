<?php
// dashboard.php
require_once 'includes/auth.php';
require_once 'config/funcoes.php';

verificarLogin();
$funcoes = new Funcoes();

// Obter dados do usuário
$usuario = $funcoes->getUsuario($_SESSION['usuario_id']);

// Listar imóveis disponíveis
$imoveis = $funcoes->listarImoveis(['status' => 'disponivel']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Home - Dashboard</title>
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
            --cor-borda: #E6E2F0;
            --sombra: rgba(0, 0, 0, 0.1);
        }

        [data-tema="escuro"] {
            /* Tema Escuro */
            --cor-fundo: #121212;
            --cor-secundaria: #1E1E1E;
            --cor-terciaria: #F0B429;
            --cor-texto: #E0E0E0;
            --cor-card: #1E1E1E;
            --cor-borda: #333333;
            --sombra: rgba(0, 0, 0, 0.3);
        }

        body {
            background-color: var(--cor-fundo);
            color: var(--cor-texto);
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
            box-shadow: 0 2px 10px var(--sombra);
        }

        .theme-toggle button:hover {
            transform: rotate(30deg);
            box-shadow: 0 4px 15px var(--sombra);
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

        .header {
            background-color: var(--cor-secundaria);
            padding: 20px 0;
            box-shadow: 0 4px 12px var(--sombra);
            border-bottom: 1px solid var(--cor-borda);
            transition: all 0.3s;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
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
        }

        .logo h1 {
            font-size: 28px;
            color: var(--cor-terciaria);
        }

        .user-menu {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background-color: var(--cor-terciaria);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #000;
            font-weight: bold;
        }

        .btn-logout {
            background-color: var(--cor-terciaria);
            color: #000;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-logout:hover {
            background-color: #e0a010;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(240, 180, 41, 0.3);
        }

        .dashboard-content {
            padding: 40px 0;
        }

        .welcome-section {
            background-color: var(--cor-card);
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px var(--sombra);
            border: 1px solid var(--cor-borda);
            transition: all 0.3s;
        }

        .welcome-section h2 {
            color: var(--cor-terciaria);
            margin-bottom: 10px;
        }

        .user-type {
            display: inline-block;
            background-color: var(--cor-terciaria);
            color: #000;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background-color: var(--cor-card);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 5px 15px var(--sombra);
            display: flex;
            align-items: center;
            gap: 20px;
            border: 1px solid var(--cor-borda);
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px var(--sombra);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            background-color: var(--cor-terciaria);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #000;
        }

        .stat-info h3 {
            font-size: 32px;
            color: var(--cor-terciaria);
            margin-bottom: 5px;
        }

        .stat-info p {
            color: var(--cor-texto);
            opacity: 0.7;
        }

        .section-title {
            font-size: 24px;
            margin-bottom: 20px;
            color: var(--cor-texto);
        }

        .imoveis-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .imovel-card {
            background-color: var(--cor-card);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 15px var(--sombra);
            transition: all 0.3s;
            border: 1px solid var(--cor-borda);
        }

        .imovel-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px var(--sombra);
        }

        .imovel-image {
            height: 200px;
            background-color: var(--cor-terciaria);
            position: relative;
        }

        .imovel-tag {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: var(--cor-terciaria);
            color: #000;
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 14px;
        }

        .imovel-content {
            padding: 20px;
        }

        .imovel-price {
            font-size: 24px;
            font-weight: 700;
            color: var(--cor-terciaria);
            margin-bottom: 10px;
        }

        .imovel-title {
            font-size: 18px;
            margin-bottom: 10px;
            color: var(--cor-texto);
        }

        .imovel-location {
            color: var(--cor-texto);
            opacity: 0.7;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .imovel-features {
            display: flex;
            justify-content: space-between;
            border-top: 1px solid var(--cor-borda);
            padding-top: 15px;
            margin-top: 15px;
        }

        .imovel-feature {
            text-align: center;
            color: var(--cor-texto);
        }

        .imovel-feature i {
            color: var(--cor-terciaria);
            margin-bottom: 5px;
        }

        .btn-agendar {
            width: 100%;
            padding: 12px;
            background-color: var(--cor-terciaria);
            color: #000;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 15px;
        }

        .btn-agendar:hover {
            background-color: #e0a010;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(240, 180, 41, 0.3);
        }

        /* Menu de navegação */
        .nav-menu {
            background-color: var(--cor-card);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px var(--sombra);
            border: 1px solid var(--cor-borda);
        }

        .nav-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .nav-item {
            background-color: var(--cor-secundaria);
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            text-decoration: none;
            color: var(--cor-texto);
            transition: all 0.3s;
            border: 1px solid var(--cor-borda);
        }

        .nav-item:hover {
            background-color: var(--cor-terciaria);
            color: #000;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(240, 180, 41, 0.2);
        }

        .nav-item i {
            font-size: 24px;
            margin-bottom: 10px;
            display: block;
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }
            
            .user-menu {
                width: 100%;
                justify-content: space-between;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .imoveis-grid {
                grid-template-columns: 1fr;
            }
            
            .theme-toggle {
                top: 10px;
                right: 10px;
            }
            
            .theme-toggle button {
                width: 40px;
                height: 40px;
            }
        }

        /* Animações */
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

        .welcome-section,
        .stat-card,
        .imovel-card,
        .nav-menu {
            animation: fadeIn 0.5s ease-out;
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

    <div class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <i class="fas fa-home"></i>
                    <h1><a href="index.php">NewHome</a></h1>
                </div>
                
                <div class="user-menu">
                    <div class="user-info">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($usuario['nome'], 0, 1)); ?>
                        </div>
                        <div>
                            <strong><?php echo htmlspecialchars($usuario['nome']); ?></strong><br>
                            <small><?php echo $usuario['email']; ?></small>
                        </div>
                    </div>
                    
                    <form method="POST" action="includes/logout.php">
                        <button type="submit" class="btn-logout">
                            <i class="fas fa-sign-out-alt"></i> Sair
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container">
        <div class="dashboard-content">
            <div class="welcome-section">
                <h2>Bem-vindo, <?php echo htmlspecialchars($usuario['nome']); ?>!</h2>
                <div class="user-type">
                    <?php 
                    $tipo_texto = [
                        'cliente' => 'Cliente',
                        'gerente' => 'Gerente de Imóveis',
                        'admin' => 'Administrador'
                    ];
                    echo $tipo_texto[$usuario['tipo']];
                    ?>
                </div>
                <p>Use este painel para gerenciar suas atividades.</p>
            </div>

            <!-- Menu de Navegação -->
            <div class="nav-menu">
                <div class="nav-grid">
                    <?php if($usuario['tipo'] == 'cliente'): ?>
                        <a href="#imoveis" class="nav-item">
                            <i class="fas fa-home"></i>
                            <span>Imóveis Disponíveis</span>
                        </a>
                        <a href="agendamentos.php" class="nav-item">
                            <i class="fas fa-calendar-check"></i>
                            <span>Meus Agendamentos</span>
                        </a>
                        <a href="contratos.php" class="nav-item">
                            <i class="fas fa-file-contract"></i>
                            <span>Meus Contratos</span>
                        </a>
                        <a href="perfil.php" class="nav-item">
                            <i class="fas fa-user"></i>
                            <span>Meu Perfil</span>
                        </a>
                    <?php endif; ?>
                    
                    <?php if($usuario['tipo'] == 'gerente'): ?>
                        <a href="admin/imoveis.php" class="nav-item">
                            <i class="fas fa-home"></i>
                            <span>Gerenciar Imóveis</span>
                        </a>
                        <a href="admin/agendamentos.php" class="nav-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Agendamentos</span>
                        </a>
                        <a href="admin/contratos.php" class="nav-item">
                            <i class="fas fa-file-signature"></i>
                            <span>Contratos</span>
                        </a>
                        <a href="admin/clientes.php" class="nav-item">
                            <i class="fas fa-users"></i>
                            <span>Clientes</span>
                        </a>
                    <?php endif; ?>
                    
                    <?php if($usuario['tipo'] == 'admin'): ?>
                        <a href="admin/index.php" class="nav-item">
                            <i class="fas fa-cog"></i>
                            <span>Painel Admin</span>
                        </a>
                        <a href="admin/usuarios.php" class="nav-item">
                            <i class="fas fa-users-cog"></i>
                            <span>Gerenciar Usuários</span>
                        </a>
                        <a href="admin/relatorios.php" class="nav-item">
                            <i class="fas fa-chart-bar"></i>
                            <span>Relatórios</span>
                        </a>
                        <a href="admin/configuracoes.php" class="nav-item">
                            <i class="fas fa-sliders-h"></i>
                            <span>Configurações</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if($usuario['tipo'] == 'cliente'): ?>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo count($imoveis); ?></h3>
                        <p>Imóveis Disponíveis</p>
                    </div>
                </div>
                
                <?php 
                $agendamentos = $funcoes->listarAgendamentosUsuario($_SESSION['usuario_id']);
                $agendamentos_ativos = array_filter($agendamentos, function($a) {
                    return $a['status'] == 'pendente' || $a['status'] == 'confirmado';
                });
                ?>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo count($agendamentos_ativos); ?></h3>
                        <p>Visitas Agendadas</p>
                    </div>
                </div>
                
                <?php 
                $contratos = $funcoes->listarContratosUsuario($_SESSION['usuario_id'], 'cliente');
                $contratos_ativos = array_filter($contratos, function($c) {
                    return $c['status'] == 'ativo';
                });
                ?>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-file-contract"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?php echo count($contratos_ativos); ?></h3>
                        <p>Contratos Ativos</p>
                    </div>
                </div>
            </div>
            
            <h2 class="section-title">Imóveis Disponíveis</h2>
            <div class="imoveis-grid">
                <?php foreach($imoveis as $imovel): ?>
                <div class="imovel-card">
                    <div class="imovel-image">
                        <div class="imovel-tag"><?php echo ucfirst($imovel['tipo']); ?></div>
                    </div>
                    <div class="imovel-content">
                        <div class="imovel-price">R$ <?php echo number_format($imovel['valor'], 2, ',', '.'); ?></div>
                        <h3 class="imovel-title"><?php echo htmlspecialchars($imovel['titulo']); ?></h3>
                        <div class="imovel-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <?php echo htmlspecialchars($imovel['cidade']); ?>
                        </div>
                        <div class="imovel-features">
                            <div class="imovel-feature">
                                <i class="fas fa-bed"></i><br>
                                <span><?php echo $imovel['quartos']; ?> Quartos</span>
                            </div>
                            <div class="imovel-feature">
                                <i class="fas fa-bath"></i><br>
                                <span><?php echo $imovel['banheiros']; ?> Banheiros</span>
                            </div>
                            <div class="imovel-feature">
                                <i class="fas fa-car"></i><br>
                                <span><?php echo $imovel['vagas']; ?> Vagas</span>
                            </div>
                        </div>
                        <button class="btn-agendar" onclick="agendarVisita(<?php echo $imovel['id']; ?>)">
                            <i class="fas fa-calendar"></i> Agendar Visita
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <?php if($usuario['tipo'] == 'gerente'): ?>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="stat-info">
                        <h3>15</h3>
                        <p>Imóveis Gerenciados</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>8</h3>
                        <p>Clientes Ativos</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-info">
                        <h3>R$ 45.000</h3>
                        <p>Faturamento Mensal</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
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
        });

        // Função para agendar visita (simulação)
        function agendarVisita(imovelId) {
            alert('Funcionalidade de agendamento em desenvolvimento!\nImóvel ID: ' + imovelId);
            // Aqui você pode implementar um modal ou redirecionar para página de agendamento
            // window.location.href = `agendar.php?imovel=${imovelId}`;
        }

        // Animar elementos ao rolar
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observar cards de estatísticas e imóveis
        document.querySelectorAll('.stat-card, .imovel-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            observer.observe(card);
        });
    </script>
</body>
</html>