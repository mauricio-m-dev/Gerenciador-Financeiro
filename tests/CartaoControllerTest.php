<?php

use PHPUnit\Framework\TestCase;
use Controller\CartaoController;
use Model\CartaoModel;

class CartaoControllerTest extends TestCase
{
    private $cartaoController;
    private $cartaoModelMock;

    protected function setUp(): void
    {
        $this->cartaoModelMock = $this->createMock(CartaoModel::class);
        $this->cartaoController = new CartaoController($this->cartaoModelMock);
    }

    public function testeIndexSemCartoes()
    {
        $userId = 1;
        
        $this->cartaoModelMock->method('getAllCards')
             ->willReturn([]);

        $resultado = $this->cartaoController->index($userId);

        $this->assertIsArray($resultado);
        $this->assertEmpty($resultado['cartoes']);
        $this->assertEquals(0, $resultado['activeCardId']);
        $this->assertEquals(0, $resultado['resumo']['saldo']);
    }

    public function testeIndexComCartoes()
    {
        $userId = 1;
        $cartoes = [['id' => 10, 'nome' => 'Cartão Teste']];

        $this->cartaoModelMock->method('getAllCards')
             ->willReturn($cartoes);
             
        $this->cartaoModelMock->method('getResumo')
             ->willReturn(['renda' => 100, 'despesa' => 50, 'meta' => 0, 'saldo' => 50]);
             
        $this->cartaoModelMock->method('getTransacoes')
             ->willReturn([]);
             
        $this->cartaoModelMock->method('getExpensesByCategory')
             ->willReturn([]);

        $resultado = $this->cartaoController->index($userId);

        $this->assertIsArray($resultado);
        $this->assertCount(1, $resultado['cartoes']);
        $this->assertEquals(10, $resultado['activeCardId']);
        $this->assertEquals(50, $resultado['resumo']['saldo']);
    }
}
