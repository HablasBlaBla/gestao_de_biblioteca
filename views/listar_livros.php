<?php
session_start();
require_once "../database/db.php"; // Conexão com o banco de dados

// Verifica se o professor está logado
if (!isset($_SESSION['professor'])) {
    header("Location: login_professor.php");
    exit();
}

// Busca todos os livros cadastrados
$result = $conn->query("SELECT id, nomeLivro, nomeAutor FROM livro");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listar Livros</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <h2>Lista de Livros</h2>
    <a href="painel_professor.php">Voltar</a>

    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nome do Livro</th>
            <th>Autor</th>
            <th>Ações</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['nomeLivro']; ?></td>
                <td><?php echo $row['nomeAutor']; ?></td>
                <td>
                    <a href="editar_livro.php?id=<?php echo $row['id']; ?>">Editar</a>
                    <a href="excluir_livro.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir este livro?')">Excluir</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
