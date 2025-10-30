<?php
// Configurações do banco de dados
$host = "localhost";
$db   = "financeiro"; // nome do banco
$user = "root";       // seu usuário do MySQL
$pass = "";           // sua senha do MySQL

// Criar conexão
$conn = new mysqli($host, $user, $pass, $db);

// Checar conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}
?>
