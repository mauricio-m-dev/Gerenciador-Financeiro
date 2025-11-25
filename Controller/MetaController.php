<?php
// Arquivo: Controller/MetaController.php

require_once __DIR__ . '/../Model/MetaModel.php';

class MetaController
{
    private $model;

    public function __construct($pdo)
    {
        $this->model = new MetaModel($pdo);
    }

    // --- CORREÇÃO PRINCIPAL AQUI ---
    public function index($userId)
    {
        // 1. Busca os dados brutos do banco (nomes das colunas originais)
        $dadosBrutos = $this->model->getAllMetas($userId);

        $metasFormatadas = [];

        // 2. Traduz as colunas do Banco para o que o HTML espera
        foreach ($dadosBrutos as $meta) {
            $metasFormatadas[] = [
                'id' => $meta['id'],
                'usuario_id' => $meta['usuario_id'],
                'nome' => $meta['nome'],
                // Tradução: Banco -> HTML
                'objetivo' => (float) $meta['valor_objetivo'],
                'acumulado' => (float) $meta['valor_atual'],
                'mensal' => (float) $meta['valor_contribuicao_mensal'],
                'prazo' => $meta['data_prazo'],
                'categoria' => $meta['categoria'],
                'cor' => $meta['cor'],
                'historico_json' => $meta['historico_json']
            ];
        }

        return $metasFormatadas;
    }

    public function create($userId)
    {
        $data = $this->getJsonInput();

        if (empty($data['nome']) || empty($data['objetivo'])) {
            $this->jsonResponse('error', 'Preencha os campos obrigatórios.');
        }

        // Garante que a data venha correta ou NULL se estiver vazia
        $prazo = !empty($data['prazo']) ? $data['prazo'] : null;

        $saveData = [
            'nome' => $data['nome'],
            'objetivo' => (float) $data['objetivo'],
            'mensal' => (float) ($data['mensal'] ?? 0),
            'prazo' => $prazo,
            'categoria' => $data['categoria'] ?? 'Geral',
            'cor' => $data['cor'] ?? '#155eef'
        ];

        // Chama o Model
        $resultado = $this->model->saveMeta($saveData, $userId);

        // Verifica o resultado
        if ($resultado === true) {
            $this->jsonResponse('success', 'Meta criada com sucesso!');
        } else {
            // Se o resultado for uma string, é a mensagem de erro do SQL que configuramos no Passo 1
            $msgErro = is_string($resultado) ? $resultado : 'Erro desconhecido ao salvar.';
            $this->jsonResponse('error', $msgErro);
        }
    }

    public function contribute($userId)
    {
        $data = $this->getJsonInput();
        $valor = (float) $data['amount'];

        // O input type="month" devolve YYYY-MM. Adicionamos dia 01
        $dataCompleta = $data['date'] . '-01';

        if ($this->model->addContribution($data['meta_id'], $userId, $valor, $dataCompleta)) {
            $this->jsonResponse('success', 'Aporte realizado!');
        } else {
            $this->jsonResponse('error', 'Erro ao processar aporte.');
        }
    }

    public function undo($userId)
    {
        $data = $this->getJsonInput();
        $valor = (float) $data['value'];
        $date = $data['date'];

        if ($this->model->removeHistoryItem($data['meta_id'], $userId, $date, $valor)) {
            $this->jsonResponse('success', 'Lançamento removido.');
        } else {
            $this->jsonResponse('error', 'Erro ao remover ou item não encontrado.');
        }
    }

    public function delete($userId)
    {
        $data = $this->getJsonInput();
        if ($this->model->deleteMeta($data['meta_id'], $userId)) {
            $this->jsonResponse('success', 'Meta excluída.');
        } else {
            $this->jsonResponse('error', 'Erro ao excluir.');
        }
    }

    // --- HELPER FUNCTIONS (Usadas no Meta.php) ---

    public function formatar_moeda($val)
    {
        return 'R$ ' . number_format((float) $val, 2, ',', '.');
    }

    public function calcular_progresso($atual, $total)
    {
        if ($total <= 0)
            return 0;
        return min(100, round(($atual / $total) * 100));
    }

    public function formatar_data($data)
    {
        return date('d/m/Y', strtotime($data));
    }

    public function calcular_diferenca_meses($dataPrazo)
    {
        $hoje = new DateTime();
        $prazo = new DateTime($dataPrazo);
        $diff = $hoje->diff($prazo);
        $meses = ($diff->y * 12) + $diff->m;
        // Se já passou o prazo, retorna negativo ou zero
        if ($diff->invert)
            return -$meses;
        return $meses;
    }

    // --- INTERNOS ---
    private function getJsonInput()
    {
        return json_decode(file_get_contents('php://input'), true);
    }

    private function jsonResponse($status, $msg)
    {
        header('Content-Type: application/json');
        echo json_encode(['status' => $status, 'message' => $msg]);
        exit;
    }
}
?>