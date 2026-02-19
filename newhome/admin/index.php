<?php
// admin/index.php
require_once '../includes/auth.php';
require_once '../config/funcoes.php';

verificarAdmin();
$funcoes = new Funcoes();

// Estatísticas
$total_usuarios = count($funcoes->listarUsuarios());
$total_clientes = count($funcoes->listarUsuarios('cliente'));
$total_gerentes = count($funcoes->listarUsuarios('gerente'));
$total_imoveis = count($funcoes->listarImoveis());
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Home - Painel Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        :root {
            --cor-fundo: #0B0F1A;
            --cor-secundaria: #1A1E2E;
            --cor-terciaria: #F0B429;
            --cor-texto: #F8F6F3;
            --cor-card: #1A1E2E;
            --sombra: rgba(0, 0, 0, 0.2);
        }

        body {
            background-color: var(--cor-fundo);
            color: var(--cor-texto);
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background-color: var(--cor-secundaria);
            padding: 20px 0;
        }

        .logo {
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }

        .logo h2 {
            color: var(--cor-terciaria);
        }

        .nav-menu {
            list-style: none;
        }

        .nav-item {
            padding: 15px 20px;
            transition: all 0.3s;
        }

        .nav-item:hover {
            background-color: rgba(240, 180, 41, 0.1);
        }

        .nav-item.active {
            background-color: var(--cor-terciaria);
        }

        .nav-item.active a {
            color: #000;
        }

        .nav-item a {
            color: var(--cor-texto);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
        }

        .main-content {
            flex: 1;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
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
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background-color: var(--cor-card);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 5px 15px var(--sombra);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            background-color: var(--cor-terciaria);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            color: #000;
            font-size: 20px;
        }

        .stat-info h3 {
            font-size: 28px;
            color: var(--cor-terciaria);
            margin-bottom: 5px;
        }

        .section-title {
            font-size: 24px;
            margin-bottom: 20px;
            color: var(--cor-terciaria);
        }

        .quick-actions {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .action-btn {
            background-color: var(--cor-terciaria);
            color: #000;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .action-btn:hover {
            background-color: #e0a010;
            transform: translateY(-2px);
        }

        .recent-users {
            background-color: var(--cor-card);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 5px 15px var(--sombra);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            text-align: left;
            padding: 12px;
            border-bottom: 2px solid var(--cor-terciaria);
            color: var(--cor-terciaria);
        }

        .table td {
            padding: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge.cliente {
            background-color: #17a2b8;
            color: white;
        }

        .badge.gerente {
            background-color: #28a745;
            color: white;
        }

        .badge.admin {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <h2><i class="fas fa-home"></i> <a href="../index.php">NewHome Admin</a></h2>
        </div>
        
        <ul class="nav-menu">
            <li class="nav-item active">
                <a href="index.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="usuarios.php">
                    <i class="fas fa-users"></i> Usuários
                </a>
            </li>
            <li class="nav-item">
                <a href="imoveis.php">
                    <i class="fas fa-home"></i> Imóveis
                </a>
            </li>
            <li class="nav-item">
                <a href="../index.php">
                    <i class="fas fa-arrow-left"></i> Voltar ao Site
                </a>
            </li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>Painel Administrativo</h1>
            
            <div class="user-info">
                <div class="user-avatar">A</div>
                <div>
                    <strong><?php echo $_SESSION['usuario_nome']; ?></strong><br>
                    <small>Administrador</small>
                </div>
                <form method="POST" action="../includes/logout.php">
                    <button type="submit" class="btn-logout">
                        <i class="fas fa-sign-out-alt"></i> Sair
                    </button>
                </form>
            </div>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $total_usuarios; ?></h3>
                    <p>Total de Usuários</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $total_clientes; ?></h3>
                    <p>Clientes</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $total_gerentes; ?></h3>
                    <p>Gerentes</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-home"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $total_imoveis; ?></h3>
                    <p>Imóveis</p>
                </div>
            </div>
        </div>
        
        <h2 class="section-title">Ações Rápidas</h2>
        <div class="quick-actions">
            <button class="action-btn" onclick="window.location.href='usuarios.php?action=create'">
                <i class="fas fa-user-plus"></i> Novo Usuário
            </button>
            <button class="action-btn" onclick="window.location.href='imoveis.php?action=create'">
                <i class="fas fa-home"></i> Novo Imóvel
            </button>
            <button class="action-btn">
                <i class="fas fa-file-export"></i> Relatórios
            </button>
            <button class="action-btn">
                <i class="fas fa-cog"></i> Configurações
            </button>
        </div>
        
        <h2 class="section-title">Usuários Recentes</h2>
        <div class="recent-users">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Tipo</th>
                        <th>Data Cadastro</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $usuarios_recentes = $funcoes->listarUsuarios();
                    $usuarios_recentes = array_slice($usuarios_recentes, 0, 5);
                    
                    foreach($usuarios_recentes as $usuario):
                    ?>
                    <tr>
                        <td>#<?php echo $usuario['id']; ?></td>
                        <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                        <td>
                            <span class="badge <?php echo $usuario['tipo']; ?>">
                                <?php echo $usuario['tipo']; ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($usuario['data_cadastro'])); ?></td>
                        <td>
                            <button onclick="window.location.href='usuarios.php?action=edit&id=<?php echo $usuario['id']; ?>'">
                                <i class="fas fa-edit"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>