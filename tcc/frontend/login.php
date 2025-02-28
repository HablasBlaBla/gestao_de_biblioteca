<!-- login.php -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Biblioteca</title>
    <link rel="stylesheet" href="style.css"> <!-- Estilos para melhorar a aparência (opcional) -->
</head>
<body>
    <h2>Login do Professor</h2>
    <form method="POST" action="login.php">
        <label for="cpf">CPF:</label>
        <input type="text" name="cpf" id="cpf" required maxlength="11" placeholder="Digite seu CPF" /><br>

        <label for="senha">Senha:</label>
        <input type="password" name="senha" id="senha" required placeholder="Digite sua senha" /><br>

        <button type="submit">Entrar</button>
    </form>

    <?php
    // Exibir mensagem de erro se houver
    if (isset($_GET['erro'])) {
        echo "<p style='color: red;'>CPF ou Senha incorretos!</p>";
    }
    ?>
</body>
</html>
