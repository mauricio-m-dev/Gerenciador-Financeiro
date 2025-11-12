<?php

namespace Controller;

use Exception;

use Model\Cartao;

class CartaoController {
    
    private $cartaoModel;
    
    public function __construct() {
        $this->cartaoModel = new Cartao();
    }

    public function getCardDetails($cartao_id) {
        try {
            $cardDetails = $this->cartaoModel->getCard($cartao_id);
            if ($cardDetails) {
                return $cardDetails;
            } else {
                throw new Exception("Cartão não encontrado.");
            }
        } catch (Exception $e) {
            echo "Erro ao obter detalhes do cartão: " . $e->getMessage();
            return false;
        }
    }
}
