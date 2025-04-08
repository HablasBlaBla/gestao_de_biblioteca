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
    background: linear-gradient(135deg, #f8f9fa, #e9ecef); /* Fundo suave */
    font-family: 'Arial', sans-serif;
    color: #212121; /* Cor do texto padrão */
}

.card {
    border-radius: 15px; /* Raio da borda */
    box-shadow: 0px 6px 30px rgba(0, 0, 0, 0.1); /* Sombra mais profunda */
    margin-bottom: 20px; /* Espaçamento entre os cartões */
}

.card-header {
    border-radius: 15px 15px 0 0; /* Raio da borda do cabeçalho */
    background-color: #00796b; /* Cor de fundo do cabeçalho */
    color: white; /* Cor do texto do cabeçalho */
    padding: 1rem; /* Padding para o cabeçalho */
}

.list-group-item {
    transition: all 0.3s ease; /* Transição suave */
    cursor: pointer;
    background-color: #ffffff; /* Cor de fundo padrão */
    border-radius: 8px; /* Raio da borda */
    padding: 12px; /* Padding para os itens da lista */
}

.list-group-item:hover {
    background-color: #e0e0e0; /* Cor de fundo ao passar o mouse */
    transform: scale(1.02); /* Efeito de escala */
    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1); /* Sombra ao passar o mouse */
}

.list-group-item a {
    text-decoration: none; /* Remover sublinhado */
    color: #333; /* Cor do texto */
    display: flex; /* Flexbox para alinhamento */
    align-items: center; /* Alinhamento vertical */
    font-size: 1.1rem; /* Tamanho da fonte */
}

.list-group-item a:hover {
    color: #007bff; /* Cor do link ao passar o mouse */
}

.icon {
    margin-right: 12px; /* Espaçamento à direita do ícone */
    color: #333; /* Cor do ícone */
    font-size: 1.5rem; /* Tamanho do ícone */
    transition: color 0.3s ease; /* Transição suave para a cor */
}

.list-group-item:hover .icon {
    color: #007bff; /* Cor do ícone ao passar o mouse */
}

.card-header h2 {
    font-size: 1.8rem; /* Tamanho da fonte do cabeçalho */
    font-weight: bold; /* Negrito */
    margin: 0; /* Remover margens */
}

.card-body {
    padding: 2rem; /* Padding para o corpo do cartão */
}

.btn-link {
    text-decoration: none; /* Remover sublinhado */
    color: #333; /* Cor do botão link */
    transition: color 0.3s ease; /* Transição suave */
}

.btn-link:hover {
    color: #007bff; /* Cor do link ao passar o mouse */
}

.btn-danger {
    background-color: #dc3545; /* Cor de fundo do botão de perigo */
    color: white; /* Cor do texto */
    padding: 12px 20px; /* Padding para o botão */
    border-radius: 8px; /* Raio da borda */
    border: none; /* Remover borda */
    transition: background-color 0.3s ease, transform 0.3s ease; /* Transições suaves */
}

.btn-danger:hover {
    background-color: #c82333; /* Cor ao passar o mouse */
    transform: scale(1.05); /* Efeito de escala ao passar o mouse */
}

.mb-4 {
    margin-bottom: 1.5rem !important; /* Margem inferior */
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
