<?php
session_start();

// Verifica se o professor está logado
if (!isset($_SESSION['professor_id'])) {
    header("Location: login.php");
    exit();
}

require 'conn.php'; // Arquivo de conexão com o banco

// Consultando as informações para o gráfico de empréstimos
$sql = "
    SELECT 
        livros.titulo, 
        COUNT(emprestimos.id) AS total_emprestimos
    FROM emprestimos
    JOIN livros ON emprestimos.livro_id = livros.id
    GROUP BY emprestimos.livro_id
    ORDER BY total_emprestimos DESC
";

$sql_alunos = "
    SELECT alunos.nome, COUNT(emprestimos.id) AS total_emprestimos
    FROM emprestimos
    JOIN alunos ON emprestimos.aluno_id = alunos.id
    GROUP BY emprestimos.aluno_id
    ORDER BY total_emprestimos DESC
    LIMIT 5
";

$sql_salas = "
    SELECT alunos.serie, COUNT(emprestimos.id) AS total_emprestimos
    FROM emprestimos
    JOIN alunos ON emprestimos.aluno_id = alunos.id
    GROUP BY alunos.serie
    ORDER BY total_emprestimos DESC
    LIMIT 1
";

// Consultas de dados
$result_livros = $conn->query($sql);
$result_alunos = $conn->query($sql_alunos);
$result_salas = $conn->query($sql_salas);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios de Empréstimos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            font-family: 'Arial', sans-serif;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            border-radius: 15px 15px 0 0;
        }
        .list-group-item {
            transition: all 0.3s ease;
            cursor: pointer;
            background-color: #ffffff;
        }
        .list-group-item:hover {
            background-color: #e0e0e0;
            transform: scale(1.02);
        }
        .list-group-item a {
            text-decoration: none;
            color: #333;
            display: flex;
            align-items: center;
            font-size: 1.1rem;
        }
        .list-group-item a:hover {
            color: #007bff;
        }
        .icon {
            margin-right: 12px;
            color: black;
            font-size: 1.5rem;
        }
        .card-header h2 {
            font-size: 1.8rem;
            font-weight: bold;
        }
        .card-body {
            padding: 2rem;
        }
        .btn-link {
            text-decoration: none;
            color: #333;
        }
        .btn-link:hover {
            color: #007bff;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background-color: #c82333;
            color: white;
        }
        .mb-4 {
            margin-bottom: 1.5rem !important;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white text-center">
                <h2><i class="fas fa-file-alt"></i> Relatórios de Empréstimos</h2>
            </div>
            <div class="card-body">
                <!-- Exibindo as informações antes do gráfico -->
                <div class="mb-4">
                    <h5><i class="fas fa-users"></i> Alunos que Mais Pegaram Livros</h5>
                    <?php if ($result_alunos->num_rows > 0): ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Aluno</th>
                                    <th>Total de Empréstimos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $result_alunos->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['nome']; ?></td>
                                        <td><?php echo $row['total_emprestimos']; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p class="text-center">Nenhum empréstimo registrado.</p>
                    <?php endif; ?>
                </div>

                <div class="mb-4">
                    <h5><i class="fas fa-school"></i> Sala que Mais Pegou Livros</h5>
                    <?php if ($result_salas->num_rows > 0): ?>
                        <?php $row = $result_salas->fetch_assoc(); ?>
                        <p><strong><?php echo $row['serie']; ?></strong> com <?php echo $row['total_emprestimos']; ?> empréstimos.</p>
                    <?php else: ?>
                        <p class="text-center">Nenhuma sala registrada.</p>
                    <?php endif; ?>
                </div>

                <!-- Gráfico de livros mais emprestados -->
                <div class="mb-4">
                    <h5><i class="fas fa-book"></i> Livros Mais Emprestados</h5>
                    <canvas id="livrosChart"></canvas>
                </div>

                <br>
                <a href="dashboard.php" class="btn btn-primary w-100" id="voltaDashboardId">
                    <i class="fas fa-arrow-left"></i> Voltar para o Painel
                </a>
            </div>
        </div>
    </div>

    <!-- Script para o gráfico -->
    <script>
        var ctx = document.getElementById('livrosChart').getContext('2d');
        var livrosChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [
                    <?php
                    // Pega os títulos dos livros
                    $result_livros->data_seek(0); // Recomeça a consulta
                    while($row = $result_livros->fetch_assoc()) {
                        echo '"' . $row['titulo'] . '",';
                    }
                    ?>
                ],
                datasets: [{
                    label: 'Total de Empréstimos',
                    data: [
                        <?php
                        // Pega o total de empréstimos
                        $result_livros->data_seek(0); // Recomeça a consulta
                        while($row = $result_livros->fetch_assoc()) {
                            echo $row['total_emprestimos'] . ',';
                        }
                        ?>
                    ],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>
