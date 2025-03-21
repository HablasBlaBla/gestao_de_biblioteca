<?php
include('../config/database.php');
include('../includes/functions.php');
session_start();

// Verifique se o usuário é um administrador
if ($_SESSION['perfil'] != 'Administrador') {
    header('Location: index.php');
    exit;
}

// Processa o cadastro de professores
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Cria um novo professor
    $usuario = new Usuario($pdo);
    $usuario->cadastrar($nome, $email, $senha, 'Professor');
    
    // Após o cadastro, redireciona
    header('Location: cadastro_professor.php');
    exit;
}
?>

<!-- Formulário de Cadastro de Professores -->
<form method="POST">
    <label for="nome">Nome do Professor:</label>
    <input type="text" name="nome" required>
    
    <label for="email">E-mail do Professor:</label>
    <input type="email" name="email" required>
    
    <label for="senha">Senha:</label>
    <input type="password" name="senha" required>
    
    <button type="submit">Cadastrar Professor</button>
</form>
