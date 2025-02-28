<?php
$host = "localhost";
$user = "root";  // Usuário padrão do XAMPP
$pass = "";      // Senha vazia no XAMPP
$dbname = "biblioteca_escolar";

// Conexão com o banco de dados
$conn = new mysqli($host, $user, $pass, $dbname);

// Verifica se houve erro na conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}
?>
