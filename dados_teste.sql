-- ============================================
-- Script de Teste - Gerenciador Financeiro
-- ============================================

-- Limpar dados existentes (cuidado!)
-- DELETE FROM InvestimentoTransacoes;
-- DELETE FROM Ativos;
-- ALTER TABLE Ativos AUTO_INCREMENT = 1;
-- ALTER TABLE InvestimentoTransacoes AUTO_INCREMENT = 1;

-- ============================================
-- Inserir Ativos (Ações, Fundos, etc)
-- ============================================

INSERT INTO Ativos (asset_symbol, asset_name, asset_type, asset_sector) VALUES
('PETR4', 'Petrobras PN', 'Ação', 'Energia'),
('VALE3', 'Vale ON', 'Ação', 'Mineração'),
('ITUB4', 'Itaú Unibanco PN', 'Ação', 'Financeiro'),
('ABEV3', 'Ambev ON', 'Ação', 'Bebidas'),
('BBDC4', 'Bradesco PN', 'Ação', 'Financeiro'),
('MGLU3', 'Magazine Luiza ON', 'Ação', 'Varejo'),
('KNRI11', 'Kinea Recebíveis Imobiliários FII', 'FII', 'Recebíveis'),
('XRPD34', 'Ripple', 'Criptmoeda', 'Digital');

-- ============================================
-- Inserir Transações de Teste
-- ============================================

-- Compras para o usuário 1
INSERT INTO InvestimentoTransacoes (user_id, ativo_id, quantidade, valor_unitario, valor_total, tipo_transacao, data_transacao) VALUES
(1, 1, 45, 30.50, 1372.50, 'compra', '2025-10-21 10:00:00'),   -- PETR4
(1, 2, 30, 85.20, 2556.00, 'compra', '2025-10-20 14:30:00'),   -- VALE3
(1, 4, 50, 12.80, 640.00, 'compra', '2025-10-19 09:15:00'),    -- ABEV3
(1, 7, 25, 125.00, 3125.00, 'compra', '2025-10-18 16:45:00'),  -- KNRI11
(1, 5, 20, 28.90, 578.00, 'compra', '2025-10-17 11:20:00');    -- BBDC4

-- Venda parcial (exemplo)
INSERT INTO InvestimentoTransacoes (user_id, ativo_id, quantidade, valor_unitario, valor_total, tipo_transacao, data_transacao) VALUES
(1, 1, 10, 32.00, 320.00, 'venda', '2025-10-22 13:00:00');     -- Venda de 10 cotas PETR4

-- Compras para o usuário 2 (para testes)
INSERT INTO InvestimentoTransacoes (user_id, ativo_id, quantidade, valor_unitario, valor_total, tipo_transacao, data_transacao) VALUES
(2, 1, 20, 30.00, 600.00, 'compra', '2025-10-20 10:00:00'),
(2, 3, 15, 45.60, 684.00, 'compra', '2025-10-19 14:30:00');

-- ============================================
-- Consultas para Verificar
-- ============================================

-- Ver todos os ativos
SELECT * FROM Ativos;

-- Ver todas as transações
SELECT it.*, a.asset_symbol, a.asset_name
FROM InvestimentoTransacoes it
JOIN Ativos a ON it.ativo_id = a.ativo_id
ORDER BY it.data_transacao DESC;

-- Ver carteira consolidada do usuário 1
SELECT 
    a.ativo_id,
    a.asset_symbol,
    a.asset_name,
    a.asset_type,
    SUM(CASE WHEN it.tipo_transacao = 'compra' THEN it.quantidade ELSE -it.quantidade END) as total_cotas,
    AVG(CASE WHEN it.tipo_transacao = 'compra' THEN it.valor_unitario END) as valor_medio,
    SUM(CASE WHEN it.tipo_transacao = 'compra' THEN it.valor_total ELSE -it.valor_total END) as valor_investido
FROM InvestimentoTransacoes it
JOIN Ativos a ON it.ativo_id = a.ativo_id
WHERE it.user_id = 1
GROUP BY a.ativo_id, a.asset_symbol, a.asset_name, a.asset_type
HAVING total_cotas > 0
ORDER BY a.asset_name;

-- Patrimônio total do usuário 1
SELECT 
    SUM(CASE WHEN it.tipo_transacao = 'compra' THEN it.valor_total ELSE -it.valor_total END) as patrimonio_total
FROM InvestimentoTransacoes it
WHERE it.user_id = 1;
