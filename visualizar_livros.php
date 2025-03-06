<?php
session_start();

// Verifica se o professor está logado
if (!isset($_SESSION['professor_id'])) {
    header("Location: login.php");
    exit();
}

require 'conn.php'; // Arquivo de conexão com o banco

// Consultando todos os livros
$sql = "SELECT id, titulo, autor, ano_publicacao, genero, isbn, quantidade FROM livros";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Livros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white text-center">
                <h4>Visualização de Livros</h4>
            </div>
            <div class="card-body">
                <?php if ($result->num_rows > 0): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Título</th>
                                <th>Autor</th>
                                <th>Ano de Publicação</th>
                                <th>Gênero</th>
                                <th>ISBN</th>
                                <th>Quantidade</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['titulo']; ?></td>
                                    <td><?php echo $row['autor']; ?></td>
                                    <td><?php echo $row['ano_publicacao']; ?></td>
                                    <td><?php echo $row['genero']; ?></td>
                                    <td><?php echo $row['isbn']; ?></td>
                                    <td><?php echo $row['quantidade']; ?></td>
                                    <td>
                                        <a href="editar_livro.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                                        <a href="excluir_livro.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm">Excluir</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-center">Nenhum livro encontrado.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <a href="dashboard.php" class="btn btn-secondary mt-3">Voltar ao Dashboard</a>
</body>
</html>

<?php
$conn->close();
?>
