<?php
session_start();
require_once "../database/db.php"; // Conexão com o banco de dados

// Verifica se o admin está logado
if (!isset($_SESSION['admin'])) {
    header("Location: login_admin.php");
    exit();
}

$erro = "";
$sucesso = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $rg = $_POST["rg"];
    $senha = $_POST["senha"];

    // Verifica se o RG já existe
    $stmt = $conn->prepare("SELECT id FROM professor WHERE rg = ?");
    $stmt->bind_param("s", $rg);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $erro = "Este RG já está cadastrado!";
    } else {
        // Criptografa a senha
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        // Insere no banco de dados
        $stmt = $conn->prepare("INSERT INTO professor (nome, rg, senha) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nome, $rg, $senhaHash);

        if ($stmt->execute()) {
            $sucesso = "Professor cadastrado com sucesso!";
        } else {
            $erro = "Erro ao cadastrar o professor.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Professor</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>Cadastrar Professor</h2>
    <a href="painel_admin.php">Voltar</a>

    <?php 
    if ($erro) echo "<p style='color:red;'>$erro</p>"; 
    if ($sucesso) echo "<p style='color:green;'>$sucesso</p>"; 
    ?>

    <form method="POST">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required>

        <label for="rg">RG:</label>
        <input type="text" id="rg" name="rg" required>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>

        <button type="submit">Cadastrar</button>
    </form>
</body>
</html>
