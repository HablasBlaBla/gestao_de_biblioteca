<?php
session_start();
require_once "../database/db.php"; // Conexão com o banco de dados

// Verifica se o aluno está logado
if (!isset($_SESSION['aluno'])) {
    header("Location: login_aluno.php");
    exit();
}

$alunoId = $_SESSION['aluno']['id'];

// Busca os empréstimos do aluno
$stmt = $conn->prepare("
    SELECT emprestimo.id, livro.nomeLivro, livro.nomeAutor, emprestimo.dataRetirada, emprestimo.dataDevolucao 
    FROM emprestimo 
    JOIN livro ON emprestimo.livroId = livro.id 
    WHERE emprestimo.alunoId = ?
");
$stmt->bind_param("i", $alunoId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Empréstimos</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>Meus Empréstimos</h2>
    <a href="painel_aluno.php">Voltar</a>

    <table border="1">
        <tr>
            <th>Nome do Livro</th>
            <th>Autor</th>
            <th>Data de Retirada</th>
            <th>Data de Devolução</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['nomeLivro']; ?></td>
                <td><?php echo $row['nomeAutor']; ?></td>
                <td><?php echo $row['dataRetirada']; ?></td>
                <td><?php echo $row['dataDevolucao'] ? $row['dataDevolucao'] : "Ainda não devolvido"; ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
