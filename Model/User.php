<?php
namespace Model;

use Model\Connection;
use PDO;
use PDOException;

class User
{
    private $db;

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }

    // REGISTRAR NOVO USUÁRIO
    // REGISTRAR NOVO USUÁRIO COM CARTÃO PADRÃO AUTOMÁTICO
    public function registerUser($nome, $email, $password)
    {
        try {
            // 1. Inicia uma transação (Tudo ou Nada)
            $this->db->beginTransaction();

            // ---------------------------------------------------------
            // PASSO A: Criar o Usuário
            // ---------------------------------------------------------
            $sqlUser = 'INSERT INTO usuarios (nome, email, senha_hash) VALUES (:nome, :email, :senha_hash)';
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $this->db->prepare($sqlUser);
            $stmt->bindValue(":nome", $nome, PDO::PARAM_STR);
            $stmt->bindValue(":email", $email, PDO::PARAM_STR);
            $stmt->bindValue(":senha_hash", $hashedPassword, PDO::PARAM_STR);

            if (!$stmt->execute()) {
                $this->db->rollBack(); // Cancela se falhar
                return false;
            }

            // Pega o ID do usuário que acabou de ser criado
            $novoUsuarioId = $this->db->lastInsertId();

            // ---------------------------------------------------------
            // PASSO B: Criar o Cartão Principal (Carteira)
            // ---------------------------------------------------------
            // Dados padrão: Nome "Carteira Principal", Final "0000", Validade +10 anos
            $sqlCartao = "INSERT INTO cartoes (usuario_id, nome, ultimos4, validade, bandeira, tipo, limite) 
                          VALUES (:uid, 'Carteira Principal', '0000', :validade, 'Sistema', 'debito', 0)";
            
            $validadeFutura = date('Y-m-d', strtotime('+10 years')); // Expira daqui a 10 anos

            $stmtCartao = $this->db->prepare($sqlCartao);
            $stmtCartao->bindValue(':uid', $novoUsuarioId, PDO::PARAM_INT);
            $stmtCartao->bindValue(':validade', $validadeFutura, PDO::PARAM_STR);
            
            if (!$stmtCartao->execute()) {
                $this->db->rollBack(); // Se falhar ao criar o cartão, desfaz a criação do usuário
                return false;
            }

            // ---------------------------------------------------------
            // SUCESSO: Confirma as alterações no banco
            // ---------------------------------------------------------
            $this->db->commit();
            return true;

        } catch (PDOException $error) {
            // Em caso de erro grave, desfaz tudo
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            // Você pode descomentar a linha abaixo para ver o erro na tela durante testes:
            // echo "Erro: " . $error->getMessage(); 
            return false;
        }
    }

    // BUSCAR USUÁRIO POR EMAIL (Usado no Login)
    public function getUserByEmail($email)
    {
        try {
            $sql = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            return false;
        }
    }

    // OBTER DADOS DO USUÁRIO PELO ID
    public function getUserInfo($id)
    {
        try {
            $sql = "SELECT id, nome, email, profile_pic_url FROM usuarios WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
            return false;
        }
    }
    public function getExpensesByCategory($usuarioId, $cardId = 0)
    {
        $data = [];

        // Base da query: soma apenas despesas (quantia < 0) e agrupa por categoria
        $sql = "SELECT c.nome as label, SUM(ABS(t.quantia)) as total 
                FROM transacoes t 
                JOIN categorias c ON t.categoria_id = c.id 
                WHERE t.usuario_id = :usuario_id AND t.quantia < 0";

        // Se tiver ID do cartão, adiciona o filtro
        if ($cardId > 0) {
            $sql .= " AND t.cartao_id = :cartao_id";
        }

        $sql .= " GROUP BY c.nome ORDER BY total DESC";

        $stmt = $this->db->prepare($sql);

        // Bind dos parâmetros
        $stmt->bindValue(':usuario_id', $usuarioId, PDO::PARAM_INT);

        if ($cardId > 0) {
            $stmt->bindValue(':cartao_id', $cardId, PDO::PARAM_INT);
        }

        if ($stmt->execute()) {
            // PDO: fetchAll pega todas as linhas de uma vez
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // Calcular porcentagens (Lógica PHP pura)
        $totalGeral = array_sum(array_column($data, 'total'));

        foreach ($data as &$item) {
            $item['porcentagem'] = $totalGeral > 0 ? ($item['total'] / $totalGeral) * 100 : 0;
            $item['porcentagem'] = round($item['porcentagem'], 2);
        }

        return $data;
    }
}



?>