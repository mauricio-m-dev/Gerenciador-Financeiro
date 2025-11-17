# Integração MVC - Investimentos

## Estrutura do Projeto

```
Gerenciador-Financeiro-1/
├── app/
│   ├── Controllers/
│   │   └── InvestimentoController.php
│   ├── Models/
│   │   ├── Ativo.php
│   │   └── InvestimentoTransacao.php
│   └── init.php
├── api/
│   └── investimento.php
├── config/
│   └── Database.php
├── template/
│   └── asset/
│       ├── css/
│       │   └── Investimento.css
│       └── js/
│           └── Investimento.js
├── View/
│   └── Investimento.php
└── README.md
```

## Passo 1: Configurar o Banco de Dados

Execute os seguintes comandos SQL no seu MySQL:

```sql
-- Tabela 1: Ativos
CREATE TABLE Ativos (
    ativo_id INT AUTO_INCREMENT PRIMARY KEY,
    asset_symbol VARCHAR(20) NOT NULL UNIQUE,
    asset_name VARCHAR(255) NOT NULL,
    asset_type VARCHAR(100) NOT NULL,
    asset_sector VARCHAR(100)
);

-- Tabela 2: InvestimentoTransacoes
CREATE TABLE InvestimentoTransacoes (
    transacao_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    ativo_id INT NOT NULL,
    quantidade INT NOT NULL,
    valor_unitario DECIMAL(10, 2) NOT NULL,
    valor_total DECIMAL(10, 2) NOT NULL,
    tipo_transacao ENUM('compra', 'venda') NOT NULL DEFAULT 'compra',
    data_transacao DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ativo_id) REFERENCES Ativos(ativo_id)
);
```

## Passo 2: Configurar a Conexão do Banco

Edite o arquivo `config/Database.php` com as suas credenciais:

```php
private const DB_HOST = 'localhost';
private const DB_NAME = 'gerenciador_financeiro'; // Seu banco de dados
private const DB_USER = 'root';
private const DB_PASS = '';
```

## Passo 3: Entender a Arquitetura MVC

### Models (app/Models/)
- `Ativo.php` - Gerencia operações com a tabela Ativos
- `InvestimentoTransacao.php` - Gerencia operações com a tabela InvestimentoTransacoes

### Controllers (app/Controllers/)
- `InvestimentoController.php` - Lógica de negócio (comprar, vender, obter carteira, etc)

### Config (config/)
- `Database.php` - Conexão com o banco de dados usando PDO

### API (api/)
- `investimento.php` - Endpoints REST para o frontend

## Passo 4: Como Funciona a Integração

1. **Frontend (Investimento.js)** - Envia requisições AJAX para a API
2. **API (api/investimento.php)** - Recebe a requisição e valida os dados
3. **Controller (InvestimentoController)** - Processa a lógica de negócio
4. **Model (Ativo, InvestimentoTransacao)** - Executa operações no banco de dados
5. **Frontend** - Recebe a resposta em JSON e atualiza a página

## Endpoints da API

### Comprar Investimento
```
POST /api/investimento.php?acao=comprar
Body: {
  "asset_symbol": "PETR4",
  "quantidade": 10,
  "valor_unitario": 30.50
}
```

### Vender Investimento
```
POST /api/investimento.php?acao=vender
Body: {
  "ativo_id": 1,
  "quantidade": 5,
  "valor_unitario": 32.00
}
```

### Obter Carteira
```
GET /api/investimento.php?acao=carteira
Response: {
  "sucesso": true,
  "carteira": [
    {
      "ativo_id": 1,
      "asset_symbol": "PETR4",
      "asset_name": "Petrobras PN",
      "total_cotas": 10,
      "valor_medio": 30.50,
      "valor_investido": 305.00
    }
  ]
}
```

### Obter Histórico de Transações
```
GET /api/investimento.php?acao=historico
```

### Obter Estatísticas
```
GET /api/investimento.php?acao=estatisticas
Response: {
  "sucesso": true,
  "estatisticas": {
    "patrimonio_total": 305.00,
    "qtd_ativos": 1,
    "carteira": [...]
  }
}
```

## Passo 5: Autenticação (IMPORTANTE)

⚠️ **Atualmente, a API usa `user_id = 1` como padrão para testes.**

Para integrar com seu sistema de autenticação, altere no arquivo `api/investimento.php`:

```php
// Antes (teste):
$userId = $_GET['user_id'] ?? 1;

// Depois (com autenticação):
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['erro' => 'Não autenticado']);
    exit;
}
$userId = $_SESSION['user_id'];
```

## Passo 6: Testar a Integração

1. Acesse a página `View/Investimento.php`
2. Clique em "Adicionar Investimento"
3. Selecione uma ação (ex: PETR4)
4. Informe a quantidade e confirme
5. Verifique se a ação aparece na tabela "Seus ativos"

## Troubleshooting

### Erro: "Erro ao conectar ao banco de dados"
- Verifique se o MySQL está rodando
- Confirme as credenciais em `config/Database.php`
- Verifique se o banco de dados existe

### Erro: "Método não permitido"
- Certifique-se de usar POST para comprar/vender
- Use GET para carteira/histórico

### A carteira não atualiza
- Abra o console do navegador (F12) para ver erros
- Verifique se a API está respondendo corretamente

## Próximos Passos

1. Integrar com sistema de autenticação real
2. Adicionar validação de inputs no frontend
3. Implementar filtros na carteira (por tipo de ativo, setor, etc)
4. Adicionar gráficos com Chart.js baseados nos dados reais
5. Implementar edição e exclusão de transações
