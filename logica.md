-- Criação do Banco de Dados (Opcional, execute se necessário)
-- CREATE DATABASE novyx_db;
-- USE novyx_db;

-- -----------------------------------------------------
-- Tabela: Usuarios
-- Armazena os usuários do sistema.
-- -----------------------------------------------------
CREATE TABLE Usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID único do usuário',
    nome VARCHAR(255) NOT NULL COMMENT 'Nome completo do usuário',
    email VARCHAR(255) NOT NULL UNIQUE COMMENT 'E-mail de login, deve ser único',
    senha VARCHAR(255) NOT NULL COMMENT 'Senha com hash (ex: bcrypt)',
    data_cadastro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data de registro'
) COMMENT = 'Tabela de usuários e autenticação';

-- -----------------------------------------------------
-- Tabela: Categorias
-- Tabela de consulta para classificar transações (Moradia, Lazer, etc). 
-- -----------------------------------------------------
CREATE TABLE Categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE COMMENT 'Nome da categoria (ex: Moradia)'
) COMMENT = 'Categorias de transações (Renda/Despesa)';

-- -----------------------------------------------------
-- Tabela: MetodosPagamento
-- Tabela de consulta para métodos de pagamento (Pix, Dinheiro, etc).
-- -----------------------------------------------------
CREATE TABLE MetodosPagamento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE COMMENT 'Nome do método (ex: Pix, Cartão de Crédito)'
) COMMENT = 'Métodos de pagamento disponíveis';

-- -----------------------------------------------------
-- Tabela: Cartoes
-- Armazena os cartões de crédito/débito do usuário.
-- -----------------------------------------------------
CREATE TABLE Cartoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL COMMENT 'Chave estrangeira para Usuarios.id',
    nome VARCHAR(100) NOT NULL COMMENT 'Apelido do cartão (ex: Meu Visa)',
    ultimos4 VARCHAR(4) NOT NULL COMMENT 'Últimos 4 dígitos do cartão',
    bandeira VARCHAR(50) NULL COMMENT 'Bandeira (ex: Visa, Mastercard)',
    tipo ENUM('credito', 'debito') NOT NULL COMMENT 'Tipo do cartão',
    
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id)
        ON DELETE CASCADE -- Se o usuário for deletado, seus cartões também são.
) COMMENT = 'Cartões de crédito e débito dos usuários';

-- -----------------------------------------------------
-- Tabela: Transacoes
-- Tabela principal, armazena todas as rendas e despesas.
-- -----------------------------------------------------
CREATE TABLE Transacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL COMMENT 'Chave estrangeira para Usuarios.id',
    id_categoria INT NOT NULL COMMENT 'Chave estrangeira para Categorias.id',
    id_metodo_pagamento INT NOT NULL COMMENT 'Chave estrangeira para MetodosPagamento.id',
    
    nome VARCHAR(255) NOT NULL COMMENT 'Descrição da transação (ex: Spotify, Aluguel)',
    quantia DECIMAL(10, 2) NOT NULL COMMENT 'Valor da transação',
    tipo ENUM('renda', 'despesa') NOT NULL COMMENT 'Tipo da transação',
    data DATE NOT NULL COMMENT 'Data em que a transação ocorreu',
    data_criacao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data de registro no sistema',
    
    -- Campo opcional sugerido na Etapa 5
    id_cartao INT NULL COMMENT 'Se o método for cartão, aponta para o Cartoes.id',

    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id)
        ON DELETE CASCADE, -- Se o usuário for deletado, suas transações também são.
    FOREIGN KEY (id_categoria) REFERENCES Categorias(id)
        ON DELETE RESTRICT, -- Impede deletar categoria em uso
    FOREIGN KEY (id_metodo_pagamento) REFERENCES MetodosPagamento(id)
        ON DELETE RESTRICT, -- Impede deletar método em uso
    FOREIGN KEY (id_cartao) REFERENCES Cartoes(id)
        ON DELETE SET NULL -- Se o cartão for deletado, a transação mantém o registro.
) COMMENT = 'Registro central de todas as transações financeiras';

-- -----------------------------------------------------
-- Tabela: CategoriasMeta
-- Tabela de consulta para classificar Metas (Lazer, Veículo, etc).
-- -----------------------------------------------------
CREATE TABLE CategoriasMeta (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE COMMENT 'Nome da categoria (ex: Lazer, Veículo)'
) COMMENT = 'Categorias para metas financeiras';

-- -----------------------------------------------------
-- Tabela: Metas
-- Armazena as metas financeiras dos usuários.
-- -----------------------------------------------------
CREATE TABLE Metas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL COMMENT 'Chave estrangeira para Usuarios.id',
    id_categoria_meta INT NOT NULL COMMENT 'Chave estrangeira para CategoriasMeta.id',
    nome VARCHAR(255) NOT NULL COMMENT 'Nome da meta (ex: Viagem ao Japão)',
    valor_objetivo DECIMAL(10, 2) NOT NULL COMMENT 'Valor total desejado',
    valor_atual DECIMAL(10, 2) NOT NULL DEFAULT 0.00 COMMENT 'Valor já economizado',
    valor_contribuicao_mensal DECIMAL(10, 2) NULL COMMENT 'Contribuição mensal planejada',
    data_prazo DATE NULL COMMENT 'Data limite para atingir a meta',
    data_criacao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id)
        ON DELETE CASCADE, -- Se o usuário for deletado, suas metas também são.
    FOREIGN KEY (id_categoria_meta) REFERENCES CategoriasMeta(id)
        ON DELETE RESTRICT -- Impede deletar categoria de meta em uso
) COMMENT = 'Metas financeiras (objetivos) dos usuários';

-- -----------------------------------------------------
-- Inserção de Dados Iniciais (Lookup Tables)
-- -----------------------------------------------------

-- Categorias de Transação (de VisaoGeral.php)
INSERT INTO Categorias (nome) VALUES
('Moradia'), ('Alimentação'), ('Transporte'), ('Lazer'),
('Saúde'), ('Educação'), ('Investimentos'), ('Outros');

-- Métodos de Pagamento (de VisaoGeral.php)
INSERT INTO MetodosPagamento (nome) VALUES
('Pix'), ('Cartão de Crédito'), ('Cartão de Débito'),
('Boleto'), ('Transferência'), ('Dinheiro');

-- Categorias de Meta (de Meta.php)
INSERT INTO CategoriasMeta (nome) VALUES
('Segurança'), ('Lazer'), ('Moradia'), ('Veículo'), ('Educação');

-- -----------------------------------------------------
-- Criação de Índices para Otimização
-- -----------------------------------------------------
CREATE INDEX idx_transacoes_usuario ON Transacoes (id_usuario);
CREATE INDEX idx_transacoes_data ON Transacoes (data);
CREATE INDEX idx_cartoes_usuario ON Cartoes (id_usuario);
CREATE INDEX idx_metas_usuario ON Metas (id_usuario);