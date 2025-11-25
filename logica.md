-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 25-Nov-2025 às 03:16
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `gerenciador_financeiro`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `ativos`
--

CREATE TABLE `ativos` (
  `id` int(11) NOT NULL,
  `simbolo` varchar(20) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `setor` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `ativos`
--

INSERT INTO `ativos` (`id`, `simbolo`, `nome`, `tipo`, `setor`) VALUES
(1, 'PETR4', 'Petrobras', 'Ação', 'Energia'),
(2, 'VALE3', 'Vale', 'Ação', 'Mineração'),
(3, 'MXRF11', 'Maxi Renda', 'FII', 'Papel'),
(4, 'BTC', 'Bitcoin', 'Cripto', 'Tech'),
(5, 'A1KA34', 'A1KA34', 'Ação', 'Geral'),
(6, 'D2AS34', 'D2AS34', 'Ação', 'Geral');

-- --------------------------------------------------------

--
-- Estrutura da tabela `cartoes`
--

CREATE TABLE `cartoes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `ultimos4` varchar(4) NOT NULL,
  `validade` date NOT NULL,
  `bandeira` varchar(50) NOT NULL,
  `tipo` varchar(20) NOT NULL,
  `limite` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `cartoes`
--

INSERT INTO `cartoes` (`id`, `usuario_id`, `nome`, `ultimos4`, `validade`, `bandeira`, `tipo`, `limite`) VALUES
(5, 7, 'dawwd', '4747', '0000-00-00', 'Mastercard', 'credito', 0.00),
(6, 7, 'dada', '4242', '4242-02-01', 'Mastercard', 'debito', 0.00);

-- --------------------------------------------------------

--
-- Estrutura da tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `tipo` enum('renda','despesa') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `categorias`
--

INSERT INTO `categorias` (`id`, `nome`, `tipo`) VALUES
(4, 'Alimentação', 'despesa'),
(11, 'Assinaturas', 'despesa'),
(10, 'Compras', 'despesa'),
(9, 'Educação', 'despesa'),
(2, 'Freelance', 'renda'),
(3, 'Investimentos', 'renda'),
(7, 'Lazer', 'despesa'),
(5, 'Moradia', 'despesa'),
(1, 'Salário', 'renda'),
(8, 'Saúde', 'despesa'),
(6, 'Transporte', 'despesa');

-- --------------------------------------------------------

--
-- Estrutura da tabela `contas`
--

CREATE TABLE `contas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `saldo_inicial` decimal(12,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `investimentos_transacoes`
--

CREATE TABLE `investimentos_transacoes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `ativo_id` int(11) NOT NULL,
  `tipo_operacao` enum('compra','venda') NOT NULL,
  `quantidade` decimal(18,8) NOT NULL,
  `valor_unitario` decimal(12,2) NOT NULL,
  `valor_total` decimal(12,2) GENERATED ALWAYS AS (`quantidade` * `valor_unitario`) STORED,
  `data_transacao` datetime NOT NULL DEFAULT current_timestamp(),
  `corretora` varchar(100) DEFAULT NULL,
  `cartao_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `metas`
--

CREATE TABLE `metas` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `valor_objetivo` decimal(12,2) NOT NULL,
  `valor_atual` decimal(12,2) DEFAULT 0.00,
  `valor_contribuicao_mensal` decimal(12,2) DEFAULT 0.00,
  `data_prazo` date DEFAULT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `cor` varchar(20) DEFAULT '#155eef',
  `historico_json` longtext DEFAULT NULL,
  `cartao_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `transacoes`
--

CREATE TABLE `transacoes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `conta_id` int(11) DEFAULT NULL,
  `categoria_id` int(11) NOT NULL,
  `meta_id` int(11) DEFAULT NULL,
  `nome` varchar(255) NOT NULL,
  `quantia` decimal(12,2) NOT NULL,
  `tipo` enum('renda','despesa') NOT NULL,
  `data_transacao` datetime NOT NULL,
  `metodo_pagamento` varchar(50) NOT NULL,
  `cartao_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha_hash` varchar(255) NOT NULL COMMENT 'Hash da senha (ex: bcrypt)',
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabela central de usuários.';

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha_hash`, `data_cadastro`) VALUES
(7, 'Mauricio', 'mauricio@teste.com', '$2y$10$OhhngCCPOQNgGlgNYpXC9.8l2pUT8/7UQcB2DW2XbdoLU4mi5nQKW', '2025-11-25 01:40:45');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `ativos`
--
ALTER TABLE `ativos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `simbolo` (`simbolo`);

--
-- Índices para tabela `cartoes`
--
ALTER TABLE `cartoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cartao_usuario` (`usuario_id`);

--
-- Índices para tabela `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_nome_tipo_global` (`nome`,`tipo`);

--
-- Índices para tabela `contas`
--
ALTER TABLE `contas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_conta_usuario` (`usuario_id`);

--
-- Índices para tabela `investimentos_transacoes`
--
ALTER TABLE `investimentos_transacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_inv_usuario` (`usuario_id`),
  ADD KEY `fk_inv_ativo` (`ativo_id`),
  ADD KEY `fk_inv_cartao` (`cartao_id`);

--
-- Índices para tabela `metas`
--
ALTER TABLE `metas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_meta_usuario` (`usuario_id`),
  ADD KEY `fk_meta_cartao` (`cartao_id`);

--
-- Índices para tabela `transacoes`
--
ALTER TABLE `transacoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_transacao_usuario` (`usuario_id`),
  ADD KEY `fk_transacao_categoria` (`categoria_id`),
  ADD KEY `fk_transacoes_metas_nova_v2` (`meta_id`),
  ADD KEY `fk_transacao_cartao` (`cartao_id`);

--
-- Índices para tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_email_unico` (`email`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `ativos`
--
ALTER TABLE `ativos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `cartoes`
--
ALTER TABLE `cartoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `contas`
--
ALTER TABLE `contas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `investimentos_transacoes`
--
ALTER TABLE `investimentos_transacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `metas`
--
ALTER TABLE `metas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `transacoes`
--
ALTER TABLE `transacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `cartoes`
--
ALTER TABLE `cartoes`
  ADD CONSTRAINT `fk_cartao_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `contas`
--
ALTER TABLE `contas`
  ADD CONSTRAINT `fk_conta_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `investimentos_transacoes`
--
ALTER TABLE `investimentos_transacoes`
  ADD CONSTRAINT `fk_inv_ativo` FOREIGN KEY (`ativo_id`) REFERENCES `ativos` (`id`),
  ADD CONSTRAINT `fk_inv_cartao` FOREIGN KEY (`cartao_id`) REFERENCES `cartoes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_inv_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `metas`
--
ALTER TABLE `metas`
  ADD CONSTRAINT `fk_meta_cartao` FOREIGN KEY (`cartao_id`) REFERENCES `cartoes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_meta_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `transacoes`
--
ALTER TABLE `transacoes`
  ADD CONSTRAINT `fk_transacao_cartao` FOREIGN KEY (`cartao_id`) REFERENCES `cartoes` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_transacao_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`),
  ADD CONSTRAINT `fk_transacao_meta` FOREIGN KEY (`meta_id`) REFERENCES `metas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_transacao_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_transacoes_metas_nova_v2` FOREIGN KEY (`meta_id`) REFERENCES `metas` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
