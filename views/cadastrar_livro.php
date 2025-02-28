<?php
session_start();
require_once "../database/db.php"; // Conexão com o banco de dados

// Verifica se o professor está logado
if (!isset($_SESSION['professor'])) {
    header("Location: login_professor.php");
    exit();
}

$erro = "";
$sucesso = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nomeLivro = $_POST['nomeLivro'];
    $nomeAutor = $_POST['nomeAutor'];

    if (!empty($nomeLivro) && !empty($nomeAutor)) {
        $stmt = $conn->prepare("INSERT INTO livro (nomeLivro, nomeAutor) VALUES (?, ?)");
        $stmt->bind_param("ss", $nomeLivro, $nomeAutor);

        if ($stmt->execute()) {
            $sucesso = "Livro cadastrado com sucesso!";
        } else {
            $erro = "Erro ao cadastrar o livro!";
        }

        $stmt->close();
    } else {
        $erro = "Todos os campos são obrigatórios!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Livro</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>Cadastrar Livro</h2>
    <a href="painel_professor.php">Voltar</a>

    <?php 
    if ($erro) echo "<p style='color:red;'>$erro</p>"; 
    if ($sucesso) echo "<p style='color:green;'>$sucesso</p>"; 
    ?>

    <form method="POST">
        <label>Nome do Livro:</label>
        <input type="text" name="nomeLivro" required>
        <br>
        <label>Autor:</label>
        <input type="text" name="nomeAutor" required>
        <br>
        <button type="submit">Cadastrar</button>
    </form>
</body>
</html>
