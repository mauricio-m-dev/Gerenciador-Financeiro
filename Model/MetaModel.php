<?php
require_once __DIR__ . '/../Config/configuration.php';
require_once __DIR__ . '/Connection.php';

use Model\Connection;

class MetaModel
{
    private $pdo;

    public function __construct()
    {
        // Usar a conexão singleton da classe Connection
        $this->pdo = Connection::getInstance();
    }

    /**
     * Busca todas as metas da tabela 'metas'.
     */
    public function getAllMetas()
    {
        $stmt = $this->pdo->query("SELECT id, nome, categoria, objetivo, acumulado, mensal, prazo, cor, historico_json FROM metas");
        return $stmt->fetchAll();
    }


    /**
     * Insere uma nova meta no banco de dados.
     * @param array $data Dados da meta (nome, objetivo, mensal, prazo, categoria, cor)
     * @return bool Retorna true em caso de sucesso.
     */
    public function saveMeta($data)
    {
        // O campo 'acumulado' começa em 0 por padrão no banco
        $historico_inicial = json_encode([]);

        $sql = "INSERT INTO metas (nome, objetivo, mensal, prazo, categoria, cor, acumulado, historico_json) 
                VALUES (?, ?, ?, ?, ?, ?, 0, ?)";

        try {
            $stmt = $this->pdo->prepare($sql);

            // Executa a query passando os dados do array na ordem correta
            $stmt->execute([
                $data['nome'],
                $data['objetivo'],
                $data['mensal'],
                $data['prazo'],
                $data['categoria'],
                $data['cor'], // A cor foi adicionada ao formulário
                $historico_inicial
            ]);

            return true;
        } catch (PDOException $e) {
            // Em um sistema real, você registraria o erro (log)
            // error_log($e->getMessage());
            return false;
        }
    }


    public function contributeToMeta($metaId, $amount, $date)
    {
        // 1. Inicia uma transação para garantir que as duas etapas ocorram ou falhem juntas
        $this->pdo->beginTransaction();

        try {
            // --- ETAPA 1: Obter o acumulado e o histórico atual ---
            $stmt = $this->pdo->prepare("SELECT acumulado, objetivo, historico_json FROM metas WHERE id = ?");
            $stmt->execute([$metaId]);
            $meta = $stmt->fetch();

            if (!$meta) {
                $this->pdo->rollBack();
                return false; // Meta não encontrada
            }

            $novoAcumulado = $meta['acumulado'] + $amount;
            $historico_raw = json_decode($meta['historico_json'], true) ?: [];
            $historico_novo_formato = [];

            if (!empty($historico_raw) && is_numeric($historico_raw[0])) {

                $count = count($historico_raw);

                $startDate = new DateTime();
                $startDate->modify('-' . ($count - 1) . ' months');

                foreach ($historico_raw as $value) {
                    $historico_novo_formato[] = [
                        'date' => $startDate->format('Y-m-d'),
                        'value' => $value
                    ];
                    $startDate->modify('+1 month');
                }

            } else {
                $historico_novo_formato = $historico_raw;
            }

            $historico_novo_formato[] = [
                'date' => $date . '-01',
                'value' => $novoAcumulado
            ];

            if(empty($historico_novo_formato) || $historico_novo_formato[0]['value'] != 0) {
                array_unshift($historico_novo_formato, [
                    'date' => $date . '-01',
                    'value' => 0
                ]);
            }

            $novoHistoricoJson = json_encode($historico_novo_formato);

            // --- ETAPA 2: Atualizar o banco de dados com o novo acumulado e histórico ---
            $sql = "UPDATE metas 
                SET acumulado = ?, historico_json = ? 
                WHERE id = ?";

            $stmtUpdate = $this->pdo->prepare($sql);
            $stmtUpdate->execute([$novoAcumulado, $novoHistoricoJson, $metaId]);

            $this->pdo->commit(); // Confirma as alterações
            return true;

        } catch (PDOException $e) {
            $this->pdo->rollBack(); // Desfaz se algo deu errado
            // Em um sistema real, você registraria o erro
            // error_log("Erro de Aporte: " . $e->getMessage()); 
            return false;
        }
    }

    public function deleteMeta($metaId)
    {
        $sql = "DELETE FROM metas WHERE id = ?";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$metaId]);
            
            return $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            // Em um sistema real, você registraria o erro
            // error_log("Erro ao deletar meta: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove um item específico do histórico e recalcula o saldo acumulado.
     * @param int $metaId
     * @param string $dateToRemove Data do registro a remover (Y-m-d)
     * @param float $valueToRemove Valor ACUMULADO naquele momento (para identificação precisa)
     */
    public function removeHistoryItem($metaId, $dateToRemove, $valueToRemove)
    {
        $this->pdo->beginTransaction();

        try {
            // 1. Busca os dados atuais
            $stmt = $this->pdo->prepare("SELECT acumulado, historico_json FROM metas WHERE id = ?");
            $stmt->execute([$metaId]);
            $meta = $stmt->fetch();

            if (!$meta) {
                $this->pdo->rollBack();
                return false;
            }

            $historico = json_decode($meta['historico_json'], true) ?: [];
            
            // Ordena histórico por data para garantir cálculo correto
            usort($historico, function($a, $b) {
                return strtotime($a['date']) - strtotime($b['date']);
            });

            $indexToRemove = -1;
            $contributionDelta = 0; // O valor que esse aporte representou (diferença do anterior)

            // 2. Encontrar o item e calcular o "delta" (o valor real do aporte naquele dia)
            foreach ($historico as $i => $item) {
                // Compara data e valor (com pequena margem para float)
                if ($item['date'] == $dateToRemove && abs($item['value'] - $valueToRemove) < 0.01) {
                    $indexToRemove = $i;
                    
                    // Calcula quanto foi adicionado neste aporte
                    $valorAnterior = ($i > 0) ? $historico[$i-1]['value'] : 0;
                    $contributionDelta = $item['value'] - $valorAnterior;
                    break;
                }
            }

            if ($indexToRemove === -1) {
                $this->pdo->rollBack();
                return false; // Item não encontrado
            }

            // 3. Remover o item e subtrair o delta de todos os itens POSTERIORES
            // Se eu apago um aporte de R$ 500 feito em Maio, todos os meses seguintes devem cair R$ 500.
            
            // Remove o item do array
            array_splice($historico, $indexToRemove, 1);

            // Ajusta os itens subsequentes
            for ($j = $indexToRemove; $j < count($historico); $j++) {
                $historico[$j]['value'] -= $contributionDelta;
                // Evita valores negativos por erro de arredondamento
                if ($historico[$j]['value'] < 0) $historico[$j]['value'] = 0;
            }

            // 4. O novo acumulado total é o valor do último item do histórico atualizado
            $novoAcumuladoTotal = 0;
            if (count($historico) > 0) {
                $lastItem = end($historico);
                $novoAcumuladoTotal = $lastItem['value'];
            }

            // 5. Salva no banco
            $novoJson = json_encode($historico);
            $sqlUpdate = "UPDATE metas SET acumulado = ?, historico_json = ? WHERE id = ?";
            $stmtUpdate = $this->pdo->prepare($sqlUpdate);
            $stmtUpdate->execute([$novoAcumuladoTotal, $novoJson, $metaId]);

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            $this->pdo->rollBack();
            return false;
        }
    }
    
}

?>