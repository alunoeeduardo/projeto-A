<?php
// admin/imoveis.php
require_once '../includes/auth.php';
require_once '../config/funcoes.php';

verificarGerente();
$funcoes = new Funcoes();

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Processar ações
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['action'])) {
        switch($_POST['action']) {
            case 'create':
                $dados = [
                    'titulo' => filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_STRING),
                    'descricao' => filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_STRING),
                    'tipo' => $_POST['tipo'],
                    'endereco' => filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_STRING),
                    'bairro' => filter_input(INPUT_POST, 'bairro', FILTER_SANITIZE_STRING),
                    'cidade' => filter_input(INPUT_POST, 'cidade', FILTER_SANITIZE_STRING),
                    'estado' => $_POST['estado'],
                    'cep' => filter_input(INPUT_POST, 'cep', FILTER_SANITIZE_STRING),
                    'valor' => str_replace(['.', ','], ['', '.'], $_POST['valor']),
                    'area' => $_POST['area'],
                    'quartos' => $_POST['quartos'],
                    'banheiros' => $_POST['banheiros'],
                    'vagas' => $_POST['vagas'],
                    'status' => $_POST['status'],
                    'destaque' => isset($_POST['destaque']) ? 1 : 0,
                    'id_proprietario' => $_POST['id_proprietario'] ?: null
                ];
                
                if($funcoes->cadastrarImovel($dados)) {
                    header("Location: imoveis.php?msg=create_success");
                } else {
                    header("Location: imoveis.php?msg=create_error");
                }
                exit();
                
            case 'update':
                $dados = [
                    'titulo' => filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_STRING),
                    'descricao' => filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_STRING),
                    'tipo' => $_POST['tipo'],
                    'endereco' => filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_STRING),
                    'bairro' => filter_input(INPUT_POST, 'bairro', FILTER_SANITIZE_STRING),
                    'cidade' => filter_input(INPUT_POST, 'cidade', FILTER_SANITIZE_STRING),
                    'estado' => $_POST['estado'],
                    'cep' => filter_input(INPUT_POST, 'cep', FILTER_SANITIZE_STRING),
                    'valor' => str_replace(['.', ','], ['', '.'], $_POST['valor']),
                    'area' => $_POST['area'],
                    'quartos' => $_POST['quartos'],
                    'banheiros' => $_POST['banheiros'],
                    'vagas' => $_POST['vagas'],
                    'status' => $_POST['status'],
                    'destaque' => isset($_POST['destaque']) ? 1 : 0,
                    'id_proprietario' => $_POST['id_proprietario'] ?: null
                ];
                
                if($funcoes->atualizarImovel($id, $dados)) {
                    header("Location: imoveis.php?msg=update_success");
                } else {
                    header("Location: imoveis.php?msg=update_error");
                }
                exit();
                
            case 'delete':
                if($funcoes->excluirImovel($id)) {
                    header("Location: imoveis.php?msg=delete_success");
                } else {
                    header("Location: imoveis.php?msg=delete_error");
                }
                exit();
        }
    }
}

// Obter dados do imóvel para edição
$imovel_edit = null;
if($id > 0 && ($action == 'edit' || $action == 'delete')) {
    $imovel_edit = $funcoes->getImovel($id);
}

// Listar todos os imóveis
$imoveis = $funcoes->listarImoveis();

// Listar proprietários
$proprietarios = $funcoes->listarUsuarios('cliente');

// Mensagens
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';

// Tipos de imóveis
$tipos_imoveis = [
    'casa' => 'Casa',
    'apartamento' => 'Apartamento',
    'sobrado' => 'Sobrado',
    'kitnet' => 'Kitnet',
    'terreno' => 'Terreno',
    'comercial' => 'Comercial'
];

// Estados
$estados = [
    'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA',
    'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN',
    'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'
];

