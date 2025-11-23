-- Tabela 1: Ativos
-- Armazena as informações descritivas de cada ativo
CREATE TABLE Ativos (
    -- Chave primária do ativo
    ativo_id INT AUTO_INCREMENT PRIMARY KEY,

    -- O "ticker" é único e é usado pela API Brapi
    asset_symbol VARCHAR(20) NOT NULL UNIQUE, -- Ex: "PETR4"
    asset_name VARCHAR(255) NOT NULL,         -- Ex: "Petrobras PN"
    
    -- Colunas para funcionalidade do gráfico:
    asset_type VARCHAR(100) NOT NULL, -- Ex: "Ação", "FII", "BDR"
    asset_sector VARCHAR(100) -- Ex: "Energia", "Financeiro", "Tijolo"
);

-- Tabela 2: InvestimentoTransacoes
-- Armazena cada operação de compra e venda
CREATE TABLE InvestimentoTransacoes (
    -- Chave primária da transação
    transacao_id INT AUTO_INCREMENT PRIMARY KEY,

    -- --- CHAVES DE INTEGRAÇÃO ---
    -- 1. Ligação com a futura tabela de usuários
    user_id INT NOT NULL, 
    -- 2. Ligação com a nossa tabela de Ativos
    ativo_id INT NOT NULL, 

    -- Dados da Transação (do formulário modal)
    quantidade INT NOT NULL,
    valor_unitario DECIMAL(10, 2) NOT NULL,
    valor_total DECIMAL(10, 2) NOT NULL,
    
    -- Dados Essenciais
    tipo_transacao ENUM('compra', 'venda') NOT NULL DEFAULT 'compra',
    data_transacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    -- Chave estrangeira para a tabela Ativos
    FOREIGN KEY (ativo_id) REFERENCES Ativos(ativo_id)
    
    -- NOTA: A FOREIGN KEY para 'user_id' será adicionada 

);

//Pode copiar e colar no sql pra criar o banco, copie tudo do inicio ao fim
