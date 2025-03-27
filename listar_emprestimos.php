<?php
session_start();
require 'conn.php';

if (!isset($_SESSION['professor_id'])) {
    header("Location: login.php");
    exit();
}

$professor_id = $_SESSION['professor_id'];

// Marcar como devolvido
if (isset($_GET['devolver_id'])) {
    $emprestimo_id = $_GET['devolver_id'];

    $sql = "SELECT livro_id FROM emprestimos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $emprestimo_id);
    $stmt->execute();
    $stmt->bind_result($livro_id);
    $stmt->fetch();
    $stmt->close();

    $sql = "DELETE FROM emprestimos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $emprestimo_id);
    if ($stmt->execute()) {
        $sql = "UPDATE livros SET quantidade = quantidade + 1 WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $livro_id);
        $stmt->execute();
        echo "<div class='alert alert-success'>Livro devolvido com sucesso!</div>";
    } else {
        echo "<div class='alert alert-danger'>Erro ao devolver livro.</div>";
    }
    $stmt->close();
}

// Buscar empréstimos do professor
$sql = "SELECT e.id, l.titulo, a.nome, e.data_emprestimo, e.data_devolucao 
        FROM emprestimos e 
        JOIN livros l ON e.livro_id = l.id
        JOIN alunos a ON e.aluno_id = a.id
        WHERE e.professor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $professor_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Empréstimos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2>Gerenciar Empréstimos</h2>
        <hr>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Livro</th>
                    <th>Aluno</th>
                    <th>Data Empréstimo</th>
                    <th>Data Devolução</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= $row['titulo']; ?></td>
                        <td><?= $row['nome']; ?></td>
                        <td><?= $row['data_emprestimo']; ?></td>
                        <td><?= $row['data_devolucao']; ?></td>
                        <td>
                            <a href="?devolver_id=<?= $row['id']; ?>" class="btn btn-danger">Devolver</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <hr>
        <a href="gerenciar_emprestimos.php" class="btn btn-primary w-100">Registrar Novo Empréstimo</a>
    </div>
</body>
</html>
