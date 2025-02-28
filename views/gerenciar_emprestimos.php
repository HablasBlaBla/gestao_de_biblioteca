<?php
session_start();
require_once "../database/db.php"; // Conexão com o banco de dados

// Verifica se o admin está logado
if (!isset($_SESSION['admin'])) {
    header("Location: login_admin.php");
    exit();
}

$erro = "";
$sucesso = "";

// Processa a devolução do livro
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['devolver'])) {
    $emprestimoId = $_POST['emprestimoId'];

    $stmt = $conn->prepare("UPDATE emprestimo SET status = 'Devolvido' WHERE id = ?");
    $stmt->bind_param("i", $emprestimoId);

    if ($stmt->execute()) {
        $sucesso = "Empréstimo marcado como devolvido!";
    } else {
        $erro = "Erro ao atualizar o status do empréstimo.";
    }
}

// Processa a exclusão de um empréstimo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['excluir'])) {
    $emprestimoId = $_POST['emprestimoId'];

    $stmt = $conn->prepare("DELETE FROM emprestimo WHERE id = ?");
    $stmt->bind_param("i", $emprestimoId);

    if ($stmt->execute()) {
        $sucesso = "Empréstimo excluído com sucesso!";
    } else {
        $erro = "Erro ao excluir o empréstimo.";
    }
}

// Busca todos os empréstimos
$result = $conn->query("
    SELECT emprestimo.id, aluno.nome AS aluno, livro.titulo AS livro, emprestimo.data_emprestimo, emprestimo.status 
    FROM emprestimo 
    INNER JOIN aluno ON emprestimo.aluno_id = aluno.id
    INNER JOIN livro ON emprestimo.livro_id = livro.id
");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Empréstimos</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>Gerenciar Empréstimos</h2>
    <a href="painel_admin.php">Voltar</a>

    <?php 
    if ($erro) echo "<p style='color:red;'>$erro</p>"; 
    if ($sucesso) echo "<p style='color:green;'>$sucesso</p>"; 
    ?>

    <h3>Lista de Empréstimos</h3>
    <table border="1">
        <tr>
            <th>Aluno</th>
            <th>Livro</th>
            <th>Data do Empréstimo</th>
            <th>Status</th>
            <th>Ação</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['aluno']; ?></td>
                <td><?php echo $row['livro']; ?></td>
                <td><?php echo $row['data_emprestimo']; ?></td>
                <td><?php echo $row['status']; ?></td>
                <td>
                    <?php if ($row['status'] != 'Devolvido'): ?>
                        <form method="POST">
                            <input type="hidden" name="emprestimoId" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="devolver">Marcar como Devolvido</button>
                        </form>
                    <?php endif; ?>
                    <form method="POST">
                        <input type="hidden" name="emprestimoId" value="<?php echo $row['id']; ?>">
                        <button type="submit" name="excluir" onclick="return confirm('Tem certeza que deseja excluir este empréstimo?')">Excluir</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
