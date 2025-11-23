<?php
// O controller precisa do Model para acessar o BD
require_once __DIR__ . '/../Model/MetaModel.php';

class MetaController
{

    private $metaModel;

    public function __construct()
    {
        // Instancia o Model para que o Controller possa usá-lo
        $this->metaModel = new MetaModel();
    }

    // FUNÇÕES AUXILIARES - Cálculos e Formatação
    public function calcular_progresso($acumulado, $objetivo)
    {
        if ($objetivo == 0)
            return 0;
        return round(($acumulado / $objetivo) * 100);
    }

    public function calcular_diferenca_meses($prazo)
    {
        $data_prazo = new DateTime($prazo);
        $data_atual = new DateTime('now');
        if ($data_prazo < $data_atual)
            return 0;

        $intervalo = $data_atual->diff($data_prazo);
        $meses = ($intervalo->y * 12) + $intervalo->m;
        if ($intervalo->d > 0)
            $meses++;

        return $meses;
    }

    public function formatar_data($prazo)
    {
        $data = new DateTime($prazo);
        $meses_pt = [1 => 'Jan', 2 => 'Fev', 3 => 'Mar', 4 => 'Abr', 5 => 'Mai', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago', 9 => 'Set', 10 => 'Out', 11 => 'Nov', 12 => 'Dez'];
        $mes = $meses_pt[(int) $data->format('n')];
        $ano = $data->format('Y');

        return $mes . ' ' . $ano;
    }

    public function formatar_moeda($valor)
    {
        return 'R$ ' . number_format($valor, 0, ',', '.');
    }

    // Método principal para exibir a lista de metas
    public function index()
    {
        // 1. CHAMA O MODEL: Busca os dados reais do BD
        $metas = $this->metaModel->getAllMetas();

        // Data mínima para o campo de prazo
        $min_date = date('Y-m-d', strtotime('+1 day'));


        // 3. PROCESSA OS DADOS (Prepara as variáveis de resumo)
        $total_objetivo = !empty($metas) ? array_sum(array_column($metas, 'objetivo')) : 0;
        $total_acumulado = !empty($metas) ? array_sum(array_column($metas, 'acumulado')) : 0;
        $total_mensal = !empty($metas) ? array_sum(array_column($metas, 'mensal')) : 0;

        $progresso_geral = ($total_objetivo > 0) ? ($total_acumulado / $total_objetivo) * 100 : 0;
        $progresso_geral_formatado = number_format($progresso_geral, 1) . '%';
        $total_objetivo_formatado = $this->formatar_moeda($total_objetivo);
        $total_acumulado_formatado = $this->formatar_moeda($total_acumulado);
        $total_mensal_formatado = $this->formatar_moeda($total_mensal);

        // Variáveis de Dicas (Exemplo)
        $dicas = [
            // ... (Dicas mantidas para exibição) ...
            [
                'tipo' => 'success',
                'titulo' => 'Excelente taxa de poupança!',
                'texto' => 'Você está economizando 42.4% da sua renda, acima da média recomendada de 30%.',
            ],
            [
                'tipo' => 'info',
                'titulo' => 'Aumente a contribuição',
                'texto' => 'Aumentando +R$ 200/mês na meta ' . (isset($metas[1]['nome']) ? $metas[1]['nome'] : 'exemplo') . ', você a alcança 3 meses antes.',
            ],
            [
                'tipo' => 'info',
                'titulo' => 'Considere investir',
                'texto' => 'Investindo suas contribuições à 10% a.a., você pode acelerar suas metas.',
            ],
        ];

        // 4. CHAMA A VIEW (Onde o HTML está)
        // Todas as variáveis definidas acima estarão disponíveis na View.
        $controller = $this;
        require 'View/Meta.php';
    }
    public function create()
    {
        // 1. Define o cabeçalho como JSON
        header('Content-Type: application/json');

        // 2. Pega os dados enviados (em formato JSON) pelo fetch() do JavaScript
        $data = json_decode(file_get_contents('php://input'), true);

        // 3. Validação simples no lado do servidor (essencial)
        if (empty($data['nome']) || empty($data['objetivo']) || empty($data['prazo'])) {
            // Envia uma resposta de erro
            echo json_encode([
                'status' => 'error',
                'message' => 'Dados inválidos. Verifique os campos obrigatórios.'
            ]);
            return;
        }

        // 4. Tenta salvar no banco de dados
        try {
            $success = $this->metaModel->saveMeta($data);

            if ($success) {
                // Envia uma resposta de sucesso
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Meta criada com sucesso!'
                ]);
            } else {
                // throw new Exception('Falha ao salvar no Model.');

                echo json_encode(['status' => 'error', 'message' => 'Falha ao salvar a meta.']);
            }

        } catch (Exception $e) {
            // Envia uma resposta de erro genérico
            echo json_encode([
                'status' => 'error',
                'Erro de Conexão com o Banco de Dados: ' . $e->getMessage()
            ]);
        } catch (\Exception $e) {
            // Isso cobre outros erros gerais
            echo json_encode(['status' => 'error', 'message' => 'Erro interno do servidor: ' . $e->getMessage()]);
        }
    }


    public function contribute()
    {

        $data = json_decode(file_get_contents('php://input'), true);
        file_put_contents(__DIR__ . '/debug_aporte.txt', print_r($data, true));

        header('Content-Type: application/json');

        // Pega os dados enviados pelo fetch()
        $data = json_decode(file_get_contents('php://input'), true);

        $metaId = filter_var($data['meta_id'], FILTER_VALIDATE_INT);
        $amount = filter_var($data['amount'], FILTER_VALIDATE_FLOAT);

        $date = $data['date'] ?? null;



        if (!$metaId || !$amount || $amount <= 0 || empty($date)) {
            echo json_encode(['status' => 'error', 'message' => 'Dados inválidos. Verifique o ID, o valor e a data do aporte.']);
            return;
        }

        try {
            $success = $this->metaModel->contributeToMeta($metaId, $amount, $date);

            if ($success) {
                echo json_encode(['status' => 'success', 'message' => 'Aporte de R$ ' . number_format($amount, 2, ',', '.') . ' registrado com sucesso!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Falha ao atualizar a meta. Meta não encontrada ou erro no banco de dados.']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Erro interno do servidor.']);
        }
    }

    public function delete()
    {
        header('Content-Type: application/json');

        $data = json_decode(file_get_contents('php://input'), true);
        $metaId = filter_var($data['meta_id'], FILTER_VALIDATE_INT);

        if (!$metaId) {
            echo json_encode(['status' => 'error', 'message' => 'ID da meta inválido.']);
            return;
        }

        try {
            $success = $this->metaModel->deleteMeta($metaId);

            if ($success) {
                echo json_encode(['status' => 'success', 'message' => 'Meta excluída com sucesso!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Falha ao excluir a meta. Meta não encontrada ou erro no banco.']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Erro interno do servidor.']);
        }
    }

    public function undo()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents('php://input'), true);

        $metaId = filter_var($data['meta_id'] ?? null, FILTER_VALIDATE_INT);
        $date = $data['date'] ?? null;
        $value = filter_var($data['value'] ?? null, FILTER_VALIDATE_FLOAT);

        if (!$metaId || !$date || $value === false) {
            echo json_encode(['status' => 'error', 'message' => 'Dados inválidos para exclusão.']);
            return;
        }

        try {
            $success = $this->metaModel->removeHistoryItem($metaId, $date, $value);

            if ($success) {
                echo json_encode(['status' => 'success', 'message' => 'Aporte removido e saldo recalculado com sucesso!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Erro ao remover aporte. Item não encontrado ou erro no banco.']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Erro interno do servidor.']);
        }
    }
}

