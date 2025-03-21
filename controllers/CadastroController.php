<?php
include('../config/database.php');
include('../models/Usuario.php');

session_start();

if ($_SESSION['perfil'] != 'Administrador') {
    // Se o usuário não for administrador, redireciona
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $perfil = 'Professor'; // Definir como 'Professor' no cadastro
    
    // Criação do novo usuário
    $usuario = new Usuario($pdo);
    $usuario->cadastrar($nome, $email, $senha, $perfil);
    
    // Redirecionar após o cadastro
    header('Location: perfil.php');
    exit;
}

?>

<form method="POST">
    <label for="nome">Nome:</label>
    <input type="text" name="nome" required>
    
    <label for="email">E-mail:</label>
    <input type="email" name="email" required>
    
    <label for="senha">Senha:</label>
    <input type="password" name="senha" required>
    
    <button type="submit">Cadastrar Professor</button>
</form>
