-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 03/03/2026 às 00:16
-- Versão do servidor: 10.4.24-MariaDB
-- Versão do PHP: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `newhome_db`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `agendamentos`
--

CREATE TABLE `agendamentos` (
  `id` int(11) NOT NULL,
  `id_imovel` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `data_visita` datetime NOT NULL,
  `status` enum('pendente','confirmado','cancelado','realizado') DEFAULT 'pendente',
  `observacoes` text DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `contratos`
--

CREATE TABLE `contratos` (
  `id` int(11) NOT NULL,
  `id_imovel` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_gerente` int(11) NOT NULL,
  `data_inicio` date NOT NULL,
  `data_fim` date DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `status` enum('ativo','encerrado','cancelado') DEFAULT 'ativo',
  `documento_path` varchar(255) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `documentos_usuario`
--

CREATE TABLE `documentos_usuario` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo` enum('rg_frente','rg_verso','cnh_frente','cnh_verso','selfie','comprovante_endereco') NOT NULL,
  `caminho_arquivo` varchar(255) NOT NULL,
  `data_upload` timestamp NOT NULL DEFAULT current_timestamp(),
  `aprovado` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `imoveis`
--

CREATE TABLE `imoveis` (
  `id` int(11) NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `descricao` text DEFAULT NULL,
  `tipo` enum('casa','apartamento','sobrado','kitnet','terreno') NOT NULL,
  `endereco` text NOT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `cidade` varchar(100) DEFAULT NULL,
  `estado` varchar(2) DEFAULT NULL,
  `cep` varchar(9) DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `area` decimal(8,2) DEFAULT NULL,
  `quartos` int(11) DEFAULT NULL,
  `banheiros` int(11) DEFAULT NULL,
  `vagas` int(11) DEFAULT NULL,
  `status` enum('disponivel','alugado','vendido','manutencao') DEFAULT 'disponivel',
  `destaque` tinyint(1) DEFAULT 0,
  `fotos` text DEFAULT NULL,
  `id_proprietario` int(11) DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Despejando dados para a tabela `imoveis`
--

INSERT INTO `imoveis` (`id`, `titulo`, `descricao`, `tipo`, `endereco`, `bairro`, `cidade`, `estado`, `cep`, `valor`, `area`, `quartos`, `banheiros`, `vagas`, `status`, `destaque`, `fotos`, `id_proprietario`, `data_cadastro`) VALUES
(1, 'Apartamento Moderno Centro', 'Apartamento bem localizado com 2 quartos, sala ampla, cozinha americana.', 'apartamento', 'Rua das Flores, 123', NULL, 'São Paulo', NULL, NULL, 250000.00, NULL, 2, 2, 1, 'disponivel', 0, NULL, NULL, '2026-03-02 22:59:35'),
(2, 'Casa com Piscina', 'Casa espaçosa com 4 quartos, piscina, área de churrasco e jardim.', 'casa', 'Av. Paulista, 1000', NULL, 'São Paulo', NULL, NULL, 850000.00, NULL, 4, 3, 2, 'disponivel', 0, NULL, NULL, '2026-03-02 22:59:35'),
(3, 'Kitnet Estudantil', 'Kitnet mobiliada próxima à universidade, ideal para estudantes.', 'kitnet', 'Rua das Acácias, 45', NULL, 'São Paulo', NULL, NULL, 120000.00, NULL, 1, 1, 0, 'disponivel', 0, NULL, NULL, '2026-03-02 22:59:35'),
(4, 'Apartamento Moderno Centro', 'Apartamento bem localizado com 2 quartos, sala ampla, cozinha americana.', 'apartamento', 'Rua das Flores, 123', NULL, 'São Paulo', 'SP', NULL, 250000.00, NULL, 2, 2, 1, 'disponivel', 1, NULL, NULL, '2026-03-02 22:59:35'),
(5, 'Casa com Piscina', 'Casa espaçosa com 4 quartos, piscina, área de churrasco e jardim.', 'casa', 'Av. Paulista, 1000', NULL, 'São Paulo', 'SP', NULL, 850000.00, NULL, 4, 3, 2, 'disponivel', 1, NULL, NULL, '2026-03-02 22:59:35'),
(6, 'Kitnet Estudantil', 'Kitnet mobiliada próxima à universidade, ideal para estudantes.', 'kitnet', 'Rua das Acácias, 45', NULL, 'São Paulo', 'SP', NULL, 120000.00, NULL, 1, 1, 0, 'disponivel', 0, NULL, NULL, '2026-03-02 22:59:35'),
(7, 'Sobrado Moderno', 'Sobrado com 3 quartos, 3 banheiros, 2 vagas de garagem.', 'sobrado', 'Rua das Palmeiras, 789', NULL, 'Rio de Janeiro', 'RJ', NULL, 550000.00, NULL, 3, 3, 2, 'disponivel', 1, NULL, NULL, '2026-03-02 22:59:35'),
(8, 'Terreno Residencial', 'Terreno plano de 500m² em ótima localização para construção.', 'terreno', 'Av. Brasil, 1500', NULL, 'Belo Horizonte', 'MG', NULL, 300000.00, NULL, 0, 0, 0, 'disponivel', 0, NULL, NULL, '2026-03-02 22:59:35');

-- --------------------------------------------------------

--
-- Estrutura para tabela `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `acao` varchar(100) NOT NULL,
  `detalhes` text DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `data_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Despejando dados para a tabela `logs`
--

INSERT INTO `logs` (`id`, `usuario_id`, `acao`, `detalhes`, `ip`, `user_agent`, `data_registro`) VALUES
(1, 5, 'cadastro_cliente', 'Usuário cadastrado com sucesso', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-02 23:04:25'),
(2, 5, 'login', 'Login realizado com sucesso', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-02 23:04:38'),
(3, 1, 'login', 'Login realizado com sucesso', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', '2026-03-02 23:10:52');

-- --------------------------------------------------------

--
-- Estrutura para tabela `mensagens`
--

CREATE TABLE `mensagens` (
  `id` int(11) NOT NULL,
  `id_remetente` int(11) NOT NULL,
  `id_destinatario` int(11) NOT NULL,
  `assunto` varchar(200) DEFAULT NULL,
  `mensagem` text NOT NULL,
  `lida` tinyint(1) DEFAULT 0,
  `data_envio` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo` enum('cliente','gerente','admin') DEFAULT 'cliente',
  `telefone` varchar(20) DEFAULT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `data_nascimento` date DEFAULT NULL,
  `endereco` text DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `ativo` tinyint(1) DEFAULT 1,
  `ultimo_login` timestamp NULL DEFAULT NULL,
  `rg` varchar(20) DEFAULT NULL,
  `profissao` varchar(100) DEFAULT NULL,
  `estado_civil` enum('solteiro','casado','divorciado','viuvo') DEFAULT 'solteiro',
  `foto_documento_path` varchar(255) DEFAULT NULL,
  `selfie_documento_path` varchar(255) DEFAULT NULL,
  `cep` varchar(9) DEFAULT NULL,
  `numero_endereco` varchar(10) DEFAULT NULL,
  `complemento` varchar(100) DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `data_contratacao` date DEFAULT NULL,
  `salario` decimal(10,2) DEFAULT NULL,
  `setor` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `tipo`, `telefone`, `cpf`, `data_nascimento`, `endereco`, `data_cadastro`, `ativo`, `ultimo_login`, `rg`, `profissao`, `estado_civil`, `foto_documento_path`, `selfie_documento_path`, `cep`, `numero_endereco`, `complemento`, `foto_perfil`, `data_contratacao`, `salario`, `setor`) VALUES
(1, 'Administrador', 'admin@newhome.com', '$2y$10$HC.GdhrU2n4P473/nrMR8ePJQQNqE/MiiDSZc6hQhTof1Il66OGl2', 'admin', '(11) 99999-9999', '123.456.789-00', NULL, NULL, '2026-03-02 22:59:34', 1, '2026-03-02 23:10:52', NULL, NULL, 'solteiro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'João Silva', 'joao@email.com', '$2y$10$hash1', 'cliente', '(11) 98888-8888', NULL, NULL, NULL, '2026-03-02 22:59:35', 1, NULL, NULL, NULL, 'solteiro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'Maria Santos', 'maria@email.com', '$2y$10$hash2', 'gerente', '(11) 97777-7777', NULL, NULL, NULL, '2026-03-02 22:59:35', 1, NULL, NULL, NULL, 'solteiro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'Carlos Oliveira', 'carlos@email.com', '$2y$10$hash3', 'cliente', '(11) 96666-6666', NULL, NULL, NULL, '2026-03-02 22:59:35', 1, NULL, NULL, NULL, 'solteiro', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'teste', 'teste@teste', '$2y$10$HC.GdhrU2n4P473/nrMR8ePJQQNqE/MiiDSZc6hQhTof1Il66OGl2', 'cliente', '(00) 00000-0000', '090.000.000-00', '1919-12-09', '000000000000000000000000000000000000000000000000000000', '2026-03-02 23:04:25', 1, '2026-03-02 23:04:38', '00.000.000-0', 'teste', 'solteiro', 'uploads/documentos/2026/03/documento_09000000000_1772492665.png', 'uploads/documentos/2026/03/selfie_09000000000_1772492665.png', '00000-000', '0000000000', '000000000000000000000000', NULL, NULL, NULL, NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_imovel` (`id_imovel`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Índices de tabela `contratos`
--
ALTER TABLE `contratos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_imovel` (`id_imovel`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_gerente` (`id_gerente`);

--
-- Índices de tabela `documentos_usuario`
--
ALTER TABLE `documentos_usuario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `imoveis`
--
ALTER TABLE `imoveis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_proprietario` (`id_proprietario`);

--
-- Índices de tabela `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `mensagens`
--
ALTER TABLE `mensagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_remetente` (`id_remetente`),
  ADD KEY `id_destinatario` (`id_destinatario`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `contratos`
--
ALTER TABLE `contratos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `documentos_usuario`
--
ALTER TABLE `documentos_usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `imoveis`
--
ALTER TABLE `imoveis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `mensagens`
--
ALTER TABLE `mensagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD CONSTRAINT `agendamentos_ibfk_1` FOREIGN KEY (`id_imovel`) REFERENCES `imoveis` (`id`),
  ADD CONSTRAINT `agendamentos_ibfk_2` FOREIGN KEY (`id_cliente`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `contratos`
--
ALTER TABLE `contratos`
  ADD CONSTRAINT `contratos_ibfk_1` FOREIGN KEY (`id_imovel`) REFERENCES `imoveis` (`id`),
  ADD CONSTRAINT `contratos_ibfk_2` FOREIGN KEY (`id_cliente`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `contratos_ibfk_3` FOREIGN KEY (`id_gerente`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `documentos_usuario`
--
ALTER TABLE `documentos_usuario`
  ADD CONSTRAINT `documentos_usuario_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `imoveis`
--
ALTER TABLE `imoveis`
  ADD CONSTRAINT `imoveis_ibfk_1` FOREIGN KEY (`id_proprietario`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `mensagens`
--
ALTER TABLE `mensagens`
  ADD CONSTRAINT `mensagens_ibfk_1` FOREIGN KEY (`id_remetente`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `mensagens_ibfk_2` FOREIGN KEY (`id_destinatario`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
