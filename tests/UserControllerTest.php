<?php

use PHPUnit\Framework\TestCase;
use Controller\UserController;
use Model\User;

class UserControllerTest extends TestCase
{
    private $userController;
    private $userModelMock;

    protected function setUp(): void
    {
        $this->userModelMock = $this->createMock(User::class);
        $this->userController = new UserController($this->userModelMock);
    }

    public function testeLoginComSucesso()
    {
        $email = 'teste@teste.com';
        $senha = '123456';
        $hash = password_hash($senha, PASSWORD_DEFAULT);

        // Simular retorno do banco
        $this->userModelMock->method('getUserByEmail')
             ->willReturn([
                 'id' => 1,
                 'nome' => 'Teste',
                 'email' => $email,
                 'senha_hash' => $hash
             ]);

        // Mock da sessão (PHPUnit roda em CLI, session_start pode dar erro ou aviso)
        // O controller tenta iniciar sessão. Vamos suprimir warnings de header.
        @$resultado = $this->userController->login($email, $senha);

        $this->assertTrue($resultado, 'Login deveria ter sucesso com senha correta');
        
        // Verificar se a sessão foi "populada" (simulado)
        if (session_status() === PHP_SESSION_ACTIVE) {
            $this->assertEquals(1, $_SESSION['id']);
        }
    }

    public function testeLoginComSenhaIncorreta()
    {
        $email = 'teste@teste.com';
        $senha = 'errada';
        $hash = password_hash('certa', PASSWORD_DEFAULT);

        $this->userModelMock->method('getUserByEmail')
             ->willReturn([
                 'id' => 1,
                 'nome' => 'Teste',
                 'email' => $email,
                 'senha_hash' => $hash
             ]);

        $resultado = $this->userController->login($email, $senha);

        $this->assertFalse($resultado, 'Login deveria falhar com senha incorreta');
    }
}
