<!-- cadastro.php -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Professor</title>
    <link rel="stylesheet" href="style.css"> <!-- Estilos opcionais -->
</head>
<body>
    <h2>Cadastro de Professor</h2>
    <form method="POST" action="cadastro.php">
        <label for="cpf">CPF:</label>
        <input type="text" name="cpf" id="cpf" required maxlength="11" placeholder="Digite o CPF" /><br>

        <label for="senha">Senha:</label>
        <input type="password" name="senha" id="senha" required placeholder="Digite sua senha" /><br>

        <label for="senha_confirm">Confirmar Senha:</label>
        <input type="password" name="senha_confirm" id="senha_confirm" required placeholder="Confirme sua senha" /><br>

        <button type="submit">Cadastrar</button>
    </form>

    <?php
    // Exibir mensagens de erro ou sucesso
    if (isset($_GET['erro'])) {
        echo "<p style='color: red;'>Erro: CPF já cadastrado ou as senhas não coincidem.</p>";
    }
    ?>
</body>
</html>
<?php
// cadastro.php

// Iniciar a sessão
session_start();

// Incluir o arquivo de conexão
include '../tcc/frontend/index.php';

// Verificar se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Receber os dados do formulário
    $cpf = $_POST['cpf'];
    $senha = $_POST['senha'];
    $senha_confirm = $_POST['senha_confirm'];

    // Verificar se as senhas coincidem
    if ($senha !== $senha_confirm) {
        // Se as senhas não coincidirem, redirecionar com erro
        header("Location: cadastro.php?erro=1");
        exit();
    }

    // Verificar se o CPF já existe no banco de dados
    $sql = "SELECT * FROM professores WHERE cpf = :cpf";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':cpf', $cpf);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Se o CPF já estiver cadastrado, redirecionar com erro
        header("Location: cadastro.php?erro=1");
        exit();
    }

    // Hash da senha
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    // Inserir os dados no banco de dados
    $sql = "INSERT INTO professores (cpf, senha) VALUES (:cpf, :senha)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':cpf', $cpf);
    $stmt->bindParam(':senha', $senhaHash);

    if ($stmt->execute()) {
        // Cadastro realizado com sucesso, redirecionar para a página de login
        header("Location: login.php");
        exit();
    } else {
        // Se o cadastro falhar, redirecionar com erro
        header("Location: cadastro.php?erro=1");
        exit();
    }
}
?>
