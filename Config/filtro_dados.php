<?php
// Define o cabeçalho de resposta como JSON
header('Content-Type: application/json');

// Recebe o ID do cartão do JavaScript (se não for passado ou for inválido, usa 0 para "Geral")
$cardId = (int)($_GET['cartao_id'] ?? 0); 

// =========================================================
// FUNÇÃO HELPER: Formatação de Moeda
// =========================================================
function formatarMoeda($valor) {
    return 'R$' . number_format($valor, 2, ',', '.');
}

// =========================================================
// FUNÇÃO HELPER: Gerar HTML da Lista de Categorias
// =========================================================
function generateCategoryListHTML($labels, $valores) {
    $total = array_sum($valores);
    $html = '';
    
    // Mapeamento de ícones e classes (cores) para simular o dinamismo
    $icon_map = [
        'Casa' => 'bx-home-alt', 
        'Transporte' => 'bx-car', 
        'Alimentação' => 'bx-lemon', 
        'Compras' => 'bx-shopping-bag',
        'Serviços Online' => 'bx-credit-card-alt', 
        'Supermercado' => 'bx-store', 
        'Contas' => 'bx-receipt',
        'Lazer' => 'bx-movie-play',
        'Outros' => 'bx-category',
    ];
    // Classes de cor baseadas no seu HTML (dot-casa, etc.)
    $color_map = [
        'Casa' => 'dot-casa', 'Transporte' => 'dot-transporte', 'Alimentação' => 'dot-mantimentos', 
        'Compras' => 'dot-compras', 'Serviços Online' => 'dot-cartao', 'Supermercado' => 'dot-mantimentos', 
        'Contas' => 'dot-casa', 'Lazer' => 'dot-compras', 'Outros' => 'dot-outros'
    ];


    foreach ($labels as $index => $label) {
        $valor = $valores[$index];
        // Calcula a porcentagem
        $percent = ($total > 0) ? number_format(($valor / $total) * 100, 2, ',', '.') : '0,00';
        
        $icon = $icon_map[$label] ?? 'bx-category'; // Ícone padrão
        $dot_class = $color_map[$label] ?? 'dot-outros'; // Cor padrão
        
        $html .= '
            <li class="category-item">
                <span class="category-icon-wrapper '. $dot_class .'">
                    <i class="bx ' . $icon . '"></i> 
                </span> 
                ' . htmlspecialchars($label) . ' 
                <span class="percentage">' . $percent . '%</span>
            </li>
        ';
    }
    return $html;
}


