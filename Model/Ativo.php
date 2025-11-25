<?php
namespace Model;

use PDO;

class Ativo
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // Busca ativo pelo código (Ex: PETR4)
    public function buscarPorSimbolo($simbolo)
    {
        $sql = "SELECT * FROM ativos WHERE simbolo = :simbolo LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':simbolo', strtoupper($simbolo));
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Busca ativo pelo ID
    public function buscarPorId($id)
    {
        $sql = "SELECT * FROM ativos WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Lista todos para um <select> ou busca
    public function listarTodos()
    {
        $sql = "SELECT * FROM ativos ORDER BY simbolo ASC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Cria um novo ativo se não existir (útil para o cadastro automático)
    public function criar($simbolo, $nome, $tipo, $setor) {
        $sql = "INSERT INTO ativos (simbolo, nome, tipo, setor) VALUES (:s, :n, :t, :set)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':s' => strtoupper($simbolo),
            ':n' => $nome,
            ':t' => $tipo,
            ':set' => $setor
        ]);
    }
}
?>