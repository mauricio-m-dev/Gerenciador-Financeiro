<?php

use PHPUnit\Framework\TestCase;
use Model\User;
use Model\Connection;

class UsuarioTest extends TestCase
{
    private $usuarioModel;
    private $emailTeste;
    private $conexao;

    protected function setUp(): void
    {
        $this->usuarioModel = new User();
        $this->conexao = Connection::getInstance();
        $this->emailTeste = 'teste_' . uniqid() . '@exemplo.com.br';
    }

    protected function tearDown(): void
    {
        if ($this->emailTeste) {
            $stmt = $this->conexao->prepare("DELETE FROM usuarios WHERE email = :email");
            $stmt->bindValue(':email', $this->emailTeste);
            $stmt->execute();
        }
    }

    public function testeRegistrarEBuscarUsuario()
    {
        $nome = 'Usuário Teste';
        $senha = 'senha123';

        $registrado = $this->usuarioModel->registerUser($nome, $this->emailTeste, $senha);
        $this->assertTrue($registrado);

        $usuario = $this->usuarioModel->getUserByEmail($this->emailTeste);
        $this->assertIsArray($usuario);
        $this->assertEquals($nome, $usuario['nome']);
        $this->assertEquals($this->emailTeste, $usuario['email']);
        $this->assertTrue(password_verify($senha, $usuario['senha_hash']));
    }
}
