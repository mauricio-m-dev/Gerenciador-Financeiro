<?php
namespace Controller;

use Model\CartaoModel;

class CartaoController {
    private $model;

    public function __construct(CartaoModel $model) {
        $this->model = $model;
    }

    public function index($userId) {
        // 1. Busca todos os cartões
        $cartoes = $this->model->getAllCards($userId);

        // 2. Define o Cartão Ativo
        $activeCardId = null;

        if (isset($_GET['card_id']) && !empty($_GET['card_id'])) {
            $activeCardId = (int)$_GET['card_id'];
        } elseif (!empty($cartoes)) {
            $activeCardId = $cartoes[0]['id'];
        }

        // --- CORREÇÃO PRINCIPAL AQUI ---
        // Se não houver cartão ativo, retorna array com as chaves que a View espera
        if ($activeCardId === null) {
            return [
                'cartoes' => [],
                'activeCardId' => 0,
                'resumo' => [
                    'renda' => 0,    // <--- Corrigido (era receitas)
                    'despesa' => 0,  // <--- Corrigido (singular)
                    'meta' => 0,     // <--- Adicionado
                    'saldo' => 0
                ],
                'transacoes' => [],
                'expensesByCategory' => []
            ];
        }

        // 3. Busca dados do cartão ativo
        $resumo = $this->model->getResumo($userId, $activeCardId);
        $transacoes = $this->model->getTransacoes($userId, $activeCardId);
        $expensesByCategory = $this->model->getExpensesByCategory($userId, $activeCardId);

        return [
            'cartoes' => $cartoes,
            'activeCardId' => $activeCardId,
            'resumo' => $resumo,
            'transacoes' => $transacoes,
            'expensesByCategory' => $expensesByCategory
        ];
    }
    
    // Método para JSON (caso use AJAX)
    public function getDadosJson($userId, $cardId) {
        if ($cardId == 0) {
            $cartoes = $this->model->getAllCards($userId);
            if (!empty($cartoes)) {
                $cardId = $cartoes[0]['id'];
            }
        }
        
        return [
            'resumo' => $this->model->getResumo($userId, $cardId),
            'transacoes' => $this->model->getTransacoes($userId, $cardId),
            'expensesByCategory' => $this->model->getExpensesByCategory($userId, $cardId)
        ];
    }
}
?>