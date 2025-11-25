<?php

use PHPUnit\Framework\TestCase;
use Model\CartaoModel;
use Model\User;
use Model\Connection;

class CartaoTest extends TestCase
{
    private $cartaoModel;
    private $userModel;
    private $conexao;
    private $usuarioId;
    private $cartaoId;
    private $emailTeste;

    protected function setUp(): void
    {
        $this->conexao = Connection::getInstance();
        $this->cartaoModel = new CartaoModel($this->conexao);
        $this->userModel = new User();

        // Criar usuário
        $this->emailTeste = 'cartao_' . uniqid() . '@teste.com';
        $this->userModel->registerUser('Usuario Cartao', $this->emailTeste, '123456');
        
        $dadosUsuario = $this->userModel->getUserByEmail($this->emailTeste);
        $this->usuarioId = $dadosUsuario['id'];

        // Pegar o cartão padrão criado automaticamente
        $cartoes = $this->cartaoModel->getAllCards($this->usuarioId);
        $this->cartaoId = $cartoes[0]['id'];
    }

    protected function tearDown(): void
    {
        if ($this->usuarioId) {
            $stmt = $this->conexao->prepare("DELETE FROM usuarios WHERE id = ?");
            $stmt->execute([$this->usuarioId]);
        }
    }

    public function testeListarCartoes()
    {
        $cartoes = $this->cartaoModel->getAllCards($this->usuarioId);
        $this->assertNotEmpty($cartoes);
        $this->assertEquals('Carteira Principal', $cartoes[0]['nome']);
    }

    public function testeResumoDoCartao()
    {
        // O resumo deve começar zerado
        $resumo = $this->cartaoModel->getResumo($this->usuarioId, $this->cartaoId);
        
        $this->assertEquals(0, $resumo['renda']);
        $this->assertEquals(0, $resumo['despesa']);
        $this->assertEquals(0, $resumo['saldo']);
    }
}
