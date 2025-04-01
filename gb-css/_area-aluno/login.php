<?php
session_start();
include('../conn.php');  // Arquivo de configuração do banco de dados

// Verifica se o formulário foi enviado
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $senha = md5($_POST['senha']); // Criptografa a senha com MD5

    // Consulta no banco de dados
    $sql = "SELECT * FROM alunos WHERE email = ? AND senha = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $senha);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $aluno = $result->fetch_assoc();
        $_SESSION['aluno_id'] = $aluno['id'];
        $_SESSION['aluno_nome'] = $aluno['nome'];
        $_SESSION['aluno_email'] = $aluno['email'];
        header("Location: dashboard.php"); // Redireciona para o painel
    } else {
        $erro = "Credenciais inválidas!";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Aluno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Login - Aluno</h2>
        <?php if (isset($erro)) { echo "<div class='alert alert-danger'>$erro</div>"; } ?>
        <div class="row justify-content-center">
            <div class="col-md-4">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary w-100">Entrar</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
