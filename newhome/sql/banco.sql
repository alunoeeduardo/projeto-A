-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS newhome_db;
USE newhome_db;

-- Tabela de usuários
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('cliente', 'gerente', 'admin') DEFAULT 'cliente',
    telefone VARCHAR(20),
    cpf VARCHAR(14),
    data_nascimento DATE,
    endereco TEXT,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ativo BOOLEAN DEFAULT TRUE,
    ultimo_login TIMESTAMP NULL
);

-- Tabela de imóveis
CREATE TABLE imoveis (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(200) NOT NULL,
    descricao TEXT,
    tipo ENUM('casa', 'apartamento', 'sobrado', 'kitnet', 'terreno') NOT NULL,
    endereco TEXT NOT NULL,
    bairro VARCHAR(100),
    cidade VARCHAR(100),
    estado VARCHAR(2),
    cep VARCHAR(9),
    valor DECIMAL(10,2) NOT NULL,
    area DECIMAL(8,2),
    quartos INT,
    banheiros INT,
    vagas INT,
    status ENUM('disponivel', 'alugado', 'vendido', 'manutencao') DEFAULT 'disponivel',
    destaque BOOLEAN DEFAULT FALSE,
    fotos TEXT, -- JSON com URLs das fotos
    id_proprietario INT,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_proprietario) REFERENCES usuarios(id)
);

-- Tabela de agendamentos
CREATE TABLE agendamentos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_imovel INT NOT NULL,
    id_cliente INT NOT NULL,
    data_visita DATETIME NOT NULL,
    status ENUM('pendente', 'confirmado', 'cancelado', 'realizado') DEFAULT 'pendente',
    observacoes TEXT,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_imovel) REFERENCES imoveis(id),
    FOREIGN KEY (id_cliente) REFERENCES usuarios(id)
);

-- Tabela de contratos
CREATE TABLE contratos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_imovel INT NOT NULL,
    id_cliente INT NOT NULL,
    id_gerente INT NOT NULL,
    data_inicio DATE NOT NULL,
    data_fim DATE,
    valor DECIMAL(10,2) NOT NULL,
    status ENUM('ativo', 'encerrado', 'cancelado') DEFAULT 'ativo',
    documento_path VARCHAR(255),
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_imovel) REFERENCES imoveis(id),
    FOREIGN KEY (id_cliente) REFERENCES usuarios(id),
    FOREIGN KEY (id_gerente) REFERENCES usuarios(id)
);

-- Tabela de mensagens
CREATE TABLE mensagens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_remetente INT NOT NULL,
    id_destinatario INT NOT NULL,
    assunto VARCHAR(200),
    mensagem TEXT NOT NULL,
    lida BOOLEAN DEFAULT FALSE,
    data_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_remetente) REFERENCES usuarios(id),
    FOREIGN KEY (id_destinatario) REFERENCES usuarios(id)
);

-- Inserir usuário admin padrão (senha: admin123)
INSERT INTO usuarios (nome, email, senha, tipo, telefone, cpf) 
VALUES ('Administrador', 'admin@newhome.com', '$2y$10$YourHashedPasswordHere', 'admin', '(11) 99999-9999', '123.456.789-00');

-- Inserir alguns usuários de exemplo
INSERT INTO usuarios (nome, email, senha, tipo, telefone) VALUES
('João Silva', 'joao@email.com', '$2y$10$hash1', 'cliente', '(11) 98888-8888'),
('Maria Santos', 'maria@email.com', '$2y$10$hash2', 'gerente', '(11) 97777-7777'),
('Carlos Oliveira', 'carlos@email.com', '$2y$10$hash3', 'cliente', '(11) 96666-6666');

