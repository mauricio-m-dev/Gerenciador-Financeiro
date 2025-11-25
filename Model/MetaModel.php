<?php
// Model/MetaModel.php

class MetaModel
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllMetas($userId)
    {
        // Busca os dados do banco
        $sql = "SELECT * FROM metas WHERE usuario_id = ? ORDER BY data_prazo ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function saveMeta($data, $userId)
    {
        // Cria um ponto inicial no histórico com chaves que o JS espera (date e value)
        $historico = json_encode([[
            'date' => date('Y-m-d'),
            'value' => 0,
            'amount' => 0
        ]]);

        $sql = "INSERT INTO metas (usuario_id, nome, valor_objetivo, valor_contribuicao_mensal, data_prazo, categoria, cor, valor_atual, historico_json) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 0, ?)";

        try {
            $stmt = $this->pdo->prepare($sql);
            
            $sucesso = $stmt->execute([
                $userId,
                $data['nome'],
                $data['objetivo'], 
                $data['mensal'],   
                $data['prazo'],    
                $data['categoria'],
                $data['cor'],
                $historico
            ]);

            return $sucesso; // Retorna true se funcionou

        } catch (Exception $e) {
            // MODO DEBUG: Retorna a mensagem exata do erro SQL
            return "Erro SQL: " . $e->getMessage(); 
        }
    }

    public function addContribution($metaId, $userId, $valor, $dataAporte)
    {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("SELECT valor_atual, historico_json FROM metas WHERE id = ? AND usuario_id = ?");
            $stmt->execute([$metaId, $userId]);
            $meta = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$meta) return false;

            // Calcula novo total
            $novoAcumulado = floatval($meta['valor_atual']) + $valor;
            
            $historico = json_decode($meta['historico_json'], true) ?? [];
            
            // Adiciona seguindo o padrão do seu JS
            $historico[] = [
                'date' => $dataAporte, // JS usa 'date' no formatChartDate
                'value' => $novoAcumulado, // JS usa 'value' no createChartData
                'amount' => $valor // Guardamos quanto foi esse aporte específico
            ];

            $novoJson = json_encode($historico);

            $update = $this->pdo->prepare("UPDATE metas SET valor_atual = ?, historico_json = ? WHERE id = ?");
            $update->execute([$novoAcumulado, $novoJson, $metaId]);

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            return false;
        }
    }

    public function removeHistoryItem($metaId, $userId, $itemDate, $itemTotalValue)
    {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("SELECT valor_atual, historico_json FROM metas WHERE id = ? AND usuario_id = ?");
            $stmt->execute([$metaId, $userId]);
            $meta = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$meta) return false;

            $historico = json_decode($meta['historico_json'], true) ?? [];
            $novoHistorico = [];
            $removido = false;
            $valorDoAporteRemovido = 0;

            foreach ($historico as $item) {
                // Seu JS envia a data e o valor TOTAL daquele momento.
                // Verificamos se data bate E se o valor total bate
                if (!$removido && $item['date'] == $itemDate && abs($item['value'] - $itemTotalValue) < 0.01) {
                    $removido = true;
                    // Se tivermos salvo o 'amount' (aporte), usamos ele para deduzir. 
                    // Se for o registro inicial (sem amount), assumimos 0 ou tratamos diferente.
                    $valorDoAporteRemovido = $item['amount'] ?? 0;
                    continue; 
                }
                $novoHistorico[] = $item;
            }

            if ($removido) {
                // Deduz apenas o valor daquele aporte específico do total atual
                $novoAcumulado = floatval($meta['valor_atual']) - floatval($valorDoAporteRemovido);
                
                // Atualiza o banco
                $update = $this->pdo->prepare("UPDATE metas SET valor_atual = ?, historico_json = ? WHERE id = ?");
                $update->execute([$novoAcumulado, json_encode($novoHistorico), $metaId]);
                $this->pdo->commit();
                return true;
            }
            
            $this->pdo->rollBack();
            return false;

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) $this->pdo->rollBack();
            return false;
        }
    }

    public function deleteMeta($metaId, $userId)
    {
        $stmt = $this->pdo->prepare("DELETE FROM metas WHERE id = ? AND usuario_id = ?");
        return $stmt->execute([$metaId, $userId]);
    }
}
?>