<?php
session_start();

// Verifica se o aluno está logado
if (!isset($_SESSION['aluno_id'])) {
    header("Location: login.php");
    exit();
}

require '../conn.php'; // Arquivo de conexão com o banco de dados

$aluno_id = $_SESSION['aluno_id'];

// Consulta para pegar as mensagens enviadas ao aluno
$sql = "SELECT mensagens.id, mensagens.mensagem, mensagens.imagem_url, mensagens.data_envio, professores.nome AS professor_nome 
        FROM mensagens 
        JOIN professores ON mensagens.professor_id = professores.id
        WHERE mensagens.aluno_id = ?
        ORDER BY mensagens.data_envio DESC"; // Ordena por data de envio (mais recentes primeiro)

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$result = $stmt->get_result();

// Deletar mensagem
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql_delete = "DELETE FROM mensagens WHERE id = ? AND aluno_id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("ii", $delete_id, $aluno_id);
    if ($stmt_delete->execute()) {
        echo "<div class='alert alert-success' role='alert'>Mensagem excluída com sucesso!</div>";
    } else {
        echo "<div class='alert alert-danger' role='alert'>Erro ao excluir a mensagem.</div>";
    }
    $stmt_delete->close();
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensagens - Aluno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <div class="container mt-5">
        <h2>Mensagens do Professor</h2>

        <?php
        // Verifica se há mensagens
        if ($result->num_rows > 0) {
            // Exibe as mensagens
            while ($row = $result->fetch_assoc()) {
        ?>
                <div class="card mb-3">
                    <div class="card-header">
                        Enviado por: <?php echo $row['professor_nome']; ?> | 
                        Data: <?php echo date('d/m/Y H:i', strtotime($row['data_envio'])); ?>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo nl2br($row['mensagem']); ?></p>
                        
                        <?php
                        // Exibe a imagem, se houver
                        if (!empty($row['imagem_url'])) {
                            echo "<img src='../_uploads/" . basename($row['imagem_url']) . "' alt='Imagem enviada' class='img-fluid'>";
                        }
                        ?>

                        <!-- Botão para excluir mensagem -->
                        <a href="?delete_id=<?php echo $row['id']; ?>" class="btn btn-danger mt-3" onclick="return confirm('Você tem certeza de que deseja excluir esta mensagem?')">Excluir</a>
                    </div>
                </div>
        <?php
            }
        } else {
            // Se não houver mensagens
            echo "<div class='alert alert-info' role='alert'>Você ainda não tem mensagens.</div>";
        }
        ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
