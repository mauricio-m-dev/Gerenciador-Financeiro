-- Estrutura para tabela `metas`
--

CREATE TABLE `metas` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `categoria` varchar(255) NOT NULL,
  `objetivo` decimal(10,0) NOT NULL,
  `acumulado` decimal(10,0) NOT NULL,
  `prazo` date NOT NULL,
  `cor` varchar(255) NOT NULL,
  `mensal` decimal(10,0) NOT NULL,
  `historico_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`historico_json`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


ALTER TABLE `metas`
  ADD PRIMARY KEY (`id`);