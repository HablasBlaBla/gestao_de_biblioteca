<?php
session_start();

if (!isset($_SESSION['professor_id'])) {
    header("Location: login.php");
    exit();
}

require 'conn.php';

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
        :root {
            --primary-color: #00796b;
            --primary-dark: #004d40;
            --background-gradient: linear-gradient(135deg, #f8f9fa, #e9ecef);
            --card-shadow: 0 6px 30px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        body {
            background: var(--background-gradient);
            font-family: 'Arial', sans-serif;
            color: #212121;
            min-height: 100vh;
            padding-bottom: 2rem;
        }

        .page-header {
            background: var(--primary-color);
            color: white;
            padding: 1.5rem 0;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-bottom: none;
            padding: 1.5rem;
            text-align: center;
        }

        .alert {
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .table {
            margin-top: 20px;
        }

        .pagination {
            justify-content: center;
            margin-top: 30px;
        }

        @media (max-width: 576px) {
            .container {
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="page-header">
            <h1 class="text-center mb-0">
                <i class="fas fa-file-alt"></i> Relatórios de Empréstimos
            </h1>
        </div>

        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-users"></i> Alunos que Mais Pegaram Livros</h2>
            </div>
            <div class="card-body">
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
        </div>

        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-school"></i> Sala que Mais Pegou Livros</h2>
            </div>
            <div class="card-body">
                <?php if ($result_salas->num_rows > 0): ?>
                    <?php $row = $result_salas->fetch_assoc(); ?>
                    <p><strong><?php echo $row['serie']; ?></strong> com <?php echo $row['total_emprestimos']; ?> empréstimos.</p>
                <?php else: ?>
                    <p class="text-center">Nenhuma sala registrada.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-book"></i> Livros Mais Emprestados</h2>
            </div>
            <div class="card-body">
                <canvas id="livrosChart"></canvas>
            </div>
        </div>

        <a href="dashboard.php" class="btn btn-primary w-100 mb-4">
            <i class="fas fa-arrow-left"></i> Voltar para o Painel
        </a>
    </div>

    <script>
        var ctx = document.getElementById('livrosChart').getContext('2d');
        var livrosChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [
                    <?php
                    $result_livros->data_seek(0);
                    while($row = $result_livros->fetch_assoc()) {
                        echo '"' . $row['titulo'] . '",';
                    }
                    ?>
                ],
                datasets: [{
                    label: 'Total de Empréstimos',
                    data: [
                        <?php
                        $result_livros->data_seek(0);
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
