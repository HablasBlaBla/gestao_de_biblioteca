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
if ($stmt === false) {
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result === false) {
    die('Execute failed: ' . htmlspecialchars($stmt->error));
}
$aluno = $result->fetch_assoc();
if (!$aluno) {
    die('No student found with the given ID.');
}

// Verificando se existe uma foto de perfil, se não, mostra uma imagem padrão
$foto_perfil = !empty($aluno['foto_perfil']) ? '/_uploads/fp_alunos/' . $aluno['foto_perfil'] : '/_uploads/imagens/imagem_cinza.png';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - Aluno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Perfil do Aluno</h2>

        <div class="mb-3">
            <label for="foto" class="form-label">Foto de Perfil</label><br>
            <!-- Verifique se o caminho está correto para a imagem -->
            <img src="<?php echo htmlspecialchars($foto_perfil); ?>" alt="Foto de Perfil" class="img-fluid profile-picture">
        </div>

        <form method="POST" action="atualizar_perfil.php" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="foto" class="form-label">Foto de Perfil</label>
                <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
            </div>
            <div class="mb-3">
                <label for="nome" class="form-label">Nome (até 12 caracteres)</label>
                <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($aluno['nome']); ?>" maxlength="12" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($aluno['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="serie" class="form-label">Série</label>
                <input type="text" class="form-control" id="serie" name="serie" value="<?php echo htmlspecialchars($aluno['serie']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="descricao" class="form-label">Descrição</label>
                <textarea class="form-control" id="descricao" name="descricao" rows="3"><?php echo htmlspecialchars($aluno['descricao']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Atualizar</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
