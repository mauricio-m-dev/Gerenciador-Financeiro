<?php
// Configurações do banco de dados
$host = "localhost";
$db   = "meu_banco_de_dados_cartoes"; // nome do banco
$user = "root";       // seu usuário do MySQL
$pass = "Henrique81003990";           // sua senha do MySQL

// Criar conexão
$conn = new mysqli($host, $user, $pass, $db);

// Checar conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}
?>
