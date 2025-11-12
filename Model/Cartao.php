<?php

namespace Model;

use Model\Connection;

use PDO;
use PDOException;

class Cartao {
    private $db;
    public function __construct() {
        $this->db = Connection::getInstance();
    }

    public function getCard($id): array {
        try {
            $sql = "SELECT descricao, quantia, metodo, data, categoria FROM transacoes WHERE id = :id LIMIT 1";
            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch(PDOException $e) {
            echo "Erro ao buscar cartão: " . $e->getMessage();
            return [];
        }
    }
}