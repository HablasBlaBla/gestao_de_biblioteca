<?php
session_start();
if (!isset($_SESSION['aluno_id'])) {
    header("Location: login.php");
    exit();
}

require '../conn.php'; // Conexão com o banco de dados

// Carregar os dados do aluno
$aluno_id = $_SESSION['aluno_id'];
$sql = "SELECT * FROM alunos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$result = $stmt->get_result();
$aluno = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - Aluno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Perfil do Aluno</h2>
        <form method="POST" action="atualizar_perfil.php">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" value="<?php echo $aluno['nome']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $aluno['email']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="serie" class="form-label">Série</label>
                <input type="text" class="form-control" id="serie" name="serie" value="<?php echo $aluno['serie']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Atualizar</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
