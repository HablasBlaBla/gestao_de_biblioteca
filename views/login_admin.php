<?php
session_start();
require_once "../database/db.php"; // Conexão com o banco de dados

$erro = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["usuario"];
    $senha = $_POST["senha"];

    // Verifica se o administrador existe
    $stmt = $conn->prepare("SELECT id, senha FROM administrador WHERE nome = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows == 1) {
        $admin = $resultado->fetch_assoc();
        // Verifica se a senha está correta
        if (password_verify($senha, $admin["senha"])) {
            $_SESSION["admin"] = ["id" => $admin["id"], "usuario" => $usuario];
            header("Location: painel_admin.php");
            exit();
        } else {
            $erro = "Usuário ou senha incorretos.";
        }
    } else {
        $erro = "Usuário ou senha incorretos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login do Administrador</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>Login do Administrador</h2>
    <?php if ($erro) echo "<p style='color:red;'>$erro</p>"; ?>

    <form method="POST">
        <label for="usuario">Usuário:</label>
        <input type="text" id="usuario" name="usuario" required>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>

        <button type="submit">Entrar</button>
    </form>

    <a href="../index.php">Voltar</a>
</body>
</html>
