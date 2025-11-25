<?php

use PHPUnit\Framework\TestCase;
use Controller\AnaliseController;
use Model\AnaliseModel;

class TestableAnaliseController extends AnaliseController
{
    public function __construct($modelMock)
    {
        $reflection = new ReflectionClass($this);
        $property = $reflection->getProperty('model');
        $property->setAccessible(true);
        $property->setValue($this, $modelMock);
    }
}

class AnaliseControllerTest extends TestCase
{
    private $analiseController;
    private $analiseModelMock;

    protected function setUp(): void
    {
        $this->analiseModelMock = $this->createMock(AnaliseModel::class);
        $this->analiseController = new TestableAnaliseController($this->analiseModelMock);
    }

    public function testeIndexResumo()
    {
        $usuarioId = 1;
        $mes = 11;
        $ano = 2025;

        // Mock dos métodos chamados no index
        $this->analiseModelMock->method('getResumoMes')
             ->willReturn(['renda' => 5000, 'despesa' => -2000]);
             
        $this->analiseModelMock->method('getTotalMetas')
             ->willReturn(100);
             
        $this->analiseModelMock->method('getTotalInvestimentos')
             ->willReturn(500);
             
        $this->analiseModelMock->method('getEvolucaoSemestral')
             ->willReturn([]);
             
        $this->analiseModelMock->method('getDespesasPorCategoria')
             ->willReturn([]);

        $resultado = $this->analiseController->index($usuarioId, $mes, $ano);

        $this->assertIsArray($resultado);
        $this->assertEquals(5000, $resultado['renda']);
        $this->assertEquals(2000, $resultado['despesa']); // abs(-2000)
    }
}
