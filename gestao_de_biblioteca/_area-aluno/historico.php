<?php
session_start();
if (!isset($_SESSION['aluno_id'])) {
    header("Location: login.php");
    exit();
}

require '../conn.php'; // Conexão com o banco de dados

$aluno_id = $_SESSION['aluno_id'];
$sql = "SELECT livros.titulo, emprestimos.data_emprestimo, emprestimos.data_devolucao 
        FROM emprestimos 
        JOIN livros ON emprestimos.livro_id = livros.id
        WHERE emprestimos.aluno_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico - Aluno</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Histórico de Empréstimos</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Título do Livro</th>
                    <th>Data de Empréstimo</th>
                    <th>Data de Devolução</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['titulo']; ?></td>
                        <td><?php echo $row['data_emprestimo']; ?></td>
                        <td><?php echo $row['data_devolucao'] ? $row['data_devolucao'] : 'Não devolvido'; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
