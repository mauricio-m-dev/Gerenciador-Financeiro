<?php

use PHPUnit\Framework\TestCase;
use Model\MetaModel;

// Precisamos incluir a classe antes de estender ou usar
require_once __DIR__ . '/../Controller/MetaController.php';

// Subclasse para expor o model e evitar conexão real no construtor original se necessário,
// mas o construtor original aceita $pdo. Vamos mockar o PDO?
// O construtor faz: $this->model = new MetaModel($pdo);
// Isso é ruim para teste unitário pois instancia a classe concreta.
// Vamos fazer um "TestableMetaController" que permite injetar o mock do model.

class TestableMetaController extends MetaController
{
    public function __construct($modelMock)
    {
        // Não chamamos o parent::__construct para evitar o new MetaModel($pdo)
        // Mas precisamos injetar o model na propriedade privada $model.
        // Como é privada, usaremos Reflection.
        
        $reflection = new ReflectionClass($this);
        $property = $reflection->getProperty('model');
        $property->setAccessible(true);
        $property->setValue($this, $modelMock);
    }
}

class MetaControllerTest extends TestCase
{
    private $metaController;
    private $metaModelMock;

    protected function setUp(): void
    {
        $this->metaModelMock = $this->createMock(MetaModel::class);
        $this->metaController = new TestableMetaController($this->metaModelMock);
    }

    public function testeIndexListarMetas()
    {
        $userId = 1;
        
        $dadosBrutos = [
            [
                'id' => 1,
                'usuario_id' => 1,
                'nome' => 'Viagem',
                'valor_objetivo' => 5000.00,
                'valor_atual' => 1000.00,
                'valor_contribuicao_mensal' => 200.00,
                'data_prazo' => '2025-12-31',
                'categoria' => 'Lazer',
                'cor' => '#FF0000',
                'historico_json' => '[]'
            ]
        ];

        $this->metaModelMock->method('getAllMetas')
             ->willReturn($dadosBrutos);

        $resultado = $this->metaController->index($userId);

        $this->assertIsArray($resultado);
        $this->assertCount(1, $resultado);
        $this->assertEquals('Viagem', $resultado[0]['nome']);
        $this->assertEquals(5000.00, $resultado[0]['objetivo']);
    }
    
    public function testeCalculoProgresso()
    {
        // Testando helper function pública
        $progresso = $this->metaController->calcular_progresso(50, 100);
        $this->assertEquals(50, $progresso);
        
        $progressoCheio = $this->metaController->calcular_progresso(200, 100);
        $this->assertEquals(100, $progressoCheio);
    }
}