// Status
$status_imoveis = [
    'disponivel' => 'Disponível',
    'alugado' => 'Alugado',
    'vendido' => 'Vendido',
    'manutencao' => 'Em Manutenção'
];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Home - Gerenciar Imóveis</title>
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
            --cor-sucesso: #28a745;
            --cor-erro: #dc3545;
            --cor-info: #17a2b8;
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
            position: fixed;
            height: 100vh;
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
            margin-left: 250px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background-color: var(--cor-secundaria);
            padding: 20px;
            border-radius: 12px;
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

        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary {
            background-color: var(--cor-terciaria);
            color: #000;
        }

        .btn-primary:hover {
            background-color: #e0a010;
            transform: translateY(-2px);
        }

        .btn-danger {
            background-color: var(--cor-erro);
            color: white;
        }

        .btn-success {
            background-color: var(--cor-sucesso);
            color: white;
        }

        .btn-outline {
            background-color: transparent;
            color: var(--cor-terciaria);
            border: 2px solid var(--cor-terciaria);
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .alert-success {
            background-color: rgba(40, 167, 69, 0.1);
            color: var(--cor-sucesso);
            border: 1px solid rgba(40, 167, 69, 0.2);
        }

        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--cor-erro);
            border: 1px solid rgba(220, 53, 69, 0.2);
        }

        .table-container {
            background-color: var(--cor-card);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 5px 15px var(--sombra);
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
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

        .badge.disponivel {
            background-color: #28a745;
            color: white;
        }

        .badge.alugado {
            background-color: #ffc107;
            color: #000;
        }

        .badge.vendido {
            background-color: #dc3545;
            color: white;
        }

        .badge.manutencao {
            background-color: #6c757d;
            color: white;
        }

        .actions {
            display: flex;
            gap: 5px;
        }

        .actions button {
            background: none;
            border: none;
            color: var(--cor-texto);
            cursor: pointer;
            padding: 5px;
        }

        .actions button:hover {
            color: var(--cor-terciaria);
        }

        .form-container {
            background-color: var(--cor-card);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 5px 15px var(--sombra);
        }

        .form-title {
            font-size: 24px;
            margin-bottom: 30px;
            color: var(--cor-terciaria);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-weight: 600;
            color: var(--cor-texto);
        }

        .form-control {
            padding: 10px 15px;
            border: 2px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            background-color: var(--cor-secundaria);
            color: var(--cor-texto);
            font-size: 16px;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--cor-terciaria);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="logo">
            <h2><i class="fas fa-home"></i> NewHome</h2>
        </div>
        
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="../dashboard.php">
                    <i class="fas fa-arrow-left"></i> Voltar ao Site
                </a>
            </li>
            <li class="nav-item">
                <a href="index.php">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="usuarios.php">
                    <i class="fas fa-users"></i> Usuários
                </a>
            </li>
            <li class="nav-item active">
                <a href="imoveis.php">
                    <i class="fas fa-home"></i> Imóveis
                </a>
            </li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>Gerenciar Imóveis</h1>
            
            <div class="user-info">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($_SESSION['usuario_nome'], 0, 1)); ?>
                </div>
                <div>
                    <strong><?php echo $_SESSION['usuario_nome']; ?></strong><br>
                    <small><?php echo ucfirst($_SESSION['usuario_tipo']); ?></small>
                </div>
                <a href="../includes/logout.php" class="btn btn-outline" style="margin-left: 20px;">
                    <i class="fas fa-sign-out-alt"></i> Sair
                </a>
            </div>
        </div>
        
        <?php if($msg): ?>
        <div class="alert alert-<?php echo strpos($msg, 'error') ? 'danger' : 'success'; ?>">
            <?php 
            $mensagens = [
                'create_success' => 'Imóvel cadastrado com sucesso!',
                'create_error' => 'Erro ao cadastrar imóvel.',
                'update_success' => 'Imóvel atualizado com sucesso!',
                'update_error' => 'Erro ao atualizar imóvel.',
                'delete_success' => 'Imóvel excluído com sucesso!',
                'delete_error' => 'Erro ao excluir imóvel.'
            ];
            echo $mensagens[$msg] ?? '';
            ?>
        </div>
        <?php endif; ?>
        
        <?php if($action == 'list'): ?>
        <div class="table-container">
            <div class="table-header">
                <h2>Todos os Imóveis</h2>
                <a href="imoveis.php?action=create" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Novo Imóvel
                </a>
            </div>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Título</th>
                        <th>Tipo</th>
                        <th>Cidade</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($imoveis as $imovel): ?>
                    <tr>
                        <td>#<?php echo $imovel['id']; ?></td>
                        <td><?php echo htmlspecialchars($imovel['titulo']); ?></td>
                        <td><?php echo $tipos_imoveis[$imovel['tipo']] ?? $imovel['tipo']; ?></td>
                        <td><?php echo htmlspecialchars($imovel['cidade']); ?></td>
                        <td>R$ <?php echo number_format($imovel['valor'], 2, ',', '.'); ?></td>
                        <td>
                            <span class="badge <?php echo $imovel['status']; ?>">
                                <?php echo $status_imoveis[$imovel['status']] ?? $imovel['status']; ?>
                            </span>
                            <?php if($imovel['destaque']): ?>
                                <span style="color: var(--cor-terciaria); margin-left: 5px;">
                                    <i class="fas fa-star"></i>
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <button onclick="window.location.href='imoveis.php?action=edit&id=<?php echo $imovel['id']; ?>'">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="if(confirm('Tem certeza que deseja excluir este imóvel?')) window.location.href='imoveis.php?action=delete&id=<?php echo $imovel['id']; ?>'">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php elseif($action == 'create' || $action == 'edit'): ?>
        <div class="form-container">
            <h2 class="form-title"><?php echo $action == 'create' ? 'Cadastrar Novo Imóvel' : 'Editar Imóvel'; ?></h2>
            
            <form method="POST" action="">
                <input type="hidden" name="action" value="<?php echo $action == 'create' ? 'create' : 'update'; ?>">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="titulo">Título *</label>
                        <input type="text" id="titulo" name="titulo" class="form-control" 
                               value="<?php echo $imovel_edit ? htmlspecialchars($imovel_edit['titulo']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="tipo">Tipo *</label>
                        <select id="tipo" name="tipo" class="form-control" required>
                            <option value="">Selecione...</option>
                            <?php foreach($tipos_imoveis as $valor => $nome): ?>
                            <option value="<?php echo $valor; ?>" 
                                <?php echo ($imovel_edit && $imovel_edit['tipo'] == $valor) ? 'selected' : ''; ?>>
                                <?php echo $nome; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="valor">Valor (R$) *</label>
                        <input type="text" id="valor" name="valor" class="form-control" 
                               value="<?php echo $imovel_edit ? number_format($imovel_edit['valor'], 2, ',', '.') : ''; ?>" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status *</label>
                        <select id="status" name="status" class="form-control" required>
                            <?php foreach($status_imoveis as $valor => $nome): ?>
                            <option value="<?php echo $valor; ?>" 
                                <?php echo ($imovel_edit && $imovel_edit['status'] == $valor) ? 'selected' : ''; ?>>
                                <?php echo $nome; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="endereco">Endereço *</label>
                        <input type="text" id="endereco" name="endereco" class="form-control" 
                               value="<?php echo $imovel_edit ? htmlspecialchars($imovel_edit['endereco']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="bairro">Bairro</label>
                        <input type="text" id="bairro" name="bairro" class="form-control" 
                               value="<?php echo $imovel_edit ? htmlspecialchars($imovel_edit['bairro']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="cidade">Cidade *</label>
                        <input type="text" id="cidade" name="cidade" class="form-control" 
                               value="<?php echo $imovel_edit ? htmlspecialchars($imovel_edit['cidade']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="estado">Estado *</label>
                        <select id="estado" name="estado" class="form-control" required>
                            <option value="">Selecione...</option>
                            <?php foreach($estados as $estado): ?>
                            <option value="<?php echo $estado; ?>" 
                                <?php echo ($imovel_edit && $imovel_edit['estado'] == $estado) ? 'selected' : ''; ?>>
                                <?php echo $estado; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="cep">CEP</label>
                        <input type="text" id="cep" name="cep" class="form-control" 
                               value="<?php echo $imovel_edit ? htmlspecialchars($imovel_edit['cep']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="area">Área (m²)</label>
                        <input type="number" step="0.01" id="area" name="area" class="form-control" 
                               value="<?php echo $imovel_edit ? $imovel_edit['area'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="quartos">Quartos</label>
                        <input type="number" id="quartos" name="quartos" class="form-control" 
                               value="<?php echo $imovel_edit ? $imovel_edit['quartos'] : '0'; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="banheiros">Banheiros</label>
                        <input type="number" id="banheiros" name="banheiros" class="form-control" 
                               value="<?php echo $imovel_edit ? $imovel_edit['banheiros'] : '0'; ?>">
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="vagas">Vagas Garagem</label>
                        <input type="number" id="vagas" name="vagas" class="form-control" 
                               value="<?php echo $imovel_edit ? $imovel_edit['vagas'] : '0'; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="id_proprietario">Proprietário</label>
                        <select id="id_proprietario" name="id_proprietario" class="form-control">
                            <option value="">Nenhum</option>
                            <?php foreach($proprietarios as $proprietario): ?>
                            <option value="<?php echo $proprietario['id']; ?>" 
                                <?php echo ($imovel_edit && $imovel_edit['id_proprietario'] == $proprietario['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($proprietario['nome']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="descricao">Descrição</label>
                        <textarea id="descricao" name="descricao" class="form-control" rows="4"><?php echo $imovel_edit ? htmlspecialchars($imovel_edit['descricao']) : ''; ?></textarea>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="destaque" name="destaque" value="1" 
                               <?php echo ($imovel_edit && $imovel_edit['destaque']) ? 'checked' : ''; ?>>
                        <label for="destaque">Destacar este imóvel</label>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                    <a href="imoveis.php" class="btn btn-outline">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // Máscara para valor
        document.getElementById('valor')?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            value = (value/100).toFixed(2) + '';
            value = value.replace(".", ",");
            value = value.replace(/(\d)(\d{3})(\d{3}),/g, "$1.$2.$3,");
            value = value.replace(/(\d)(\d{3}),/g, "$1.$2,");
            e.target.value = value ? 'R$ ' + value : '';
        });

        // Máscara para CEP
        document.getElementById('cep')?.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if(value.length > 5) {
                value = value.replace(/(\d{5})(\d+)/, '$1-$2');
            }
            e.target.value = value.substring(0, 9);
        });

        // Auto-completar endereço via CEP (simplificado)
        document.getElementById('cep')?.addEventListener('blur', function() {
            const cep = this.value.replace(/\D/g, '');
            if(cep.length === 8) {
                // Em um sistema real, você faria uma requisição para a API de CEP
                // fetch(`https://viacep.com.br/ws/${cep}/json/`)
                // .then(response => response.json())
                // .then(data => {
                //     if(!data.erro) {
                //         document.getElementById('endereco').value = data.logradouro;
                //         document.getElementById('bairro').value = data.bairro;
                //         document.getElementById('cidade').value = data.localidade;
                //         document.getElementById('estado').value = data.uf;
                //     }
                // });
            }
        });
    </script>
</body>
</html>