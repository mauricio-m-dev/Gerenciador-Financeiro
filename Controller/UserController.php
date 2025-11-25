<?php
namespace Controller;

use Model\User;

class UserController
{
    private $userModel;

    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
    }

    public function login($email, $password)
    {
        $user = $this->userModel->getUserByEmail($email);

        // Verifica senha usando o hash do banco
        if ($user && password_verify($password, $user['senha_hash'])) {
            
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // ATENÇÃO: Salvando exatamente como o Dashboard espera
            $_SESSION['id'] = $user['id'];
            $_SESSION['nome'] = $user['nome'];
            $_SESSION['email'] = $user['email'];
            
            return true;
        }
        
        return false;
    }
    
    // ... manter o resto das funções (register, etc) ...
    public function createUser($nome, $email, $password) {
         return $this->userModel->registerUser($nome, $email, $password);
    }
    public function checkUserByEmail($email) {
        return $this->userModel->getUserByEmail($email);
    }
}
?>