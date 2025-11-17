# 🚀 Guia de Início Rápido

## ⏱️ 5 Minutos para Começar

### Passo 1️⃣ - Criar as Tabelas (1 min)

Abra seu cliente MySQL (phpMyAdmin, MySQL Workbench, etc) e execute:

```sql
-- Criar banco de dados (se não existir)
CREATE DATABASE IF NOT EXISTS gerenciador_financeiro;
USE gerenciador_financeiro;

-- Tabela de Ativos
CREATE TABLE Ativos (
    ativo_id INT AUTO_INCREMENT PRIMARY KEY,
    asset_symbol VARCHAR(20) NOT NULL UNIQUE,
    asset_name VARCHAR(255) NOT NULL,
    asset_type VARCHAR(100) NOT NULL,
    asset_sector VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabela de Transações
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

### Passo 2️⃣ - Configurar Conexão (1 min)

Edite `config/Database.php` com suas credenciais:

```php
private const DB_HOST = 'localhost';
private const DB_NAME = 'gerenciador_financeiro';  // ← Seu banco
private const DB_USER = 'root';                   // ← Seu usuário
private const DB_PASS = '';                       // ← Sua senha
```

---

### Passo 3️⃣ - Inserir Dados de Teste (1 min)

Execute `dados_teste.sql` no MySQL para inserir dados de exemplo:

```sql
-- De: dados_teste.sql
INSERT INTO Ativos (asset_symbol, asset_name, asset_type, asset_sector) VALUES
('PETR4', 'Petrobras PN', 'Ação', 'Energia'),
('VALE3', 'Vale ON', 'Ação', 'Mineração'),
('ITUB4', 'Itaú Unibanco PN', 'Ação', 'Financeiro');

INSERT INTO InvestimentoTransacoes (user_id, ativo_id, quantidade, valor_unitario, valor_total, tipo_transacao) VALUES
(1, 1, 45, 30.50, 1372.50, 'compra'),
(1, 2, 30, 85.20, 2556.00, 'compra'),
(1, 3, 20, 28.90, 578.00, 'compra');
```

---

### Passo 4️⃣ - Testar (1 min)

Abra seu navegador:

```
http://localhost/Gerenciador-Financeiro-1/teste_banco.php
```

Verifique se aparece:
- ✅ Conexão com banco de dados OK
- ✅ 3 Ativos listados
- ✅ 3 Investimentos na carteira
- ✅ Patrimônio total: R$ 4.506,50

---

### Passo 5️⃣ - Usar! (1 min)

Acesse a página:

```
http://localhost/Gerenciador-Financeiro-1/View/Investimento.php
```

Veja:
- 📊 Total de ativos: 3
- 📈 Carteira com todos os investimentos
- ➕ Botão "Adicionar Investimento"

---

## 🎯 Teste Completo

### Teste 1: Comprar uma ação

1. Clique em "Adicionar Investimento"
2. Selecione **PETR4**
3. Quantidade: **10**
4. Valor: **30.50** (auto-preenchido)
5. Clique em **Salvar**
6. ✅ Deve aparecer na tabela: **PETR4** com **55 cotas** (45 + 10)

### Teste 2: Verificar dados no banco

```sql
SELECT * FROM InvestimentoTransacoes ORDER BY data_transacao DESC;
```

Deve aparecer a nova transação.

### Teste 3: Testar carregamento automático

1. Recarregue a página (F5)
2. A carteira deve aparecer do banco de dados
3. Não há dados mockados, tudo vem do BD

---

## 🔍 Verificar Tudo Funciona

### ✅ Conexão
```php
// Abra teste_banco.php - deve conectar sem erros
http://localhost/Gerenciador-Financeiro-1/teste_banco.php
```

### ✅ API
```javascript
// No console do navegador (F12):
fetch('../api/investimento.php?acao=carteira')
  .then(r => r.json())
  .then(d => console.log(d));
