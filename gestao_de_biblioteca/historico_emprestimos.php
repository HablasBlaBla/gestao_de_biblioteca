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
    SELECT e.id, l.titulo AS livro, a.nome AS aluno, e.data_emprestimo, e.data_devolucao, e.estado_livro
    FROM emprestimos e
    JOIN livros l ON e.livro_id = l.id
    JOIN alunos a ON e.aluno_id = a.id
    WHERE 1
";

// Adicionar filtros à consulta dinamicamente
if ($search_aluno) {
    $sql .= " AND a.nome LIKE ?";
    $param_types .= 's'; // Tipo do parâmetro: 's' (string)
    $param_values[] = "%" . $search_aluno . "%"; // Valor do parâmetro
}
if ($search_livro) {
    $sql .= " AND l.titulo LIKE ?";
    $param_types .= 's'; // Tipo do parâmetro: 's' (string)
    $param_values[] = "%" . $search_livro . "%"; // Valor do parâmetro
}
if ($search_estado) {
    $sql .= " AND e.estado_livro = ?";
    $param_types .= 's'; // Tipo do parâmetro: 's' (string)
    $param_values[] = $search_estado; // Valor do parâmetro
}
if ($search_data_inicio) {
    $sql .= " AND e.data_emprestimo >= ?";
    $param_types .= 's'; // Tipo do parâmetro: 's' (string para datas)
    $param_values[] = $search_data_emprestimo; // Valor do parâmetro
}
if ($search_data_fim) {
    $sql .= " AND e.data_emprestimo <= ?";
    $param_types .= 's'; // Tipo do parâmetro: 's' (string para datas)
    $param_values[] = $search_data_devolucao; // Valor do parâmetro
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
            background-color: #007bff;
            color: white;
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
                            <input type="text" class="form-control" name="aluno" placeholder="Aluno" value="<?php echo htmlspecialchars($search_aluno); ?>">
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="livro" placeholder="Livro" value="<?php echo htmlspecialchars($search_livro); ?>">
                        </div>
                        <div class="col-md-2">
                            <select class="form-control" name="estado">
                                <option value="">Estado do Livro</option>
                                <option value="Novo" <?php if ($search_estado == 'Novo') echo 'selected'; ?>>Novo</option>
                                <option value="Bom" <?php if ($search_estado == 'Bom') echo 'selected'; ?>>Bom</option>
                                <option value="Regular" <?php if ($search_estado == 'Regular') echo 'selected'; ?>>Regular</option>
                                <option value="Ruim" <?php if ($search_estado == 'Ruim') echo 'selected'; ?>>Ruim</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" name="data_inicio" value="<?php echo htmlspecialchars($search_data_inicio); ?>">
                        </div>
                        <div class="col-md-2">
                            <input type="date" class="form-control" name="data_fim" value="<?php echo htmlspecialchars($search_data_fim); ?>">
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
                                    <td><?php echo $row['estado_livro']; ?></td>
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
