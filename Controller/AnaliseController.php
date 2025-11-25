<?php
namespace Controller;

use Model\AnaliseModel;

class AnaliseController
{
    private $model;

    public function __construct($pdo)
    {
        $this->model = new AnaliseModel($pdo);
    }

    public function index($usuarioId, $mes = null, $ano = null)
    {
        if (!$mes) $mes = date('m');
        if (!$ano) $ano = date('Y');

        // Datas para comparação
        $mesAnt = $mes - 1;
        $anoAnt = $ano;
        if ($mesAnt < 1) { $mesAnt = 12; $anoAnt = $ano - 1; }

        // --- 1. Renda e Despesa ---
        $resumo = $this->model->getResumoMes($usuarioId, $mes, $ano);
        $renda = $resumo['renda'] ?? 0;
        $despesa = abs($resumo['despesa'] ?? 0);
        
        $resumoAnt = $this->model->getResumoMes($usuarioId, $mesAnt, $anoAnt);
        $rendaAnt = $resumoAnt['renda'] ?? 0;
        $despesaAnt = abs($resumoAnt['despesa'] ?? 0);

        // --- 2. Metas (NOVO) ---
        $metas = $this->model->getTotalMetas($usuarioId, $mes, $ano);
        $metasAnt = $this->model->getTotalMetas($usuarioId, $mesAnt, $anoAnt);

        // --- 3. Investimentos (NOVO) ---
        $investimentos = $this->model->getTotalInvestimentos($usuarioId, $mes, $ano);
        $investimentosAnt = $this->model->getTotalInvestimentos($usuarioId, $mesAnt, $anoAnt);

        // --- 4. Gráficos ---
        // Evolução
        $evolucaoData = $this->model->getEvolucaoSemestral($usuarioId);
        $evoLabels = [];
        $evoValores = [];
        $mesesNome = ['01'=>'Jan','02'=>'Fev','03'=>'Mar','04'=>'Abr','05'=>'Mai','06'=>'Jun','07'=>'Jul','08'=>'Ago','09'=>'Set','10'=>'Out','11'=>'Nov','12'=>'Dez'];
        
        foreach ($evolucaoData as $d) {
            $parts = explode('-', $d['mes_ano']); 
            if(count($parts) == 2) {
                $m = $parts[1];
                $evoLabels[] = $mesesNome[$m] ?? $m;
                $evoValores[] = (float)$d['saldo_mensal'];
            }
        }

        // Categorias
        $catData = $this->model->getDespesasPorCategoria($usuarioId, $mes, $ano);
        $catLabels = [];
        $catValores = [];
        foreach ($catData as $c) {
            $catLabels[] = $c['categoria'];
            $catValores[] = (float)$c['total'];
        }

        return [
            // Renda/Despesa
            'renda' => $renda,
            'despesa' => $despesa,
            'rendaAnt' => $rendaAnt,
            'despesaAnt' => $despesaAnt,
            'pctRenda' => $this->calcPct($renda, $rendaAnt),
            'pctDespesa' => $this->calcPct($despesa, $despesaAnt),
            
            // Metas/Investimentos (NOVO)
            'metas' => $metas,
            'metasAnt' => $metasAnt,
            'pctMetas' => $this->calcPct($metas, $metasAnt),
            'investimentos' => $investimentos,
            'investimentosAnt' => $investimentosAnt,
            'pctInvestimentos' => $this->calcPct($investimentos, $investimentosAnt),

            // Gráficos
            'evoLabels' => $evoLabels,
            'evoValores' => $evoValores,
            'catLabels' => $catLabels,
            'catValores' => $catValores
        ];
    }

    private function calcPct($atual, $anterior) {
        if ($anterior == 0) return $atual > 0 ? 100 : 0;
        return (($atual - $anterior) / $anterior) * 100;
    }
}
?>