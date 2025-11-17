<?php
/**
 * Controller: InvestimentoController
 * Gerencia a lógica de negócio para investimentos
 */
class InvestimentoController
{
    private $ativoModel;
    private $transacaoModel;

    public function __construct($ativoModel, $transacaoModel)
    {
        $this->ativoModel = $ativoModel;
        $this->transacaoModel = $transacaoModel;
    }

    /**
     * Adiciona um novo investimento (transação de compra)
     * @param int $userId
     * @param string $assetSymbol Ex: "PETR4"
     * @param int $quantidade
     * @param float $valorUnitario
     * @return array ['sucesso' => bool, 'mensagem' => string, 'transacaoId' => int|null]
     */
    public function adicionarInvestimento($userId, $assetSymbol, $quantidade, $valorUnitario)
    {
        try {
            // 1. Verifica se o ativo existe, se não, cria
            $ativo = $this->ativoModel->buscarPorSymbol($assetSymbol);
            
            if (!$ativo) {
                // Cria um novo ativo (informações mínimas vindas da API)
                $novoAtivo = $this->ativoModel->criar([
                    'asset_symbol' => $assetSymbol,
                    'asset_name' => $assetSymbol, // Será atualizado depois com dados da API
                    'asset_type' => 'Ação',
                    'asset_sector' => 'Diversos'
                ]);
                
                if (!$novoAtivo) {
                    return [
                        'sucesso' => false,
                        'mensagem' => 'Erro ao criar o ativo no banco de dados.'
                    ];
                }
                $ativoId = $novoAtivo;
            } else {
                $ativoId = $ativo['ativo_id'];
            }

            // 2. Calcula o valor total
            $valorTotal = $quantidade * $valorUnitario;

            // 3. Cria a transação
            $dados = [
                'user_id' => $userId,
                'ativo_id' => $ativoId,
                'quantidade' => $quantidade,
                'valor_unitario' => $valorUnitario,
                'valor_total' => $valorTotal,
                'tipo_transacao' => 'compra',
                'data_transacao' => date('Y-m-d H:i:s')
            ];

            $transacaoId = $this->transacaoModel->criar($dados);

            if ($transacaoId) {
                return [
                    'sucesso' => true,
                    'mensagem' => 'Investimento adicionado com sucesso!',
                    'transacaoId' => $transacaoId
                ];
            } else {
                return [
                    'sucesso' => false,
                    'mensagem' => 'Erro ao registrar a transação no banco de dados.'
                ];
            }
        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'mensagem' => 'Erro: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Vende um investimento (transação de venda)
     * @param int $userId
     * @param int $ativoId
     * @param int $quantidade
     * @param float $valorUnitario
     * @return array ['sucesso' => bool, 'mensagem' => string]
     */
    public function venderInvestimento($userId, $ativoId, $quantidade, $valorUnitario)
    {
        try {
            // Verifica se o usuário tem cotas suficientes
            $saldo = $this->transacaoModel->obterSaldoCotas($userId);
            $cotasDisponiveis = 0;

            foreach ($saldo as $ativo) {
                if ($ativo['ativo_id'] == $ativoId) {
                    $cotasDisponiveis = $ativo['total_cotas'];
                    break;
                }
            }

            if ($cotasDisponiveis < $quantidade) {
                return [
                    'sucesso' => false,
                    'mensagem' => 'Quantidade insuficiente de cotas. Disponível: ' . $cotasDisponiveis
                ];
            }

            // Cria a transação de venda
            $valorTotal = $quantidade * $valorUnitario;
            $dados = [
                'user_id' => $userId,
                'ativo_id' => $ativoId,
                'quantidade' => $quantidade,
                'valor_unitario' => $valorUnitario,
                'valor_total' => $valorTotal,
                'tipo_transacao' => 'venda',
                'data_transacao' => date('Y-m-d H:i:s')
            ];

            $transacaoId = $this->transacaoModel->criar($dados);

            if ($transacaoId) {
                return [
                    'sucesso' => true,
                    'mensagem' => 'Venda realizada com sucesso!',
                    'transacaoId' => $transacaoId
                ];
            } else {
                return [
                    'sucesso' => false,
                    'mensagem' => 'Erro ao registrar a venda.'
                ];
            }
        } catch (Exception $e) {
            return [
                'sucesso' => false,
                'mensagem' => 'Erro: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtém o resumo da carteira do usuário
     * @param int $userId
     * @return array
     */
    public function obterCarteiraUsuario($userId)
    {
        return $this->transacaoModel->obterSaldoCotas($userId);
    }

    /**
     * Obtém o histórico de transações do usuário
     * @param int $userId
     * @return array
     */
    public function obterHistoricoTransacoes($userId)
    {
        return $this->transacaoModel->listarPorUsuario($userId);
    }

    /**
     * Calcula estatísticas da carteira
     * @param int $userId
     * @return array Com patrimônio total, lucro/prejuízo, etc
     */
    public function calcularEstatisticas($userId)
    {
        $carteira = $this->obterCarteiraUsuario($userId);
        
        $patrimonioTotal = 0;
        $qtdAtivos = 0;

        foreach ($carteira as $ativo) {
            $patrimonioTotal += $ativo['valor_investido'];
            $qtdAtivos++;
        }

        return [
            'patrimonio_total' => $patrimonioTotal,
            'qtd_ativos' => $qtdAtivos,
            'carteira' => $carteira
        ];
    }
}
?>
