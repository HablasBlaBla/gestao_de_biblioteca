<?php
session_start();
require_once "../database/db.php"; // Conexão com o banco de dados

// Verifica se o administrador ou professor está logado
if (!isset($_SESSION['admin']) && !isset($_SESSION['professor'])) {
    header("Location: login_admin.php");
    exit();
}

$erro = "";
$sucesso = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $serie = $_POST['serie'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); // Criptografar a senha

    // Verifica se o e-mail já existe
    $stmt = $conn->prepare("SELECT id FROM aluno WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $erro = "Já existe um aluno com esse e-mail!";
    } else {
        // Insere no banco de dados
        $stmt = $conn->prepare("INSERT INTO aluno (nome, serie, email, senha) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nome, $serie, $email, $senha);
        if ($stmt->execute()) {
            $sucesso = "Aluno cadastrado com sucesso!";
        } else {
            $erro = "Erro ao cadastrar aluno!";
        }
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Aluno</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>Cadastrar Aluno</h2>
    <a href="painel_admin.php">Voltar</a>
    
    <?php if ($erro) echo "<p style='color:red;'>$erro</p>"; ?>
    <?php if ($sucesso) echo "<p style='color:green;'>$sucesso</p>"; ?>

    <form method="POST">
        <label>Nome:</label>
        <input type="text" name="nome" required>
        <br>
        <label>Série:</label>
        <input type="text" name="serie" required>
        <br>
        <label>Email:</label>
        <input type="email" name="email" required>
        <br>
        <label>Senha:</label>
        <input type="password" name="senha" required>
        <br>
        <button type="submit">Cadastrar</button>
    </form>
</body>
</html>
