<?php
session_start();

// Verifica se o aluno está logado
if (!isset($_SESSION['aluno_id'])) {
    header("Location: login.php");
    exit();
}

require '../conn.php'; // Arquivo de conexão com o banco de dados

$aluno_id = $_SESSION['aluno_id'];

// Variáveis de erro e sucesso
$erro = $sucesso = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recebe os dados do formulário
    $senha_atual = md5($_POST['senha_atual']);
    $nova_senha = md5($_POST['nova_senha']);
    $confirmar_senha = md5($_POST['confirmar_senha']);

    // Verifica se a senha atual está correta
    $sql_check = "SELECT senha FROM alunos WHERE id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $aluno_id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $stmt_check->bind_result($senha_db);
        $stmt_check->fetch();

        if ($senha_atual !== $senha_db) {
            $erro = "A senha atual está incorreta!";
        } elseif ($nova_senha !== $confirmar_senha) {
            $erro = "As senhas não coincidem!";
        } else {
            // Atualiza a senha no banco de dados
            $sql_update = "UPDATE alunos SET senha = ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("si", $nova_senha, $aluno_id);

            if ($stmt_update->execute()) {
                $sucesso = "Senha atualizada com sucesso!";
            } else {
                $erro = "Erro ao atualizar a senha!";
            }

            $stmt_update->close();
        }
    } else {
        $erro = "Aluno não encontrado!";
    }

    $stmt_check->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações - Aluno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <div class="container mt-5">
        <h2>Configurações - Alterar Senha</h2>

        <?php if (!empty($erro)) { ?>
            <div class="alert alert-danger" role="alert"><?php echo $erro; ?></div>
        <?php } ?>

        <?php if (!empty($sucesso)) { ?>
            <div class="alert alert-success" role="alert"><?php echo $sucesso; ?></div>
        <?php } ?>

        <form action="configuracoes.php" method="POST">
            <div class="mb-3">
                <label for="senha_atual" class="form-label">Senha Atual</label>
                <input type="password" class="form-control" id="senha_atual" name="senha_atual" required>
            </div>
            <div class="mb-3">
                <label for="nova_senha" class="form-label">Nova Senha</label>
                <input type="password" class="form-control" id="nova_senha" name="nova_senha" required>
            </div>
            <div class="mb-3">
                <label for="confirmar_senha" class="form-label">Confirmar Nova Senha</label>
                <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" required>
            </div>
            <button type="submit" class="btn btn-primary">Alterar Senha</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
