<?php

use PHPUnit\Framework\TestCase;
use Model\DashboardModel;
use Model\User;
use Model\Connection;

class DashboardTest extends TestCase
{
    private $dashboardModel;
    private $userModel;
    private $conexao;
    private $usuarioId;
    private $emailTeste;

    protected function setUp(): void
    {
        $this->conexao = Connection::getInstance();
        $this->dashboardModel = new DashboardModel($this->conexao);
        $this->userModel = new User();
        
        // Criar usuário para teste
        $this->emailTeste = 'dash_' . uniqid() . '@teste.com';
        $this->userModel->registerUser('Usuario Dash', $this->emailTeste, '123456');
        
        // Pegar ID do usuário criado
        $dadosUsuario = $this->userModel->getUserByEmail($this->emailTeste);
        $this->usuarioId = $dadosUsuario['id'];
    }

    protected function tearDown(): void
    {
        // Limpar dados de teste
        if ($this->usuarioId) {
            $stmt = $this->conexao->prepare("DELETE FROM usuarios WHERE id = ?");
            $stmt->execute([$this->usuarioId]);
        }
    }

    public function testeObterDetalhesDoUsuario()
    {
        $detalhes = $this->dashboardModel->getUserDetails($this->usuarioId);
        $this->assertIsArray($detalhes);
        $this->assertEquals('Usuario Dash', $detalhes['nome']);
    }

    public function testeAdicionarEListarTransacoes()
    {
        // 1. Adicionar uma transação
        $dadosTransacao = [
            'usuario_id' => $this->usuarioId,
            'categoria_id' => 1, // Assumindo que categoria 1 existe (Salário)
            'nome' => 'Salário Teste',
            'quantia' => 5000.00,
            'tipo' => 'renda',
            'data' => date('Y-m-d H:i:s'),
            'metodo' => 'pix'
        ];

        $sucesso = $this->dashboardModel->addTransaction($dadosTransacao);
        $this->assertTrue($sucesso, 'Falha ao adicionar transação');

        // 2. Verificar totais globais
        $totais = $this->dashboardModel->getGlobalTotals($this->usuarioId);
        $this->assertEquals(5000.00, $totais['totalRenda']);
        $this->assertEquals(0, $totais['totalDespesas']);

        // 3. Verificar transações recentes
        $recentes = $this->dashboardModel->getRecentTransactions($this->usuarioId);
        $this->assertCount(1, $recentes);
        $this->assertEquals('Salário Teste', $recentes[0]['nome']);
    }
}
