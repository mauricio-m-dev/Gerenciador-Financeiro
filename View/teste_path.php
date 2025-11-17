<?php
/**
 * Teste rápido do caminho da API
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>Teste de Path</title>
</head>
<body>
    <h1>Teste de Path da API</h1>
    <p>Testando os caminhos possíveis...</p>

    <h2>Teste 1: ../api/investimento_mock.php</h2>
    <p id="test1">Carregando...</p>

    <h2>Teste 2: /Gerenciador-Financeiro-1/api/investimento_mock.php</h2>
    <p id="test2">Carregando...</p>

    <h2>Teste 3: /api/investimento_mock.php</h2>
    <p id="test3">Carregando...</p>

    <script>
        // Teste 1
        fetch('../api/investimento_mock.php?acao=carteira')
            .then(r => r.json())
            .then(d => {
                document.getElementById('test1').innerHTML = '✅ OK: ' + d.sucesso;
            })
            .catch(e => {
                document.getElementById('test1').innerHTML = '❌ Erro: ' + e.message;
            });

        // Teste 2
        fetch('/Gerenciador-Financeiro-1/api/investimento_mock.php?acao=carteira')
            .then(r => r.json())
            .then(d => {
                document.getElementById('test2').innerHTML = '✅ OK: ' + d.sucesso;
            })
            .catch(e => {
                document.getElementById('test2').innerHTML = '❌ Erro: ' + e.message;
            });

        // Teste 3
        fetch('/api/investimento_mock.php?acao=carteira')
            .then(r => r.json())
            .then(d => {
                document.getElementById('test3').innerHTML = '✅ OK: ' + d.sucesso;
            })
            .catch(e => {
                document.getElementById('test3').innerHTML = '❌ Erro: ' + e.message;
            });
    </script>
</body>
</html>
