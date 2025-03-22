<?php
session_start();
require 'conn.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['professor_id'])) {
    header("Location: login.php");
    exit();
}

$professor_id = $_SESSION['professor_id'];

// Obter todas as classes disponíveis para filtro
$sql = "SELECT DISTINCT serie FROM alunos WHERE serie IS NOT NULL AND serie <> '' ORDER BY serie";
$classes = $conn->query($sql);

// Registrar empréstimo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['livro_id']) && isset($_POST['aluno_id'])) {
    $livro_id = $_POST['livro_id'];
    $aluno_id = $_POST['aluno_id'];
    $data_emprestimo = date("Y-m-d");
    $data_devolucao = date("Y-m-d", strtotime("+15 days")); // Devolução em 15 dias

    // Verificar se o livro está disponível
    $sql = "SELECT quantidade FROM livros WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $livro_id);
    $stmt->execute();
    $stmt->bind_result($quantidade);
    if ($stmt->fetch() === null) {
        echo "<div class='alert alert-danger'>Erro ao verificar disponibilidade do livro.</div>";
        exit();
    }
    $stmt->close();

    if ($quantidade > 0) {
        // Registrar empréstimo
        $sql = "INSERT INTO emprestimos (livro_id, aluno_id, professor_id, data_emprestimo, data_devolucao) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiss", $livro_id, $aluno_id, $professor_id, $data_emprestimo, $data_devolucao);
        if ($stmt->execute()) {
            // Atualizar quantidade do livro
            $sql = "UPDATE livros SET quantidade = quantidade - 1 WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $livro_id);
            $stmt->execute();
            echo "<div class='alert alert-success'>Empréstimo registrado com sucesso!</div>";
        } else {
            echo "<div class='alert alert-danger'>Erro ao registrar empréstimo.</div>";
        }
        $stmt->close();
    } else {
        echo "<div class='alert alert-warning'>Livro indisponível para empréstimo.</div>";
    }
}

// Marcar como devolvido
if (isset($_GET['devolver_id'])) {
    $emprestimo_id = $_GET['devolver_id'];

    // Pegar o livro do empréstimo antes de deletar
    $sql = "SELECT livro_id FROM emprestimos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $emprestimo_id);
    $stmt->execute();
    $stmt->bind_result($livro_id);
    $stmt->fetch();
    $stmt->close();

    // Deletar o empréstimo e atualizar quantidade do livro
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
    <title>Gerenciar Empréstimos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .btn:hover {
            background-color: #0056b3 !important;
            color: white !important;
        }

        .card {
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #007bff;
            color: white;
        }

        .table th, .table td {
            vertical-align: middle;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2><i class="fas fa-book"></i> Gerenciar Empréstimos</h2>
        <hr>

        <!-- Formulário para registrar empréstimo -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h4><i class="fas fa-plus-circle"></i> Registrar Novo Empréstimo</h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Livro</label>
                        <select class="form-select" name="livro_id" required>
                            <?php
                            $livros = $conn->query("SELECT id, titulo FROM livros WHERE quantidade > 0");
                            while ($livro = $livros->fetch_assoc()) {
                                echo "<option value='" . $livro['id'] . "'>" . $livro['titulo'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Filtrar por Classe</label>
                        <select class="form-select" id="classeFilter" onchange="filtrarAlunos()">
                            <option value="">Todas</option>
                            <?php while ($classe = $classes->fetch_assoc()) { ?>
                                <option value="<?= $classe['serie']; ?>"><?= $classe['serie']; ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Aluno</label>
                        <input type="text" class="form-control mb-2" id="searchAluno" onkeyup="buscarAluno()" placeholder="Pesquisar aluno">
                        <select class="form-select" name="aluno_id" required id="alunoSelect">
                            <option value="">Selecione um aluno</option>
                            <?php
                            $alunos = $conn->query("SELECT id, nome, serie FROM alunos");
                            while ($aluno = $alunos->fetch_assoc()) {
                                echo "<option value='" . $aluno['id'] . "' data-classe='" . $aluno['serie'] . "'>" . $aluno['nome'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save"></i> Registrar Empréstimo
                    </button>
                </form>
            </div>
        </div>

        <!-- Lista de Empréstimos -->
        <div class="card">
            <div class="card-header bg-success text-white">
                <h4><i class="fas fa-list"></i> Empréstimos Registrados</h4>
            </div>
            <div class="card-body">
                <input type="text" class="form-control mb-3" id="searchAluno" placeholder="Pesquisar aluno">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Título</th>
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
                                <td><a href="?devolver_id=<?= $row['id']; ?>" class="btn btn-danger btn-sm"><i class="fas fa-undo"></i> Devolver</a></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <br>
                <a href="dashboard.php" class="btn btn-primary w-100" id="voltaDashboardId"><i class="fas fa-arrow-left"></i> Voltar para o Painel</a>
                <br>
            </div>
        </div>
    </div>
    <script>
        function filtrarAlunos() {
            var classe = document.getElementById("classeFilter").value;
            var alunos = document.querySelectorAll("#alunoSelect option");

            alunos.forEach(function(aluno) {
                if (classe === "" || aluno.getAttribute("data-classe") === classe) {
                    aluno.style.display = "block";
                } else {
                    aluno.style.display = "none";
                }
            });
        }

        function buscarAluno() {
            var searchTerm = document.getElementById("searchAluno").value.toLowerCase();
            var alunos = document.querySelectorAll("#alunoSelect option");

            alunos.forEach(function(aluno) {
                var nome = aluno.textContent.toLowerCase();
                if (nome.includes(searchTerm)) {
                    aluno.style.display = "block";
                } else {
                    aluno.style.display = "none";
                }
            });
        }
    </script>
</body>
</html>
