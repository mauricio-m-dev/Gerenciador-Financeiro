<?php
namespace Controller;

use Model\Ativo;
use Model\Investimento;

class InvestimentoController
{
    private $ativoModel;
    private $investimentoModel;

    // Recebe a conexão PDO direto no construtor
    public function __construct($pdo)
    {
        $this->ativoModel = new Ativo($pdo);
        $this->investimentoModel = new Investimento($pdo);
    }

    /**
     * Carrega todos os dados necessários para a tela
     */
    public function index($usuarioId)
    {
        // Busca a carteira consolidada (agrupada por ativo)
        $carteira = $this->investimentoModel->obterCarteira($usuarioId);
        
        // Busca o histórico (lista de transações)
        $historico = $this->investimentoModel->obterHistorico($usuarioId);
        
        // Calcula totais
        $patrimonioTotal = 0;
        $labelsGrafico = [];
        $valoresGrafico = [];

        foreach ($carteira as $item) {
            $valorAtual = floatval($item['valor_investido_liquido']);
            $patrimonioTotal += $valorAtual;

            // Prepara dados para o gráfico
            $labelsGrafico[] = $item['simbolo']; // Ex: PETR4
            $valoresGrafico[] = $valorAtual;     // Ex: 1500.00
        }

        return [
            'carteira' => $carteira,
            'historico' => $historico,
            'patrimonioTotal' => $patrimonioTotal,
            'qtdAtivos' => count($carteira),
            'graficoLabels' => $labelsGrafico,
            'graficoValores' => $valoresGrafico
        ];
    }

    /**
     * Processa o formulário de Adicionar Investimento
     */
    public function adicionar($dados, $usuarioId)
    {
        $simbolo = strtoupper(trim($dados['ticker'] ?? ''));
        $qtd = floatval($dados['quantidade'] ?? 0);
        $valor = floatval($dados['valor'] ?? 0);
        $tipo = $dados['tipo_operacao'] ?? 'compra';
        $data = $dados['data'] ?? date('Y-m-d H:i:s');

        if (empty($simbolo) || $qtd <= 0 || $valor <= 0) {
            return ['sucesso' => false, 'msg' => 'Dados inválidos. Verifique quantidade e valor.'];
        }

        // 1. Verifica se o ativo já existe no banco
        $ativo = $this->ativoModel->buscarPorSimbolo($simbolo);

        // 2. Se não existe, cria automaticamente
        if (!$ativo) {
            $nomeAtivo = $dados['nome_ativo'] ?? $simbolo; // Usa o ticker como nome se não vier nada
            $tipoAtivo = 'Ação'; // Padrão, depois você pode melhorar isso
            
            $this->ativoModel->criar($simbolo, $nomeAtivo, $tipoAtivo, 'Geral');
            $ativo = $this->ativoModel->buscarPorSimbolo($simbolo);
        }

        // 3. Registra a transação
        $resultado = $this->investimentoModel->adicionarTransacao(
            $usuarioId,
            $ativo['id'],
            $tipo,
            $qtd,
            $valor,
            'Manual'
        );

        if ($resultado) {
            return ['sucesso' => true, 'msg' => 'Operação registrada com sucesso!'];
        } else {
            return ['sucesso' => false, 'msg' => 'Erro ao salvar no banco de dados.'];
        }
    }

    /**
     * Excluir transação
     */
    public function excluir($idTransacao, $usuarioId)
    {
        if ($this->investimentoModel->removerTransacao($idTransacao, $usuarioId)) {
            return ['sucesso' => true, 'msg' => 'Transação removida.'];
        }
        return ['sucesso' => false, 'msg' => 'Erro ao remover.'];
    }
}
?>