// =========================================================
// DADOS FICTÍCIOS POR ID DE CARTÃO (ATUALIZADO)
// =========================================================
$dados_ficticios = [
    // Chave 0: Dados Consolidados/Padrão (usado no carregamento inicial)
    0 => [
        'renda' => 5502.45,
        'renda_percent' => '12,5', 
        'renda_trend' => 'up', 
        'metas' => 3945.55,
        'metas_percent' => '15', 
        'metas_trend' => 'down',
        'despesas_total' => 9450.00,
        'despesas_percent' => '27,1',
        'despesas_trend' => 'up',
        'grafico' => [
            'labels' => ['Casa', 'Transporte', 'Alimentação', 'Outros'],
            'valores' => [4135, 1347, 2500, 1468] // Total: 9450
        ],
        'tabela_html' => '
            <tr><td><span class="initials orlando">OS</span> Salário Geral</td><td>Depósito</td><td>2024/04/01</td><td class="income">+R$5.502,45</td><td><i class=\'bx bx-dots-vertical-rounded\'></i></td></tr>
            <tr><td><span class="initials netflix">N</span> Netflix Geral</td><td>Cartão de crédito</td><td>2024/03/29</td><td class="expense">-R$9,90</td><td><i class=\'bx bx-dots-vertical-rounded\'></i></td></tr>
            <tr><td><span class="initials uber"><i class=\'bx bxs-car\'></i></span> Uber/Geral</td><td>Cartão de crédito</td><td>2024/03/26</td><td class="expense">-R$34,20</td><td><i class=\'bx bx-dots-vertical-rounded\'></i></td></tr>
        '
    ],
    // Chave 19: Dados para o Cartão ID 19
    19 => [
        'renda' => 4200.00, 
        'renda_percent' => '5,0',
        'renda_trend' => 'up',
        'metas' => 1000.00,
        'metas_percent' => '20',
        'metas_trend' => 'up',
        'despesas_total' => 1850.00,
        'despesas_percent' => '10,5',
        'despesas_trend' => 'down',
        'grafico' => [
            'labels' => ['Compras', 'Serviços Online', 'Transporte'],
            'valores' => [1000, 500, 350] // Total: 1850
        ],
        // Transações atualizadas SEM o sufixo (Cartão 19)
        'tabela_html' => '
            <tr><td><span class="initials loja"><i class=\'bx bx-shopping-bag\'></i></span> Loja Online</td><td>Crédito</td><td>2024/04/05</td><td class="expense">-R$750,00</td><td><i class=\'bx bx-dots-vertical-rounded\'></i></td></tr>
            <tr><td><span class="initials netflix">N</span> Assinatura</td><td>Crédito</td><td>2024/04/01</td><td class="expense">-R$200,00</td><td><i class=\'bx bx-dots-vertical-rounded\'></i></td></tr>
            <tr><td><span class="initials uber"><i class=\'bx bxs-car\'></i></span> Viagem</td><td>Crédito</td><td>2024/03/28</td><td class="expense">-R$900,00</td><td><i class=\'bx bx-dots-vertical-rounded\'></i></td></tr>
        '
    ],
    // Chave 20: Dados para o Cartão ID 20
    20 => [
        'renda' => 1302.45,
        'renda_percent' => '0,5',
        'renda_trend' => 'up',
        'metas' => 2945.55,
        'metas_percent' => '5',
        'metas_trend' => 'down',
        'despesas_total' => 3800.00,
        'despesas_percent' => '5,8',
        'despesas_trend' => 'up',
        'grafico' => [
            'labels' => ['Supermercado', 'Contas', 'Lazer'],
            'valores' => [2000, 1500, 300] // Total: 3800
        ],
        // Transações atualizadas SEM o sufixo (Cartão 20)
        'tabela_html' => '
            <tr><td><span class="initials mercado"><i class=\'bx bx-store\'></i></span> Supermercado</td><td>Débito</td><td>2024/04/03</td><td class="expense">-R$350,00</td><td><i class=\'bx bx-dots-vertical-rounded\'></i></td></tr>
            <tr><td><span class="initials ifood"><i class=\'bx bx-food-menu\'></i></span> Restaurante</td><td>Débito</td><td>2024/04/02</td><td class="expense">-R$50,00</td><td><i class=\'bx bx-dots-vertical-rounded\'></i></td></tr>
        '
    ]
];

// Pega os dados com base no ID recebido, ou usa o ID 0 se o ID não existir
$dados_cartao = $dados_ficticios[$cardId] ?? $dados_ficticios[0];


// =========================================================
// MONTAGEM DA RESPOSTA JSON FINAL
// =========================================================

$response = [
    'sumario' => [
        'renda_formatada' => formatarMoeda($dados_cartao['renda']),
        'renda_percent' => $dados_cartao['renda_percent'],
        'renda_trend' => $dados_cartao['renda_trend'],
        
        'despesas_formatada' => formatarMoeda($dados_cartao['despesas_total']),
        'despesas_percent' => $dados_cartao['despesas_percent'],
        'despesas_trend' => $dados_cartao['despesas_trend'],
        
        'metas_formatada' => formatarMoeda($dados_cartao['metas']),
        'metas_percent' => $dados_cartao['metas_percent'],
        'metas_trend' => $dados_cartao['metas_trend'],
    ],
    'grafico' => [
        'labels' => $dados_cartao['grafico']['labels'],
        'data' => $dados_cartao['grafico']['valores'],
    ],
    'tabela_html' => $dados_cartao['tabela_html'],
    'category_list_html' => generateCategoryListHTML($dados_cartao['grafico']['labels'], $dados_cartao['grafico']['valores']), 
];


echo json_encode($response);
exit;