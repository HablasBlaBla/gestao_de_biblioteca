<?php
session_start();

// Verifica se o professor está logado
if (!isset($_SESSION['professor_id'])) {
    header("Location: login.php");
    exit();
}

require 'conn.php'; // Arquivo de conexão com o banco

// Consultando o número total de empréstimos por livro
$sql = "
    SELECT livros.titulo, COUNT(emprestimos.id) AS total_emprestimos
    FROM emprestimos
    JOIN livros ON emprestimos.livro_id = livros.id
    GROUP BY emprestimos.livro_id
    ORDER BY total_emprestimos DESC
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios de Empréstimos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white text-center">
                <h4>Relatório de Empréstimos</h4>
            </div>
            <div class="card-body">
                <?php if ($result->num_rows > 0): ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Título do Livro</th>
                                <th>Total de Empréstimos</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['titulo']; ?></td>
                                    <td><?php echo $row['total_emprestimos']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-center">Nenhum empréstimo registrado.</p>
                <?php endif; ?>
            </div>
        </div>
        <br>
            <a href="dashboard.php" class="btn btn-primary w-100" id="voltaDashboardId">Voltar para o Painel</a>
        <br>
    </div>
</body>
</html>

<?php
$conn->close();
?>
