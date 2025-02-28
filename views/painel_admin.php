<?php
session_start();
require_once "../database/db.php"; // Conexão com o banco de dados

// Verifica se o administrador está logado
if (!isset($_SESSION['admin'])) {
    header("Location: login_admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Administrador</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>Painel do Administrador</h2>
    <a href="logout.php">Sair</a>
    
    <h3>Gerenciamento</h3>
    <ul>
        <li><a href="gerenciar_professores.php">Gerenciar Professores</a></li>
        <li><a href="gerenciar_alunos.php">Gerenciar Alunos</a></li>
        <li><a href="gerenciar_livros.php">Gerenciar Livros</a></li>
        <li><a href="gerenciar_emprestimos_admin.php">Gerenciar Empréstimos</a></li>
    </ul>
</body>
</html>
