<?php

namespace Controller;

use Model\User;
use Exception;

class UserController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    // REGISTRO DE USUÁRIO
    public function createUser($user_fullname, $email, $password){
        if (empty($user_fullname) or empty($email) or empty($password)) {
            return false;
        }

        // $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        return $this->userModel->registerUser($user_fullname, $email, $password);
    }

    // E-MAIL JÁ CADASTRADO?
    public function checkUserByEmail($email){
        return $this->userModel->getUserByEmail($email);
    }

    // LOGIN DE USUÁRIO
    public function login($email, $password){
        $user = $this->userModel->getUserByEmail($email);

        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    // USUÁRIO LOGADO?
    public function isLoggedIn()
    {
        return isset($_SESSION['id']);
    }

    // RESGATAR DADOS DO USUÁRIO
    public function getUserData($id, $user_fullname, $email)
    {
        return $this->userModel->getUserInfo($id, $user_fullname, $email);
    }
}

?>