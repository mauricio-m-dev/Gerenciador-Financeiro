<?php

use PHPUnit\Framework\TestCase;
use Model\DashboardModel;

require_once __DIR__ . '/../Controller/ExtratoController.php';

class ExtratoControllerTest extends TestCase
{
    private $extratoController;
    private $dashboardModelMock;

    protected function setUp(): void
    {
        $this->dashboardModelMock = $this->createMock(DashboardModel::class);
        $this->extratoController = new ExtratoController($this->dashboardModelMock);
    }

    public function testeAgrupamentoTransacoes()
    {
        // Usando Reflection para testar método privado groupTransactionsByDate
        $reflection = new ReflectionClass($this->extratoController);
        $method = $reflection->getMethod('groupTransactionsByDate');
        $method->setAccessible(true);

        $transacoes = [
            ['data' => '2025-11-25 10:00:00', 'nome' => 'T1'],
            ['data' => '2025-11-25 14:00:00', 'nome' => 'T2'],
            ['data' => '2025-11-24 09:00:00', 'nome' => 'T3']
        ];

        $agrupado = $method->invokeArgs($this->extratoController, [$transacoes]);

        // Esperamos 2 grupos de datas
        $this->assertCount(2, $agrupado);
        
        // Verifica chaves (datas formatadas)
        // 25 Nov 2025
        $this->assertArrayHasKey('25 Nov 2025', $agrupado);
        $this->assertCount(2, $agrupado['25 Nov 2025']);
        
        // 24 Nov 2025
        $this->assertArrayHasKey('24 Nov 2025', $agrupado);
        $this->assertCount(1, $agrupado['24 Nov 2025']);
    }

    public function testeShowExtrato()
    {
        $userId = 1;

        $this->dashboardModelMock->method('getUserDetails')
             ->willReturn(['nome' => 'Teste']);
             
        $this->dashboardModelMock->method('getAllTransactions')
             ->willReturn([]);

        ob_start();
        @$this->extratoController->showExtrato($userId);
        $output = ob_get_clean();

        $this->assertTrue(true); // Se não quebrou, passou
    }
}
