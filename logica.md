-- Configurações iniciais
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Criação do Banco de Dados
--
CREATE DATABASE IF NOT EXISTS `gerenciador_financeiro` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `gerenciador_financeiro`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `senha_hash` varchar(255) NOT NULL COMMENT 'Hash da senha (ex: bcrypt)',
  `profile_pic_url` varchar(512) DEFAULT 'https://via.placeholder.com/40' COMMENT 'URL da foto de perfil',
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_email_unico` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabela central de usuários.';

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha_hash`, `profile_pic_url`, `data_cadastro`) VALUES
(1, 'Mauricio ', 'teste@novyx.com', '$2y$10$T.HE.f.Q.X.UqN8INsYj9e.F/yT.xK.wL3qE.y/wD.uY.zX.oK9qK', 'https://via.placeholder.com/40', '2025-11-12 14:20:58');

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias`
--

DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `tipo` enum('renda','despesa') NOT NULL COMMENT 'Define se a categoria é de entrada ou saída.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_usuario_nome_tipo_unico` (`usuario_id`,`nome`,`tipo`),
  KEY `fk_categoria_usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Etiquetas personalizadas do usuário para classificar transações.';

--
-- Despejando dados para a tabela `categorias`
--

INSERT INTO `categorias` (`id`, `usuario_id`, `nome`, `tipo`) VALUES
(3, 1, 'Alimentação', 'despesa'),
(2, 1, 'Freelance', 'renda'),
(6, 1, 'Lazer', 'despesa'),
(4, 1, 'Moradia', 'despesa'),
(1, 1, 'Salário', 'renda'),
(5, 1, 'Transporte', 'despesa');

-- --------------------------------------------------------

--
-- Estrutura para tabela `contas`
--

DROP TABLE IF EXISTS `contas`;
CREATE TABLE `contas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL COMMENT 'Apelido (Ex: "Itaú", "Nubank Crédito", "Carteira")',
  `tipo` enum('conta_corrente','cartao_credito','poupanca','investimento','dinheiro') NOT NULL COMMENT 'Define a lógica da conta',
  `saldo_inicial` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Saldo no momento do cadastro da conta',
  `ultimos4` char(4) DEFAULT NULL COMMENT 'Opcional, para cartões',
  `bandeira` varchar(50) DEFAULT NULL COMMENT 'Opcional, para cartões (Ex: Visa, Master)',
  `dia_fechamento_fatura` tinyint(2) DEFAULT NULL COMMENT 'Opcional, para cartões de crédito (1-31)',
  `dia_vencimento_fatura` tinyint(2) DEFAULT NULL COMMENT 'Opcional, para cartões de crédito (1-31)',
  PRIMARY KEY (`id`),
  KEY `fk_conta_usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `metas`
--

DROP TABLE IF EXISTS `metas`;
CREATE TABLE `metas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL COMMENT 'Nome da meta (ex: "Viagem ao Japão")',
  `valor_objetivo` decimal(12,2) NOT NULL COMMENT 'Valor total da meta',
  `valor_atual` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Valor atual economizado',
  `valor_contribuicao_mensal` decimal(12,2) DEFAULT NULL COMMENT 'Valor de contribuição *planejado*',
  `data_prazo` date DEFAULT NULL COMMENT 'Data limite para alcançar a meta',
  `categoria` varchar(50) DEFAULT NULL COMMENT 'Tag de categoria (ex: lazer, moradia)',
  PRIMARY KEY (`id`),
  KEY `fk_meta_usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `metas`
--

INSERT INTO `metas` (`id`, `usuario_id`, `nome`, `valor_objetivo`, `valor_atual`, `valor_contribuicao_mensal`, `data_prazo`, `categoria`) VALUES
(1, 1, 'Viagem de Férias', 10000.00, 3500.00, NULL, '2026-12-01', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `investimentos`
--

DROP TABLE IF EXISTS `investimentos`;
CREATE TABLE `investimentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `nome_ativo` varchar(100) NOT NULL COMMENT 'Ticker ou nome (Ex: "PETR4", "Bitcoin")',
  `tipo_ativo` enum('acao','fii','bdr','tesouro_direto','criptomoeda','outro') NOT NULL,
  `corretora` varchar(100) DEFAULT NULL COMMENT 'Onde o ativo está custodiado (Ex: "XP")',
  `data_operacao` date NOT NULL COMMENT 'Data da execução da compra ou venda',
  `tipo_operacao` enum('compra','venda') NOT NULL,
  `quantidade` decimal(18,8) NOT NULL COMMENT 'Quantidade (alta precisão para cripto)',
  `valor_unitario` decimal(12,2) NOT NULL COMMENT 'Preço por unidade na operação',
  `custos_operacao` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Taxas, emolumentos',
  PRIMARY KEY (`id`),
  KEY `fk_investimento_usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Histórico de operações de investimento (compra/venda de ativos).';

-- --------------------------------------------------------

--
-- Estrutura para tabela `transacoes`
--

DROP TABLE IF EXISTS `transacoes`;
CREATE TABLE `transacoes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `conta_id` int(11) DEFAULT NULL COMMENT 'Conta (Opcional na visão global)',
  `categoria_id` int(11) NOT NULL COMMENT 'Categoria da transação (ex: Alimentação)',
  `meta_id` int(11) DEFAULT NULL COMMENT 'ID da meta (se for uma contribuição)',
  `nome` varchar(255) NOT NULL COMMENT 'Descrição (ex: "Supermercado")',
  `quantia` decimal(12,2) NOT NULL COMMENT 'Valor sempre positivo',
  `tipo` enum('renda','despesa') NOT NULL COMMENT 'Define se a quantia soma ou subtrai',
  `data_transacao` datetime NOT NULL COMMENT 'Data e hora da transação',
  `metodo_pagamento` varchar(50) NOT NULL COMMENT 'Ex: "Pix", "Cartão de Crédito"',
  PRIMARY KEY (`id`),
  KEY `idx_usuario_conta` (`usuario_id`,`conta_id`),
  KEY `idx_data_transacao` (`data_transacao`),
  KEY `fk_transacao_conta` (`conta_id`),
  KEY `fk_transacao_categoria` (`categoria_id`),
  KEY `fk_transacao_meta` (`meta_id`),
  CONSTRAINT `fk_transacao_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`),
  CONSTRAINT `fk_transacao_conta` FOREIGN KEY (`conta_id`) REFERENCES `contas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_transacao_meta` FOREIGN KEY (`meta_id`) REFERENCES `metas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_transacao_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Registro de todas as rendas e despesas, vinculadas a uma Conta.';

--
-- Despejando dados para a tabela `transacoes`
--

INSERT INTO `transacoes` (`id`, `usuario_id`, `conta_id`, `categoria_id`, `meta_id`, `nome`, `quantia`, `tipo`, `data_transacao`, `metodo_pagamento`) VALUES
(13, 1, NULL, 1, NULL, 'Salário Empresa X', 5000.00, 'renda', '2025-11-05 09:00:00', 'Pix'),
(14, 1, NULL, 2, NULL, 'Projeto Y', 1500.00, 'renda', '2025-11-10 14:30:00', 'Transferência'),
(16, 1, NULL, 6, NULL, 'Spotify', 10.00, 'despesa', '2025-11-08 17:15:00', 'Cartão de Débito'),
(17, 1, NULL, 3, NULL, 'Supermercado', 450.00, 'despesa', '2025-11-09 11:05:00', 'Cartão de Crédito'),
(18, 1, NULL, 6, 1, 'Contribuição Meta Viagem', 500.00, 'despesa', '2025-11-10 18:00:00', 'Pix'),
(19, 1, NULL, 2, NULL, 'Salario', 100.00, 'renda', '2025-11-17 11:24:00', 'Pix'),
(22, 1, NULL, 5, NULL, 'carro', -150.00, 'despesa', '2025-11-17 11:28:00', 'Pix'),
(23, 1, NULL, 6, NULL, 'casa', -10.00, 'despesa', '2025-11-17 11:30:00', 'Pix'),
(24, 1, NULL, 4, NULL, 'Apartamento', -500.00, 'despesa', '2025-11-17 11:30:00', 'Cartão de Crédito'),
(25, 1, NULL, 2, NULL, 'Sla', 150.00, 'renda', '2025-02-11 05:00:00', 'Cartão de Crédito');

--
-- Restrições (Constraints) Adicionais
--

ALTER TABLE `categorias`
  ADD CONSTRAINT `fk_categoria_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `contas`
  ADD CONSTRAINT `fk_conta_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `investimentos`
  ADD CONSTRAINT `fk_investimento_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `metas`
  ADD CONSTRAINT `fk_meta_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;