<?php

use PHPUnit\Framework\TestCase;
use Controller\InvestimentoController;
use Model\Ativo;
use Model\Investimento;

// Subclasse para injetar mocks, já que o construtor original instancia as classes
class TestableInvestimentoController extends InvestimentoController
{
    public function __construct($ativoMock, $investimentoMock)
    {
        // Injetar via Reflection
        $reflection = new ReflectionClass($this);
        
        $propAtivo = $reflection->getProperty('ativoModel');
        $propAtivo->setAccessible(true);
        $propAtivo->setValue($this, $ativoMock);
        
        $propInv = $reflection->getProperty('investimentoModel');
        $propInv->setAccessible(true);
        $propInv->setValue($this, $investimentoMock);
    }
}

class InvestimentoControllerTest extends TestCase
{
    private $investimentoController;
    private $ativoMock;
    private $investimentoMock;

    protected function setUp(): void
    {
        $this->ativoMock = $this->createMock(Ativo::class);
        $this->investimentoMock = $this->createMock(Investimento::class);
        $this->investimentoController = new TestableInvestimentoController($this->ativoMock, $this->investimentoMock);
    }

    public function testeIndexCarteiraVazia()
    {
        $usuarioId = 1;

        $this->investimentoMock->method('obterCarteira')
             ->willReturn([]);
             
        $this->investimentoMock->method('obterHistorico')
             ->willReturn([]);

        $resultado = $this->investimentoController->index($usuarioId);

        $this->assertIsArray($resultado);
        $this->assertEmpty($resultado['carteira']);
        $this->assertEquals(0, $resultado['patrimonioTotal']);
    }

    public function testeAdicionarInvestimento()
    {
        $usuarioId = 1;
        $dados = [
            'ticker' => 'PETR4',
            'quantidade' => 10,
            'valor' => 30.00,
            'tipo_operacao' => 'compra'
        ];

        // Simular que o ativo já existe
        $this->ativoMock->method('buscarPorSimbolo')
             ->willReturn(['id' => 1, 'simbolo' => 'PETR4']);

        // Simular sucesso na transação
        $this->investimentoMock->method('adicionarTransacao')
             ->willReturn(true);

        $resultado = $this->investimentoController->adicionar($dados, $usuarioId);

        $this->assertTrue($resultado['sucesso']);
        $this->assertEquals('Operação registrada com sucesso!', $resultado['msg']);
    }
}