-- Inserir alguns imóveis de exemplo
INSERT INTO imoveis (titulo, descricao, tipo, endereco, cidade, valor, quartos, banheiros, vagas, status) VALUES
('Apartamento Moderno Centro', 'Apartamento bem localizado com 2 quartos, sala ampla, cozinha americana.', 'apartamento', 'Rua das Flores, 123', 'São Paulo', 250000.00, 2, 2, 1, 'disponivel'),
('Casa com Piscina', 'Casa espaçosa com 4 quartos, piscina, área de churrasco e jardim.', 'casa', 'Av. Paulista, 1000', 'São Paulo', 850000.00, 4, 3, 2, 'disponivel'),
('Kitnet Estudantil', 'Kitnet mobiliada próxima à universidade, ideal para estudantes.', 'kitnet', 'Rua das Acácias, 45', 'São Paulo', 120000.00, 1, 1, 0, 'disponivel');

-- Tabela de logs (se ainda não existir)
CREATE TABLE IF NOT EXISTS logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    acao VARCHAR(100) NOT NULL,
    detalhes TEXT,
    ip VARCHAR(45),
    user_agent TEXT,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Inserir alguns dados de exemplo para testar
INSERT INTO imoveis (titulo, descricao, tipo, endereco, cidade, estado, valor, quartos, banheiros, vagas, status, destaque) VALUES
('Apartamento Moderno Centro', 'Apartamento bem localizado com 2 quartos, sala ampla, cozinha americana.', 'apartamento', 'Rua das Flores, 123', 'São Paulo', 'SP', 250000.00, 2, 2, 1, 'disponivel', 1),
('Casa com Piscina', 'Casa espaçosa com 4 quartos, piscina, área de churrasco e jardim.', 'casa', 'Av. Paulista, 1000', 'São Paulo', 'SP', 850000.00, 4, 3, 2, 'disponivel', 1),
('Kitnet Estudantil', 'Kitnet mobiliada próxima à universidade, ideal para estudantes.', 'kitnet', 'Rua das Acácias, 45', 'São Paulo', 'SP', 120000.00, 1, 1, 0, 'disponivel', 0),
('Sobrado Moderno', 'Sobrado com 3 quartos, 3 banheiros, 2 vagas de garagem.', 'sobrado', 'Rua das Palmeiras, 789', 'Rio de Janeiro', 'RJ', 550000.00, 3, 3, 2, 'disponivel', 1),
('Terreno Residencial', 'Terreno plano de 500m² em ótima localização para construção.', 'terreno', 'Av. Brasil, 1500', 'Belo Horizonte', 'MG', 300000.00, 0, 0, 0, 'disponivel', 0);

-- Atualizar senha do admin para algo mais seguro
UPDATE usuarios SET senha = '$2y$10$HxG8K9LmNpQrS7T1UvW2XeYzA0B1C2D3E4F5G6H7I8J9K0L1M2N3O4P5Q' WHERE email = 'admin@newhome.com';
-- Senha: admin123 



-- Banco de dados atualizado para suportar novos campos
ALTER TABLE usuarios 
ADD COLUMN rg VARCHAR(20),
ADD COLUMN profissao VARCHAR(100),
ADD COLUMN estado_civil ENUM('solteiro', 'casado', 'divorciado', 'viuvo') DEFAULT 'solteiro',
ADD COLUMN foto_documento_path VARCHAR(255),
ADD COLUMN selfie_documento_path VARCHAR(255),
ADD COLUMN cep VARCHAR(9),
ADD COLUMN numero_endereco VARCHAR(10),
ADD COLUMN complemento VARCHAR(100),
ADD COLUMN foto_perfil VARCHAR(255);

-- Adicionar colunas específicas para gerente
ALTER TABLE usuarios
ADD COLUMN data_contratacao DATE,
ADD COLUMN salario DECIMAL(10,2),
ADD COLUMN setor VARCHAR(100);

-- Criar tabela para armazenar histórico de fotos/documentos
CREATE TABLE IF NOT EXISTS documentos_usuario (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT NOT NULL,
    tipo ENUM('rg_frente', 'rg_verso', 'cnh_frente', 'cnh_verso', 'selfie', 'comprovante_endereco') NOT NULL,
    caminho_arquivo VARCHAR(255) NOT NULL,
    data_upload TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    aprovado BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);