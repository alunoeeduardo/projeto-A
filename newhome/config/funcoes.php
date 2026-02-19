<?php
// config/funcoes.php

require_once 'database.php';

class Funcoes {
    private $conn;
    
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }
    
    // ==================== FUNÇÕES PARA CADASTRO DIFERENCIADO ====================
    
    // Função para processar uploads de documentos
    public function processarUploads($cpf) {
        $resultado = [
            'sucesso' => false,
            'mensagem' => '',
            'foto_documento_path' => '',
            'selfie_documento_path' => ''
        ];
        
        // Diretório para uploads
        $cpf_limpo = preg_replace('/[^0-9]/', '', $cpf);
        $ano_mes = date('Y/m');
        $diretorio = 'uploads/documentos/' . $ano_mes . '/';
        
        // Criar diretório se não existir
        if(!is_dir($diretorio)) {
            mkdir($diretorio, 0777, true);
        }
        
        // Tipos de arquivo permitidos
        $tipos_permitidos = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        $tamanho_maximo = 2 * 1024 * 1024; // 2MB
        
        // Processar foto do documento
        if(isset($_FILES['foto_documento']) && $_FILES['foto_documento']['error'] == 0) {
            $arquivo = $_FILES['foto_documento'];
            $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
            
            // Validar tipo de arquivo
            if(!in_array($extensao, $tipos_permitidos)) {
                $resultado['mensagem'] = 'Tipo de arquivo não permitido para o documento. Use: ' . implode(', ', $tipos_permitidos);
                return $resultado;
            }
            
            // Validar tamanho
            if($arquivo['size'] > $tamanho_maximo) {
                $resultado['mensagem'] = 'Arquivo muito grande. Máximo: 2MB';
                return $resultado;
            }
            
            $nome_arquivo = 'documento_' . $cpf_limpo . '_' . time() . '.' . $extensao;
            $caminho = $diretorio . $nome_arquivo;
            
            if(move_uploaded_file($arquivo['tmp_name'], $caminho)) {
                $resultado['foto_documento_path'] = $caminho;
            } else {
                $resultado['mensagem'] = 'Erro ao salvar foto do documento.';
                return $resultado;
            }
        }
        
        // Processar selfie com documento
        if(isset($_FILES['selfie_documento']) && $_FILES['selfie_documento']['error'] == 0) {
            $arquivo = $_FILES['selfie_documento'];
            $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
            
            // Validar tipo de arquivo
            if(!in_array($extensao, $tipos_permitidos)) {
                $resultado['mensagem'] = 'Tipo de arquivo não permitido para a selfie. Use: ' . implode(', ', $tipos_permitidos);
                return $resultado;
            }
            
            // Validar tamanho
            if($arquivo['size'] > $tamanho_maximo) {
                $resultado['mensagem'] = 'Arquivo muito grande. Máximo: 2MB';
                return $resultado;
            }
            
            $nome_arquivo = 'selfie_' . $cpf_limpo . '_' . time() . '.' . $extensao;
            $caminho = $diretorio . $nome_arquivo;
            
            if(move_uploaded_file($arquivo['tmp_name'], $caminho)) {
                $resultado['selfie_documento_path'] = $caminho;
            } else {
                $resultado['mensagem'] = 'Erro ao salvar selfie com documento.';
                return $resultado;
            }
        }
        
        $resultado['sucesso'] = true;
        return $resultado;
    }
    
    // Função para verificar CPF
    public function verificarCPF($cpf) {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        
        $query = "SELECT id FROM usuarios WHERE REPLACE(REPLACE(cpf, '.', ''), '-', '') = :cpf";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cpf", $cpf);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
    
    // Função para verificar RG
    public function verificarRG($rg) {
        $rg = preg_replace('/[^0-9]/', '', $rg);
        
        $query = "SELECT id FROM usuarios WHERE rg IS NOT NULL AND REPLACE(REPLACE(rg, '.', ''), '-', '') = :rg";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":rg", $rg);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
    
    // Função para cadastrar usuário (versão atualizada para dois tipos)
    public function cadastrarUsuario($dados, $tipo_cadastro = 'cliente') {
        try {
            // Hash da senha
            $senha_hash = password_hash($dados['senha'], PASSWORD_DEFAULT);
            
            if($tipo_cadastro == 'cliente') {
                $query = "INSERT INTO usuarios (nome, email, senha, tipo, telefone, cpf, rg, profissao, estado_civil, data_nascimento, endereco, cep, numero_endereco, complemento, foto_documento_path, selfie_documento_path) 
                          VALUES (:nome, :email, :senha, :tipo, :telefone, :cpf, :rg, :profissao, :estado_civil, :data_nascimento, :endereco, :cep, :numero, :complemento, :foto_documento, :selfie_documento)";
                
                $stmt = $this->conn->prepare($query);
                
                // Mapear campos opcionais
                $foto_documento = $dados['foto_documento_path'] ?? null;
                $selfie_documento = $dados['selfie_documento_path'] ?? null;
                $profissao = $dados['profissao'] ?? null;
                $complemento = $dados['complemento'] ?? null;
                $cep = $dados['cep'] ?? null;
                $numero = $dados['numero'] ?? null;
                $estado_civil = $dados['estado_civil'] ?? 'solteiro';
                
                $stmt->bindParam(":nome", $dados['nome']);
                $stmt->bindParam(":email", $dados['email']);
                $stmt->bindParam(":senha", $senha_hash);
                $stmt->bindParam(":tipo", $dados['tipo']);
                $stmt->bindParam(":telefone", $dados['telefone']);
                $stmt->bindParam(":cpf", $dados['cpf']);
                $stmt->bindParam(":rg", $dados['rg']);
                $stmt->bindParam(":profissao", $profissao);
                $stmt->bindParam(":estado_civil", $estado_civil);
                $stmt->bindParam(":data_nascimento", $dados['data_nascimento']);
                $stmt->bindParam(":endereco", $dados['endereco']);
                $stmt->bindParam(":cep", $cep);
                $stmt->bindParam(":numero", $numero);
                $stmt->bindParam(":complemento", $complemento);
                $stmt->bindParam(":foto_documento", $foto_documento);
                $stmt->bindParam(":selfie_documento", $selfie_documento);
                
            } else {
                // Cadastro de gerente
                $query = "INSERT INTO usuarios (nome, email, senha, tipo, telefone, cpf, cep, endereco, data_contratacao, salario, setor) 
                          VALUES (:nome, :email, :senha, :tipo, :telefone, :cpf, :cep, :endereco, :data_contratacao, :salario, :setor)";
                
                $stmt = $this->conn->prepare($query);
                
                // Converter salário para formato decimal
                $salario = isset($dados['salario']) ? str_replace(['R$', '.', ','], ['', '', '.'], $dados['salario']) : 0;
                $salario = floatval($salario);
                
                $stmt->bindParam(":nome", $dados['nome']);
                $stmt->bindParam(":email", $dados['email']);
                $stmt->bindParam(":senha", $senha_hash);
                $stmt->bindParam(":tipo", $dados['tipo']);
                $stmt->bindParam(":telefone", $dados['telefone']);
                $stmt->bindParam(":cpf", $dados['cpf']);
                $stmt->bindParam(":cep", $dados['cep']);
                $stmt->bindParam(":endereco", $dados['endereco']);
                $stmt->bindParam(":data_contratacao", $dados['data_contratacao']);
                $stmt->bindParam(":salario", $salario);
                $stmt->bindParam(":setor", $dados['setor']);
            }
            
            if($stmt->execute()) {
                $usuario_id = $this->conn->lastInsertId();
                
                // Registrar log
                $this->registrarLog($usuario_id, 'cadastro_' . $tipo_cadastro, 'Usuário cadastrado com sucesso');
                
                return true;
            }
            
            return false;
            
        } catch(PDOException $e) {
            error_log("Erro ao cadastrar usuário: " . $e->getMessage());
            return false;
        }
    }
    
    // Função para buscar endereço pelo CEP (ViaCEP)
    public function buscarEnderecoPorCEP($cep) {
        $cep = preg_replace('/[^0-9]/', '', $cep);
        
        if(strlen($cep) !== 8) {
            return ['erro' => true, 'mensagem' => 'CEP inválido'];
        }
        
        $url = "https://viacep.com.br/ws/{$cep}/json/";
        
        // Usando cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        $dados = json_decode($response, true);
        
        if(isset($dados['erro'])) {
            return ['erro' => true, 'mensagem' => 'CEP não encontrado'];
        }
        
        return [
            'erro' => false,
            'logradouro' => $dados['logradouro'] ?? '',
            'bairro' => $dados['bairro'] ?? '',
            'cidade' => $dados['localidade'] ?? '',
            'estado' => $dados['uf'] ?? '',
            'cep' => $dados['cep'] ?? $cep
        ];
    }
    
    // ==================== USUÁRIOS ====================
    
    // Função para fazer login
    public function login($email, $senha) {
        $query = "SELECT id, nome, email, senha, tipo FROM usuarios WHERE email = :email AND ativo = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if(password_verify($senha, $row['senha'])) {
                // Atualizar último login
                $update = "UPDATE usuarios SET ultimo_login = NOW() WHERE id = :id";
                $stmt2 = $this->conn->prepare($update);
                $stmt2->bindParam(":id", $row['id']);
                $stmt2->execute();
                
                // Registrar log
                $this->registrarLog($row['id'], 'login', 'Login realizado com sucesso');
                
                return $row;
            }
        }
        return false;
    }
    
    // Função para verificar se email já existe
    public function verificarEmail($email) {
        $query = "SELECT id FROM usuarios WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
    
    // Função para listar usuários (admin)
    public function listarUsuarios($tipo = null) {
        $query = "SELECT * FROM usuarios WHERE 1=1";
        
        if($tipo) {
            $query .= " AND tipo = :tipo";
        }
        
        $query .= " ORDER BY data_cadastro DESC";
        
        $stmt = $this->conn->prepare($query);
        
        if($tipo) {
            $stmt->bindParam(":tipo", $tipo);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Função para listar gerentes
    public function listarGerentes() {
        $query = "SELECT * FROM usuarios WHERE tipo = 'gerente' AND ativo = 1 ORDER BY nome";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Função para listar clientes
    public function listarClientes() {
        $query = "SELECT * FROM usuarios WHERE tipo = 'cliente' AND ativo = 1 ORDER BY nome";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Função para obter usuário por ID
    public function getUsuario($id) {
        $query = "SELECT * FROM usuarios WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Função para obter usuário por CPF
    public function getUsuarioPorCPF($cpf) {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        
        $query = "SELECT * FROM usuarios WHERE REPLACE(REPLACE(cpf, '.', ''), '-', '') = :cpf";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":cpf", $cpf);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Função para atualizar usuário
    public function atualizarUsuario($id, $dados) {
        $query = "UPDATE usuarios SET 
                  nome = :nome, 
                  email = :email, 
                  telefone = :telefone,
                  cpf = :cpf,
                  data_nascimento = :data_nascimento,
                  endereco = :endereco,
                  ativo = :ativo
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":nome", $dados['nome']);
        $stmt->bindParam(":email", $dados['email']);
        $stmt->bindParam(":telefone", $dados['telefone']);
        $stmt->bindParam(":cpf", $dados['cpf']);
        $stmt->bindParam(":data_nascimento", $dados['data_nascimento']);
        $stmt->bindParam(":endereco", $dados['endereco']);
        $stmt->bindParam(":ativo", $dados['ativo']);
        $stmt->bindParam(":id", $id);
        
        $resultado = $stmt->execute();
        
        if($resultado) {
            $this->registrarLog($id, 'atualizacao_usuario', 'Dados do usuário atualizados');
        }
        
        return $resultado;
    }
    
    // Função para excluir usuário (soft delete)
    public function excluirUsuario($id) {
        $query = "UPDATE usuarios SET ativo = 0 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        
        $resultado = $stmt->execute();
        
        if($resultado) {
            $this->registrarLog($id, 'exclusao_usuario', 'Usuário desativado');
        }
        
        return $resultado;
    }
    
    // Função para aprovar documentos do cliente
    public function aprovarDocumentosCliente($usuario_id) {
        $query = "UPDATE usuarios SET documento_aprovado = 1 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $usuario_id);
        
        $resultado = $stmt->execute();
        
        if($resultado) {
            $this->registrarLog($usuario_id, 'aprovacao_documentos', 'Documentos do cliente aprovados');
        }
        
        return $resultado;
    }
    
    // ==================== IMÓVEIS ====================
    
    // Função para cadastrar imóvel
    public function cadastrarImovel($dados) {
        $query = "INSERT INTO imoveis (titulo, descricao, tipo, endereco, bairro, cidade, estado, cep, valor, area, quartos, banheiros, vagas, status, destaque, id_proprietario) 
                  VALUES (:titulo, :descricao, :tipo, :endereco, :bairro, :cidade, :estado, :cep, :valor, :area, :quartos, :banheiros, :vagas, :status, :destaque, :id_proprietario)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":titulo", $dados['titulo']);
        $stmt->bindParam(":descricao", $dados['descricao']);
        $stmt->bindParam(":tipo", $dados['tipo']);
        $stmt->bindParam(":endereco", $dados['endereco']);
        $stmt->bindParam(":bairro", $dados['bairro']);
        $stmt->bindParam(":cidade", $dados['cidade']);
        $stmt->bindParam(":estado", $dados['estado']);
        $stmt->bindParam(":cep", $dados['cep']);
        $stmt->bindParam(":valor", $dados['valor']);
        $stmt->bindParam(":area", $dados['area']);
        $stmt->bindParam(":quartos", $dados['quartos']);
        $stmt->bindParam(":banheiros", $dados['banheiros']);
        $stmt->bindParam(":vagas", $dados['vagas']);
        $stmt->bindParam(":status", $dados['status']);
        $stmt->bindParam(":destaque", $dados['destaque']);
        $stmt->bindParam(":id_proprietario", $dados['id_proprietario']);
        
        $resultado = $stmt->execute();
        
        if($resultado) {
            $imovel_id = $this->conn->lastInsertId();
            $this->registrarLog($dados['id_proprietario'], 'cadastro_imovel', "Imóvel ID $imovel_id cadastrado");
        }
        
        return $resultado;
    }
    
    // Função para listar imóveis
    public function listarImoveis($filtros = []) {
        $query = "SELECT i.*, u.nome as proprietario_nome 
                  FROM imoveis i 
                  LEFT JOIN usuarios u ON i.id_proprietario = u.id 
                  WHERE 1=1";
        
        if(isset($filtros['status'])) {
            $query .= " AND i.status = :status";
        }
        if(isset($filtros['tipo'])) {
            $query .= " AND i.tipo = :tipo";
        }
        if(isset($filtros['cidade'])) {
            $query .= " AND i.cidade LIKE :cidade";
        }
        if(isset($filtros['valor_max'])) {
            $query .= " AND i.valor <= :valor_max";
        }
        if(isset($filtros['quartos'])) {
            $query .= " AND i.quartos >= :quartos";
        }
        if(isset($filtros['destaque'])) {
            $query .= " AND i.destaque = :destaque";
        }
        
        $query .= " ORDER BY i.destaque DESC, i.data_cadastro DESC";
        
        $stmt = $this->conn->prepare($query);
        
        if(isset($filtros['status'])) {
            $stmt->bindParam(":status", $filtros['status']);
        }
        if(isset($filtros['tipo'])) {
            $stmt->bindParam(":tipo", $filtros['tipo']);
        }
        if(isset($filtros['cidade'])) {
            $cidade = "%" . $filtros['cidade'] . "%";
            $stmt->bindParam(":cidade", $cidade);
        }
        if(isset($filtros['valor_max'])) {
            $stmt->bindParam(":valor_max", $filtros['valor_max']);
        }
        if(isset($filtros['quartos'])) {
            $stmt->bindParam(":quartos", $filtros['quartos']);
        }
        if(isset($filtros['destaque'])) {
            $stmt->bindParam(":destaque", $filtros['destaque']);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Função para obter imóvel por ID
    public function getImovel($id) {
        $query = "SELECT i.*, u.nome as proprietario_nome 
                  FROM imoveis i 
                  LEFT JOIN usuarios u ON i.id_proprietario = u.id 
                  WHERE i.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Função para atualizar imóvel
    public function atualizarImovel($id, $dados) {
        $query = "UPDATE imoveis SET 
                  titulo = :titulo, 
                  descricao = :descricao, 
                  tipo = :tipo,
                  endereco = :endereco,
                  bairro = :bairro,
                  cidade = :cidade,
                  estado = :estado,
                  cep = :cep,
                  valor = :valor,
                  area = :area,
                  quartos = :quartos,
                  banheiros = :banheiros,
                  vagas = :vagas,
                  status = :status,
                  destaque = :destaque,
                  id_proprietario = :id_proprietario
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":titulo", $dados['titulo']);
        $stmt->bindParam(":descricao", $dados['descricao']);
        $stmt->bindParam(":tipo", $dados['tipo']);
        $stmt->bindParam(":endereco", $dados['endereco']);
        $stmt->bindParam(":bairro", $dados['bairro']);
        $stmt->bindParam(":cidade", $dados['cidade']);
        $stmt->bindParam(":estado", $dados['estado']);
        $stmt->bindParam(":cep", $dados['cep']);
        $stmt->bindParam(":valor", $dados['valor']);
        $stmt->bindParam(":area", $dados['area']);
        $stmt->bindParam(":quartos", $dados['quartos']);
        $stmt->bindParam(":banheiros", $dados['banheiros']);
        $stmt->bindParam(":vagas", $dados['vagas']);
        $stmt->bindParam(":status", $dados['status']);
        $stmt->bindParam(":destaque", $dados['destaque']);
        $stmt->bindParam(":id_proprietario", $dados['id_proprietario']);
        $stmt->bindParam(":id", $id);
        
        $resultado = $stmt->execute();
        
        if($resultado) {
            $this->registrarLog($dados['id_proprietario'], 'atualizacao_imovel', "Imóvel ID $id atualizado");
        }
        
        return $resultado;
    }
    
    // Função para excluir imóvel
    public function excluirImovel($id) {
        $query = "DELETE FROM imoveis WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        
        return $stmt->execute();
    }
    
    // Função para buscar cidades disponíveis
    public function getCidades() {
        $query = "SELECT DISTINCT cidade FROM imoveis WHERE cidade IS NOT NULL ORDER BY cidade";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    // Função para buscar bairros disponíveis
    public function getBairros($cidade = null) {
        $query = "SELECT DISTINCT bairro FROM imoveis WHERE bairro IS NOT NULL";
        
        if($cidade) {
            $query .= " AND cidade = :cidade";
        }
        
        $query .= " ORDER BY bairro";
        
        $stmt = $this->conn->prepare($query);
        
        if($cidade) {
            $stmt->bindParam(":cidade", $cidade);
        }
        
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    // Função para obter estatísticas de imóveis
    public function getEstatisticasImoveis() {
        $query = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'disponivel' THEN 1 ELSE 0 END) as disponiveis,
                    SUM(CASE WHEN status = 'alugado' THEN 1 ELSE 0 END) as alugados,
                    SUM(CASE WHEN status = 'vendido' THEN 1 ELSE 0 END) as vendidos,
                    SUM(CASE WHEN destaque = 1 THEN 1 ELSE 0 END) as destaques,
                    AVG(valor) as media_valor
                  FROM imoveis";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // ==================== AGENDAMENTOS ====================
    
    // Função para criar agendamento
    public function criarAgendamento($id_imovel, $id_cliente, $data_visita, $observacoes = '') {
        $query = "INSERT INTO agendamentos (id_imovel, id_cliente, data_visita, observacoes) 
                  VALUES (:id_imovel, :id_cliente, :data_visita, :observacoes)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":id_imovel", $id_imovel);
        $stmt->bindParam(":id_cliente", $id_cliente);
        $stmt->bindParam(":data_visita", $data_visita);
        $stmt->bindParam(":observacoes", $observacoes);
        
        $resultado = $stmt->execute();
        
        if($resultado) {
            $this->registrarLog($id_cliente, 'criacao_agendamento', "Agendamento para imóvel ID $id_imovel");
        }
        
        return $resultado;
    }
    
    // Função para listar agendamentos do usuário
    public function listarAgendamentosUsuario($id_cliente) {
        $query = "SELECT a.*, i.titulo, i.endereco, i.cidade, u.nome as cliente_nome
                  FROM agendamentos a
                  INNER JOIN imoveis i ON a.id_imovel = i.id
                  INNER JOIN usuarios u ON a.id_cliente = u.id
                  WHERE a.id_cliente = :id_cliente
                  ORDER BY a.data_visita DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_cliente", $id_cliente);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Função para listar todos os agendamentos (admin/gerente)
    public function listarTodosAgendamentos() {
        $query = "SELECT a.*, i.titulo, i.endereco, i.cidade, u.nome as cliente_nome
                  FROM agendamentos a
                  INNER JOIN imoveis i ON a.id_imovel = i.id
                  INNER JOIN usuarios u ON a.id_cliente = u.id
                  ORDER BY a.data_visita DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Função para atualizar status do agendamento
    public function atualizarStatusAgendamento($id, $status) {
        $query = "UPDATE agendamentos SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $id);
        
        $resultado = $stmt->execute();
        
        if($resultado) {
            $this->registrarLog($_SESSION['usuario_id'] ?? 0, 'atualizacao_agendamento', "Agendamento ID $id atualizado para $status");
        }
        
        return $resultado;
    }
    
    // ==================== CONTRATOS ====================
    
    // Função para criar contrato
    public function criarContrato($dados) {
        $query = "INSERT INTO contratos (id_imovel, id_cliente, id_gerente, data_inicio, data_fim, valor, status) 
                  VALUES (:id_imovel, :id_cliente, :id_gerente, :data_inicio, :data_fim, :valor, :status)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":id_imovel", $dados['id_imovel']);
        $stmt->bindParam(":id_cliente", $dados['id_cliente']);
        $stmt->bindParam(":id_gerente", $dados['id_gerente']);
        $stmt->bindParam(":data_inicio", $dados['data_inicio']);
        $stmt->bindParam(":data_fim", $dados['data_fim']);
        $stmt->bindParam(":valor", $dados['valor']);
        $stmt->bindParam(":status", $dados['status']);
        
        $resultado = $stmt->execute();
        
        if($resultado) {
            $this->registrarLog($dados['id_gerente'], 'criacao_contrato', "Contrato criado para cliente ID {$dados['id_cliente']}");
        }
        
        return $resultado;
    }
    
    // Função para listar contratos do usuário
    public function listarContratosUsuario($id_usuario, $tipo = 'cliente') {
        $query = "SELECT c.*, i.titulo, i.endereco, i.cidade, 
                         u.nome as cliente_nome, g.nome as gerente_nome
                  FROM contratos c
                  INNER JOIN imoveis i ON c.id_imovel = i.id
                  INNER JOIN usuarios u ON c.id_cliente = u.id
                  INNER JOIN usuarios g ON c.id_gerente = g.id
                  WHERE ";
        
        if($tipo == 'cliente') {
            $query .= "c.id_cliente = :id_usuario";
        } else {
            $query .= "c.id_gerente = :id_usuario";
        }
        
        $query .= " ORDER BY c.data_inicio DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_usuario", $id_usuario);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Função para listar todos os contratos (admin)
    public function listarTodosContratos() {
        $query = "SELECT c.*, i.titulo, i.endereco, i.cidade, 
                         u.nome as cliente_nome, g.nome as gerente_nome
                  FROM contratos c
                  INNER JOIN imoveis i ON c.id_imovel = i.id
                  INNER JOIN usuarios u ON c.id_cliente = u.id
                  INNER JOIN usuarios g ON c.id_gerente = g.id
                  ORDER BY c.data_inicio DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // ==================== MENSAGENS ====================
    
    // Função para enviar mensagem
    public function enviarMensagem($id_remetente, $id_destinatario, $assunto, $mensagem) {
        $query = "INSERT INTO mensagens (id_remetente, id_destinatario, assunto, mensagem) 
                  VALUES (:id_remetente, :id_destinatario, :assunto, :mensagem)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":id_remetente", $id_remetente);
        $stmt->bindParam(":id_destinatario", $id_destinatario);
        $stmt->bindParam(":assunto", $assunto);
        $stmt->bindParam(":mensagem", $mensagem);
        
        $resultado = $stmt->execute();
        
        if($resultado) {
            $this->registrarLog($id_remetente, 'envio_mensagem', "Mensagem enviada para usuário ID $id_destinatario");
        }
        
        return $resultado;
    }
    
    // Função para listar mensagens do usuário
    public function listarMensagensUsuario($id_usuario) {
        $query = "SELECT m.*, 
                         r.nome as remetente_nome, 
                         d.nome as destinatario_nome
                  FROM mensagens m
                  INNER JOIN usuarios r ON m.id_remetente = r.id
                  INNER JOIN usuarios d ON m.id_destinatario = d.id
                  WHERE m.id_remetente = :id_usuario OR m.id_destinatario = :id_usuario
                  ORDER BY m.data_envio DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_usuario", $id_usuario);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Função para marcar mensagem como lida
    public function marcarMensagemLida($id) {
        $query = "UPDATE mensagens SET lida = 1 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        
        return $stmt->execute();
    }
    
    // Função para contar mensagens não lidas
    public function contarMensagensNaoLidas($id_usuario) {
        $query = "SELECT COUNT(*) as total 
                  FROM mensagens 
                  WHERE id_destinatario = :id_usuario AND lida = 0";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_usuario", $id_usuario);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'];
    }
    
    // ==================== ESTATÍSTICAS ====================
    
    // Função para obter estatísticas gerais
    public function getEstatisticasGerais() {
        $estatisticas = [];
        
        // Total de usuários
        $query = "SELECT COUNT(*) as total FROM usuarios WHERE ativo = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $estatisticas['total_usuarios'] = $result['total'];
        
        // Total por tipo
        $query = "SELECT tipo, COUNT(*) as total FROM usuarios WHERE ativo = 1 GROUP BY tipo";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach($tipos as $tipo) {
            $estatisticas['usuarios_' . $tipo['tipo']] = $tipo['total'];
        }
        
        // Total de imóveis
        $query = "SELECT COUNT(*) as total FROM imoveis";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $estatisticas['total_imoveis'] = $result['total'];
        
        // Total de agendamentos hoje
        $query = "SELECT COUNT(*) as total FROM agendamentos WHERE DATE(data_visita) = CURDATE()";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $estatisticas['agendamentos_hoje'] = $result['total'];
        
        // Total de contratos ativos
        $query = "SELECT COUNT(*) as total FROM contratos WHERE status = 'ativo'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $estatisticas['contratos_ativos'] = $result['total'];
        
        return $estatisticas;
    }
    
    // Função para obter relatório de vendas/aluguéis
    public function getRelatorioVendas($mes = null, $ano = null) {
        $query = "SELECT 
                    DATE_FORMAT(data_inicio, '%Y-%m') as mes,
                    COUNT(*) as total_contratos,
                    SUM(valor) as valor_total
                  FROM contratos
                  WHERE 1=1";
        
        if($mes && $ano) {
            $query .= " AND YEAR(data_inicio) = :ano AND MONTH(data_inicio) = :mes";
        } elseif($ano) {
            $query .= " AND YEAR(data_inicio) = :ano";
        }
        
        $query .= " GROUP BY DATE_FORMAT(data_inicio, '%Y-%m')
                    ORDER BY mes DESC";
        
        $stmt = $this->conn->prepare($query);
        
        if($mes && $ano) {
            $stmt->bindParam(":ano", $ano);
            $stmt->bindParam(":mes", $mes);
        } elseif($ano) {
            $stmt->bindParam(":ano", $ano);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // ==================== FUNÇÕES AUXILIARES ====================
    
    // Função para gerar relatório em PDF (simulação)
    public function gerarRelatorioPDF($tipo, $dados) {
        // Esta função seria implementada com uma biblioteca de PDF
        // Por enquanto, retorna um array com os dados para o relatório
        return [
            'tipo' => $tipo,
            'data_geracao' => date('d/m/Y H:i:s'),
            'dados' => $dados
        ];
    }
    
    // Função para exportar dados para Excel (simulação)
    public function exportarParaExcel($tabela, $filtros = []) {
        // Esta função seria implementada com uma biblioteca de Excel
        // Por enquanto, retorna os dados para exportação
        $query = "SELECT * FROM $tabela";
        
        if(!empty($filtros)) {
            $query .= " WHERE 1=1";
            foreach($filtros as $campo => $valor) {
                $query .= " AND $campo = :$campo";
            }
        }
        
        $stmt = $this->conn->prepare($query);
        
        foreach($filtros as $campo => $valor) {
            $stmt->bindParam(":$campo", $valor);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Função para fazer backup do banco (simulação)
    public function fazerBackup() {
        $backup_file = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $backup_path = '../backups/' . $backup_file;
        
        // Em um sistema real, você usaria o mysqldump
        // Por enquanto, apenas retorna o caminho do arquivo
        return [
            'arquivo' => $backup_file,
            'caminho' => $backup_path,
            'data' => date('d/m/Y H:i:s'),
            'tamanho' => '0KB' // Seria calculado no sistema real
        ];
    }
    
    // Função para validar CPF
    public function validarCPF($cpf) {
        // Remove caracteres não numéricos
        $cpf = preg_replace('/[^0-9]/', '', $cpf);
        
        // Verifica se tem 11 dígitos
        if(strlen($cpf) != 11) {
            return false;
        }
        
        // Verifica se é uma sequência de números repetidos
        if(preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }
        
        // Validação do CPF
        for($t = 9; $t < 11; $t++) {
            for($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if($cpf[$c] != $d) {
                return false;
            }
        }
        
        return true;
    }
    
    // Função para validar RG
    public function validarRG($rg) {
        // Remove caracteres não numéricos
        $rg = preg_replace('/[^0-9]/', '', $rg);
        
        // Verifica se tem entre 8 e 12 dígitos (varia por estado)
        if(strlen($rg) < 8 || strlen($rg) > 12) {
            return false;
        }
        
        return true;
    }
    
    // Função para formatar telefone
    public function formatarTelefone($telefone) {
        $telefone = preg_replace('/[^0-9]/', '', $telefone);
        
        if(strlen($telefone) == 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $telefone);
        } elseif(strlen($telefone) == 10) {
            return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $telefone);
        }
        
        return $telefone;
    }
    
    // Função para formatar valor monetário
    public function formatarMoeda($valor) {
        return 'R$ ' . number_format($valor, 2, ',', '.');
    }
    
    // Função para gerar senha aleatória
    public function gerarSenha($tamanho = 8) {
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
        $senha = '';
        
        for($i = 0; $i < $tamanho; $i++) {
            $senha .= $caracteres[rand(0, strlen($caracteres) - 1)];
        }
        
        return $senha;
    }
    
    // Função para enviar email (simulação)
    public function enviarEmail($para, $assunto, $mensagem) {
        // Em um sistema real, você usaria PHPMailer ou similar
        // Por enquanto, apenas registra no log
        $log = "Email para: $para\nAssunto: $assunto\nMensagem: $mensagem\n\n";
        file_put_contents('logs/emails.log', $log, FILE_APPEND);
        
        return true;
    }
    
    // Função para log de atividades
    public function registrarLog($usuario_id, $acao, $detalhes = '') {
        $query = "INSERT INTO logs (usuario_id, acao, detalhes, ip, user_agent) 
                  VALUES (:usuario_id, :acao, :detalhes, :ip, :user_agent)";
        
        $stmt = $this->conn->prepare($query);
        
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconhecido';
        
        $stmt->bindParam(":usuario_id", $usuario_id);
        $stmt->bindParam(":acao", $acao);
        $stmt->bindParam(":detalhes", $detalhes);
        $stmt->bindParam(":ip", $ip);
        $stmt->bindParam(":user_agent", $user_agent);
        
        return $stmt->execute();
    }
    
    // Função para obter logs recentes
    public function getLogsRecentes($limite = 50) {
        $query = "SELECT l.*, u.nome as usuario_nome 
                  FROM logs l
                  LEFT JOIN usuarios u ON l.usuario_id = u.id
                  ORDER BY l.data_registro DESC
                  LIMIT :limite";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":limite", $limite, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Função para limpar logs antigos (mais de 30 dias)
    public function limparLogsAntigos() {
        $query = "DELETE FROM logs WHERE data_registro < DATE_SUB(NOW(), INTERVAL 30 DAY)";
        $stmt = $this->conn->prepare($query);
        
        return $stmt->execute();
    }
}
?>