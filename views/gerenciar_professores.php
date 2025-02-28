<?php
session_start();
require_once "../database/db.php"; // Conexão com o banco de dados

// Verifica se o admin está logado
if (!isset($_SESSION['admin'])) {
    header("Location: login_admin.php");
    exit();
}

// Excluir professor
if (isset($_GET['excluir'])) {
    $id = $_GET['excluir'];

    // Exclui o professor pelo ID
    $stmt = $conn->prepare("DELETE FROM professor WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: gerenciar_professores.php?sucesso=Professor excluído com sucesso!");
        exit();
    } else {
        header("Location: gerenciar_professores.php?erro=Erro ao excluir o professor.");
        exit();
    }
}

// Buscar todos os professores cadastrados
$stmt = $conn->prepare("SELECT id, nome, rg FROM professor");
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Professores</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>Gerenciar Professores</h2>
    <a href="painel_admin.php">Voltar</a>

    <?php
    if (isset($_GET['sucesso'])) echo "<p style='color:green;'>{$_GET['sucesso']}</p>";
    if (isset($_GET['erro'])) echo "<p style='color:red;'>{$_GET['erro']}</p>";
    ?>

    <table border="1">
        <tr>
            <th>Nome</th>
            <th>RG</th>
            <th>Ações</th>
        </tr>
        <?php while ($professor = $resultado->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($professor['nome']); ?></td>
                <td><?php echo htmlspecialchars($professor['rg']); ?></td>
                <td>
                    <a href="gerenciar_professores.php?excluir=<?php echo $professor['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir este professor?');">Excluir</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