// Deve retornar carteira em JSON
```

### ✅ View
```
http://localhost/Gerenciador-Financeiro-1/View/Investimento.php
```
Deve mostrar os investimentos da tabela.

---

## 📚 Próximas Leituras

1. **[RESUMO_INTEGRACAO.md](RESUMO_INTEGRACAO.md)** - Visão geral do projeto
2. **[ARQUITETURA.md](ARQUITETURA.md)** - Fluxo de requisições
3. **[INTEGRACAO_MVC.md](INTEGRACAO_MVC.md)** - Detalhes técnicos
4. **[PROXIMOS_PASSOS.md](PROXIMOS_PASSOS.md)** - O que fazer agora
5. **[exemplo_autenticacao.php](exemplo_autenticacao.php)** - Integrar login

---

## ⚡ Comandos Rápidos

### Ver todos os arquivos criados
```bash
cd c:\xampp\htdocs\Gerenciador-Financeiro-1
dir /s /b
```

### Testar no navegador
```
http://localhost/Gerenciador-Financeiro-1/teste_banco.php
```

### Acessar a página
```
http://localhost/Gerenciador-Financeiro-1/View/Investimento.php
```

### Verificar erros do PHP
- Abra: `C:\xampp\apache\logs\error.log`
- Ou configure `display_errors = On` em `php.ini`

---

## 🆘 Problemas Comuns

| Problema | Solução |
|----------|---------|
| Conexão recusada | Verifique credenciais em `config/Database.php` |
| Banco não existe | Crie o banco com `CREATE DATABASE` |
| Tabelas vazias | Execute `dados_teste.sql` |
| Página em branco | Verifique erros do PHP (F12 ou log) |
| API retorna erro 500 | Abra console F12 e verifique resposta |
| Investimento não salva | Verifique `user_id` na API |

---

## 🎓 Entender o Código

### Como um investimento é salvo:

```javascript
// 1. JavaScript (Investimento.js)
fetch('../api/investimento.php?acao=comprar', {
    method: 'POST',
    body: JSON.stringify({
        asset_symbol: 'PETR4',
        quantidade: 10,
        valor_unitario: 30.50
    })
})
```

```php
// 2. API (api/investimento.php)
$resultado = $investimentoController->adicionarInvestimento(1, 'PETR4', 10, 30.50);
```

```php
// 3. Controller (InvestimentoController.php)
$ativo = $this->ativoModel->buscarPorSymbol('PETR4');
$this->transacaoModel->criar($dados);
```

```php
// 4. Model (InvestimentoTransacao.php)
$stmt = $this->pdo->prepare("INSERT INTO InvestimentoTransacoes ...");
$stmt->execute();
```

```sql
-- 5. Banco de Dados
INSERT INTO InvestimentoTransacoes 
(user_id, ativo_id, quantidade, valor_unitario, valor_total, tipo_transacao)
VALUES (1, 1, 10, 30.50, 305.00, 'compra');
```

---

## 🚀 Assim que Funcionar

### Próxima Prioridade: Autenticação

Arquivo: `exemplo_autenticacao.php`

Mudanças necessárias:
1. Criar tabela de usuários
2. Implementar login
3. Usar `$_SESSION['user_id']` em vez de `$_GET['user_id']`

---

## 📞 Perguntas Frequentes

**P: Posso usar com outro banco de dados?**
R: Sim! Mude em `config/Database.php` para PostgreSQL, SQLite, etc.

**P: Preciso de autenticação?**
R: Sim, para produção. Veja `exemplo_autenticacao.php`

**P: Posso integrar com a API Brapi?**
R: Sim! O código já tem suporte. Veja `Investimento.js` linha ~150

**P: Como faço backup?**
R: Execute: `mysqldump -u root gerenciador_financeiro > backup.sql`

**P: Quantos usuários suporta?**
R: Ilimitado! Basta adicionar registros com `user_id` diferente.

---

## ✨ Resumo do Que Foi Feito

| Componente | Status | Arquivo |
|-----------|--------|---------|
| Database | ✅ Pronto | `config/Database.php` |
| Models | ✅ Pronto | `app/Models/*.php` |
| Controller | ✅ Pronto | `app/Controllers/*.php` |
| API | ✅ Pronto | `api/investimento.php` |
| Frontend | ✅ Pronto | `template/asset/js/*.js` |
| View | ✅ Pronto | `View/Investimento.php` |
| Testes | ✅ Pronto | `teste_banco.php` |
| Docs | ✅ Pronto | `*.md` |

---

## 🎉 Próximo Passo

Leia: **[PROXIMOS_PASSOS.md](PROXIMOS_PASSOS.md)**

ou

Implemente: **Autenticação Real** (Veja `exemplo_autenticacao.php`)

---

**Tempo total:** ~5-10 minutos ⏱️
**Dificuldade:** Fácil ✅
**Status:** Pronto para usar 🚀

Divirta-se! 🎊
