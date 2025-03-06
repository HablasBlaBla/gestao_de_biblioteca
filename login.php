<?php
session_start();  // Inicia a sessão

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'conn.php'; // Arquivo de conexão com o banco

    $email = $_POST['email'];
    $senha = md5($_POST['senha']); // Criptografando a senha com MD5

    // Consultando o banco para verificar se o professor existe
    $sql = "SELECT * FROM professores WHERE email = ? AND senha = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $senha);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $professor = $resultado->fetch_assoc();
        $_SESSION['professor_id'] = $professor['id'];  // Armazenando a sessão do professor
        $_SESSION['nome'] = $professor['nome'];  // Armazenando o nome do professor
        header("Location: dashboard.php");  // Redireciona para a página principal
        exit();
    } else {
        $erro = "Email ou senha incorretos!";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white text-center">
                <h4>Login de Professor</h4>
            </div>
            <div class="card-body">
                <?php if (isset($erro)) { echo "<div class='alert alert-danger'>$erro</div>"; } ?>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" name="senha" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Entrar</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
