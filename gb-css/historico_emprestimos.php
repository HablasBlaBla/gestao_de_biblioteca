<?php
session_start();
require 'conn.php'; // Arquivo de conexão com o banco

// Verifica se o professor está logado
if (!isset($_SESSION['professor_id'])) {
    header("Location: login.php");
    exit();
}

// Iniciando o array de tipos dos parâmetros para bind_param
$param_types = '';

// Iniciando o array de valores para bind_param
$param_values = [];

// Construir a consulta SQL dinamicamente
$sql = "
    SELECT e.id, l.titulo AS livro, a.nome AS aluno, e.data_emprestimo, e.data_devolucao, e.devolvido
    FROM emprestimos e
    JOIN livros l ON e.livro_id = l.id
    JOIN alunos a ON e.aluno_id = a.id
    WHERE 1
";

// Adicionar filtros à consulta dinamicamente
if (!empty($_GET['aluno'])) {
    $search_aluno = $_GET['aluno'];
    $sql .= " AND a.nome LIKE ?";
    $param_types .= 's'; // Tipo do parâmetro: 's' (string)
    $param_values[] = "%" . $search_aluno . "%"; // Valor do parâmetro
}
if (!empty($_GET['livro'])) {
    $search_livro = $_GET['livro'];
    $sql .= " AND l.titulo LIKE ?";
    $param_types .= 's'; // Tipo do parâmetro: 's' (string)
    $param_values[] = "%" . $search_livro . "%"; // Valor do parâmetro
}
if (!empty($_GET['estado'])) {
    $search_estado = $_GET['estado'];
    $sql .= " AND e.devolvido = ?";
    $param_types .= 's'; // Tipo do parâmetro: 's' (string)
    $param_values[] = $search_estado; // Valor do parâmetro
}
if (!empty($_GET['data_inicio'])) {
    $search_data_inicio = $_GET['data_inicio'];
    $sql .= " AND e.data_emprestimo >= ?";
    $param_types .= 's'; // Tipo do parâmetro: 's' (string para datas)
    $param_values[] = $search_data_inicio; // Valor do parâmetro
}
if (!empty($_GET['data_fim'])) {
    $search_data_fim = $_GET['data_fim'];
    $sql .= " AND e.data_emprestimo <= ?";
    $param_types .= 's'; // Tipo do parâmetro: 's' (string para datas)
    $param_values[] = $search_data_fim; // Valor do parâmetro
}

// Preparar a consulta SQL
$stmt = $conn->prepare($sql);

// Verificar se há filtros aplicados e vincular os parâmetros corretamente
if (count($param_values) > 0) {
    // Bind dos parâmetros dinamicamente
    $stmt->bind_param($param_types, ...$param_values);
}

// Executar a consulta
$stmt->execute();

// Obter os resultados
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Empréstimos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        :root {
            --primary-color: #00796b;
            --hover-color: #004d40;
            --background-gradient: linear-gradient(135deg, #f0f4f8, #e0e7ff);
            --card-shadow: 0px 6px 20px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        body {
            background: var(--background-gradient);
            font-family: 'Arial', sans-serif;
            color: #212121;
        }

        .container {
            margin-top: 50px;
        }

        .card {
            border-radius: 15px;
            box-shadow: var(--card-shadow);
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 15px 15px 0 0;
        }

        .table {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header text-center">
                <h4><i class="fas fa-history"></i> Histórico de Empréstimos</h4>
            </div>
            <div class="card-body">
                <!-- Formulário de Pesquisa -->
                <form method="GET" action="historico_emprestimos.php" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="aluno" placeholder="Aluno" value="<?php echo htmlspecialchars($_GET['aluno'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="livro" placeholder="Livro" value="<?php echo htmlspecialchars($_GET['livro'] ?? ''); ?>">
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" name="estado">
                                <option value="">Estado do Livro</option>
                                <option value="0" <?php if (isset($_GET['estado']) && $_GET['estado'] == '0') echo 'selected'; ?>>Não Devolvido</option>
                                <option value="1" <?php if (isset($_GET['estado']) && $_GET['estado'] == '1') echo 'selected'; ?>>Devolvido</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" name="data_inicio" value="<?php echo htmlspecialchars($_GET['data_inicio'] ?? ''); ?>">
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" name="data_fim" value="<?php echo htmlspecialchars($_GET['data_fim'] ?? ''); ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Buscar</button>
                        </div>
                    </div>
                </form>

                <!-- Tabela de Histórico -->
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Livro</th>
                            <th>Aluno</th>
                            <th>Data de Empréstimo</th>
                            <th>Data de Devolução</th>
                            <th>Estado do Livro</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['livro']; ?></td>
                                    <td><?php echo $row['aluno']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($row['data_emprestimo'])); ?></td>
                                    <td><?php echo $row['data_devolucao'] ? date('d/m/Y', strtotime($row['data_devolucao'])) : 'Não devolvido'; ?></td>
                                    <td><?php echo $row['devolvido'] == '0' ? 'Não Devolvido' : 'Devolvido'; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Nenhum registro encontrado.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <!-- Botões de Exportação -->
                <div class="text-center">
                    <a href="exportar_csv.php" class="btn btn-success">Exportar para CSV</a>
                    <a href="exportar_pdf.php" class="btn btn-danger">Exportar para PDF</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

<?php
$conn->close();
?>
