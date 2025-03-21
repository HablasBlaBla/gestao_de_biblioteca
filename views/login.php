<?php
include('../config/database.php');
include('../includes/functions.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    
    // Verifica se as credenciais são válidas
    $usuario = validarLogin($email, $senha, $pdo);
    if ($usuario) {
        session_start();
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['perfil'] = $usuario['perfil'];
        
        // Redireciona para a página de livros ou dashboard
        header('Location: livros.php');
    } else {
        echo "Login inválido!";
    }
}
?>

<form method="POST">
    <input type="email" name="email" required placeholder="E-mail">
    <input type="password" name="senha" required placeholder="Senha">
    <button type="submit">Entrar</button>
</form>
