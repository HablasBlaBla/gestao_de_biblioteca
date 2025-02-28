<?php
// Definindo as configurações para a conexão com o banco de dados
$host = "localhost";
$dbname = "crud"; // Nome do banco de dados
$username = "root"; // Usuário padrão do XAMPP
$password = ""; // Senha padrão do XAMPP (em branco)

// Tentativa de conexão com o banco de dados usando PDO
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Configura o PDO para lançar exceções em caso de erro
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Echo para verificar se a conexão foi bem-sucedida (pode ser removido depois)
    // echo "Conexão bem-sucedida!";
} catch (PDOException $e) {
    // Caso a
