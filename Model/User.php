<?php

namespace Model;

use Model\Connection;

use PDO;
use PDOException;
use Exception;

class User
{
    private $db;

    /*
     * MÉTODO QUE IRÁ SER EXECUTADO TODA VEZ QUE
     * FOR CRIADO UM OBJETO DA CLASSE -> USER
     */

    public function __construct()
    {
        $this->db = Connection::getInstance();
    }

    // FUNÇÃO DE CRIAR USUÁRIO
    public function registerUser($user_fullname, $email, $password)
    {
        try {
            // INSERÇÃO DE DADOS NA LINGUAGEM SQL
            $sql = 'INSERT INTO user (user_fullname, email, password, created_at) VALUES (:user_fullname, :email, :password, NOW())';

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // PREPARAR O BANCO DE DADOS PARA RECEBER O COMANDO ACIMA
            $stmt = $this->db->prepare($sql);

            // REFERENCIAR OS DADOS PASSADOS PELO COMANDO SQL COM OS PARÂMETROS DA FUNÇÃO
            $stmt->bindParam(":user_fullname", $user_fullname, PDO::PARAM_STR);
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);
            $stmt->bindParam(":password", $hashedPassword, PDO::PARAM_STR);

            // EXECUTAR TUDO

            return $stmt->execute();

        } catch (PDOException $error) {
            // EXIBIR MENSAGEM DE ERRO COMPLETA E PARAR A EXECUÇÃO
            echo "Erro ao executar o comando " . $error->getMessage();
            return false;
        }
    }

    // LOGIN
    public function getUserByEmail($email)
    {
        try {
            $sql = "SELECT * FROM user WHERE email = :email LIMIT 1";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(":email", $email, PDO::PARAM_STR);

            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $error) {
        }
    }

    // OBTER INFORMAÇÕES DO USUÁRIO
    public function getUserInfo($id, $user_fullname, $email)
    {
        try {
            $sql = "SELECT user_fullname, email FROM user WHERE id = :id AND user_fullname = :user_fullname AND email = :email";

            $stmt = $this->db->prepare($sql);

            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->bindParam(":user_fullname", $user_fullname, PDO::PARAM_STR);
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);

            $stmt->execute();


            /**
             * fetch = querySelector();
             * fetchAll = querySelectorAll();
             * 
             * FETCH_ASSOC:
             * $user[
             *  "user_fullname" => "teste",
             *  "email" => "teste@example.com"
             * ]
             * 
             * 
             * 
             * COMO OBTER INFORMAÇÕES:
             * $user['user_fullname'];
             */

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $error) {
            echo "Erro ao buscar informações: " . $error->getMessage();
            return false;
        }
    }
}

?>