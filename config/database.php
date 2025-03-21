<?php
// Configuração de conexão com o banco de dados
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // ou outro usuário
define('DB_PASSWORD', '');     // senha
define('DB_DATABASE', 'biblioteca'); // nome do banco

try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_DATABASE, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro ao conectar: " . $e->getMessage();
}
?>
