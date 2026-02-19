<?php
// admin/usuarios.php
require_once '../includes/auth.php';
require_once '../config/funcoes.php';

verificarAdmin();
$funcoes = new Funcoes();

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Processar ações
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(isset($_POST['action'])) {
        switch($_POST['action']) {
            case 'create':
                $dados = [
                    'nome' => filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING),
                    'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
                    'senha' => $_POST['senha'],
                    'tipo' => $_POST['tipo'],
                    'telefone' => filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING),
                    'cpf' => filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING),
                    'data_nascimento' => $_POST['data_nascimento'],
                    'endereco' => filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_STRING),
                    'ativo' => isset($_POST['ativo']) ? 1 : 0
                ];
                
                if($funcoes->cadastrarUsuario($dados)) {
                    header("Location: usuarios.php?msg=create_success");
                } else {
                    header("Location: usuarios.php?msg=create_error");
                }
                exit();
                
            case 'update':
                $dados = [
                    'nome' => filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING),
                    'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
                    'telefone' => filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_STRING),
                    'cpf' => filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING),
                    'data_nascimento' => $_POST['data_nascimento'],
                    'endereco' => filter_input(INPUT_POST, 'endereco', FILTER_SANITIZE_STRING),
                    'ativo' => isset($_POST['ativo']) ? 1 : 0
                ];
                
                if($funcoes->atualizarUsuario($id, $dados)) {
                    header("Location: usuarios.php?msg=update_success");
                } else {
                    header("Location: usuarios.php?msg=update_error");
                }
                exit();
                
            case 'delete':
                if($funcoes->excluirUsuario($id)) {
                    header("Location: usuarios.php?msg=delete_success");
                } else {
                    header("Location: usuarios.php?msg=delete_error");
                }
                exit();
        }
    }
}

// Obter dados do usuário para edição
$usuario_edit = null;
if($id > 0 && ($action == 'edit' || $action == 'delete')) {
    $usuario_edit = $funcoes->getUsuario($id);
}

// Listar todos os usuários
$usuarios = $funcoes->listarUsuarios();

// Mensagens
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Home - Gerenciar Usuários</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Estilos similares ao admin/index.php - mantendo consistência */
        /* Adicione aqui os estilos específicos para a página de usuários */
    </style>
</head>
<body>
    <div class="sidebar">
        <!-- Mesma sidebar do admin/index.php -->
    </div>
    
    <div class="main-content">
        <div class="header">
            <h1>Gerenciar Usuários</h1>
        </div>
        
        <?php if($msg): ?>
        <div class="alert alert-<?php echo strpos($msg, 'error') ? 'danger' : 'success'; ?>">
            <?php 
            $mensagens = [
                'create_success' => 'Usuário criado com sucesso!',
                'create_error' => 'Erro ao criar usuário.',
                'update_success' => 'Usuário atualizado com sucesso!',
                'update_error' => 'Erro ao atualizar usuário.',
                'delete_success' => 'Usuário excluído com sucesso!',
                'delete_error' => 'Erro ao excluir usuário.'
            ];
            echo $mensagens[$msg] ?? '';
            ?>
        </div>
        <?php endif; ?>
        
        <?php if($action == 'list'): ?>
        <div class="table-container">
            <div class="table-header">
                <h2>Todos os Usuários</h2>
                <button onclick="window.location.href='usuarios.php?action=create'" class="btn-add">
                    <i class="fas fa-plus"></i> Novo Usuário
                </button>
            </div>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Tipo</th>
                        <th>Status</th>
                        <th>Data Cadastro</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($usuarios as $usuario): ?>
                    <tr>
                        <td>#<?php echo $usuario['id']; ?></td>
                        <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                        <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                        <td>
                            <span class="badge <?php echo $usuario['tipo']; ?>">
                                <?php echo $usuario['tipo']; ?>
                            </span>
                        </td>
                        <td>
                            <span class="status <?php echo $usuario['ativo'] ? 'active' : 'inactive'; ?>">
                                <?php echo $usuario['ativo'] ? 'Ativo' : 'Inativo'; ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y', strtotime($usuario['data_cadastro'])); ?></td>
                        <td class="actions">
                            <button onclick="window.location.href='usuarios.php?action=edit&id=<?php echo $usuario['id']; ?>'">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="if(confirm('Tem certeza que deseja excluir?')) window.location.href='usuarios.php?action=delete&id=<?php echo $usuario['id']; ?>'">
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
            <h2><?php echo $action == 'create' ? 'Novo Usuário' : 'Editar Usuário'; ?></h2>
            
            <form method="POST" action="">
                <input type="hidden" name="action" value="<?php echo $action == 'create' ? 'create' : 'update'; ?>">
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Nome Completo *</label>
                        <input type="text" name="nome" value="<?php echo $usuario_edit ? htmlspecialchars($usuario_edit['nome']) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" value="<?php echo $usuario_edit ? htmlspecialchars($usuario_edit['email']) : ''; ?>" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>CPF</label>
                        <input type="text" name="cpf" value="<?php echo $usuario_edit ? htmlspecialchars($usuario_edit['cpf']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Telefone</label>
                        <input type="text" name="telefone" value="<?php echo $usuario_edit ? htmlspecialchars($usuario_edit['telefone']) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Tipo de Usuário *</label>
                    <select name="tipo" required>
                        <option value="cliente" <?php echo ($usuario_edit && $usuario_edit['tipo'] == 'cliente') ? 'selected' : ''; ?>>Cliente</option>
                        <option value="gerente" <?php echo ($usuario_edit && $usuario_edit['tipo'] == 'gerente') ? 'selected' : ''; ?>>Gerente</option>
                        <option value="admin" <?php echo ($usuario_edit && $usuario_edit['tipo'] == 'admin') ? 'selected' : ''; ?>>Administrador</option>
                    </select>
                </div>
                
                <?php if($action == 'create'): ?>
                <div class="form-row">
                    <div class="form-group">
                        <label>Senha *</label>
                        <input type="password" name="senha" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Confirmar Senha *</label>
                        <input type="password" name="confirmar_senha" required>
                    </div>
                </div>
                <?php endif; ?>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Data de Nascimento</label>
                        <input type="date" name="data_nascimento" value="<?php echo $usuario_edit ? $usuario_edit['data_nascimento'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>Endereço</label>
                        <input type="text" name="endereco" value="<?php echo $usuario_edit ? htmlspecialchars($usuario_edit['endereco']) : ''; ?>">
                    </div>
                </div>
                
                <div class="form-group checkbox">
                    <label>
                        <input type="checkbox" name="ativo" value="1" <?php echo (!$usuario_edit || $usuario_edit['ativo']) ? 'checked' : ''; ?>>
                        Usuário Ativo
                    </label>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                    <button type="button" class="btn-cancel" onclick="window.location.href='usuarios.php'">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>