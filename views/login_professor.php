<?php
session_start();
require_once "../database/db.php"; // Conexão com o banco de dados

$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rg = $_POST["rg"];
    $senha = $_POST["senha"];

    // Verifica se o professor existe
    $stmt = $conn->prepare("SELECT id, nome, senha FROM professor WHERE rg = ?");
    $stmt->bind_param("s", $rg);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows == 1) {
        $professor = $resultado->fetch_assoc();
        // Verifica se a senha está correta
        if (password_verify($senha, $professor["senha"])) {
            $_SESSION["professor"] = ["id" => $professor["id"], "nome" => $professor["nome"]];
            header("Location: painel_professor.php");
            exit();
        } else {
            $erro = "RG ou senha incorretos.";
        }
    } else {
        $erro = "RG ou senha incorretos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login do Professor</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>Login do Professor</h2>
    <?php if ($erro) echo "<p style='color:red;'>$erro</p>"; ?>

    <form method="POST">
        <label for="rg">RG:</label>
        <input type="text" id="rg" name="rg" required>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>

        <button type="submit">Entrar</button>
    </form>

    <a href="index.php">Voltar</a>
</body>
</html>
