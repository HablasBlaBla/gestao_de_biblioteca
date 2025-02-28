<?php
session_start();
if (!isset($_SESSION['professor'])) {
    header("Location: login_professor.php");
    exit();
}

$nome = $_SESSION['professor']['nome'];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Professor</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>Bem-vindo, <?php echo htmlspecialchars($nome); ?>!</h2>

    <ul>
        <li><a href="cadastrar_aluno.php">Cadastrar Aluno</a></li>
        <li><a href="cadastrar_livro.php">Cadastrar Livro</a></li>
        <li><a href="gerenciar_emprestimos.php">Gerenciar Empréstimos</a></li>
        <li><a href="logout.php">Sair</a></li>
    </ul>
</body>
</html>
