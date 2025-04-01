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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Empréstimos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f0f4f8, #e0e7ff); /* Fundo suave */
            font-family: 'Arial', sans-serif;
            color: #212121;
        }

        .container {
            margin-top: 40px;
        }

        .card {
            border-radius: 15px; /* Aumentar o raio da borda */
            box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.1); /* Sombra mais suave */
        }

        .card-header {
            background-color: #00796b; /* Cor de fundo do cabeçalho */
            color: white;
            border-radius: 15px 15px 0 0; /* Aumentar o raio da borda */
            padding: 1rem; /* Adicionar padding */
        }

        .table-hover tbody tr:hover {
            background-color: #e0f2f1; /* Cor de fundo ao passar o mouse */
            transition: background-color 0.3s ease; /* Transição suave */
        }

        .btn-primary {
            background-color: #00796b; /* Cor do botão primário */
            border-color: #00796b;
            padding: 12px 20px; /* Adicionar padding */
            border-radius: 8px; /* Raio da borda */
            transition: all 0.3s ease; /* Transição suave */
        }

        .btn-primary:hover {
            background-color: #004d40; /* Cor ao passar o mouse */
            border-color: #004d40; /* Cor da borda ao passar o mouse */
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2); /* Sombra ao passar o mouse */
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
            padding: 12px 20px; /* Adicionar padding */
            border-radius: 8px; /* Raio da borda */
            transition: all 0.3s ease; /* Transição suave */
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #c82333;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2); /* Sombra ao passar o mouse */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4><i class="fas fa-book"></i> Lista de Empréstimos</h4>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
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
                                    <a href="?devolver_id=<?= $row['id']; ?>" class="btn btn-danger btn-sm">
                                        <i class="fas fa-undo-alt"></i> Devolver
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <a href="dashboard.php" class="btn btn-primary w-100">Voltar para o Painel</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
