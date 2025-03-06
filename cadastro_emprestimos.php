<?php
session_start();

// Verifica se o professor está logado
if (!isset($_SESSION['professor_id'])) {
    header("Location: login.php");
    exit();
}

require 'conn.php'; // Arquivo de conexão com o banco

// Obter todos os alunos e livros para os selects
$alunos_sql = "SELECT id, nome FROM alunos";
$livros_sql = "SELECT id, titulo FROM livros";
$alunos_result = $conn->query($alunos_sql);
$livros_result = $conn->query($livros_sql);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $aluno_id = $_POST['aluno_id'];
    $livro_id = $_POST['livro_id'];
    $data_emprestimo = $_POST['data_emprestimo'];
    $data_devolucao = $_POST['data_devolucao'];

    // Verificando se o livro está disponível
    $livro_check_sql = "SELECT quantidade FROM livros WHERE id = ?";
    $stmt = $conn->prepare($livro_check_sql);
    $stmt->bind_param("i", $livro_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($quantidade);
    $stmt->fetch();

    if ($quantidade > 0) {
        // Registrar o empréstimo
        $sql = "INSERT INTO emprestimos (aluno_id, livro_id, data_emprestimo, data_devolucao, devolvido) VALUES (?, ?, ?, ?, 0)";
        $stmt_emprestimo = $conn->prepare($sql);
        $stmt_emprestimo->bind_param("iiss", $aluno_id, $livro_id, $data_emprestimo, $data_devolucao);

        if ($stmt_emprestimo->execute()) {
            // Atualizar quantidade do livro
            $sql_update = "UPDATE livros SET quantidade = quantidade - 1 WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("i", $livro_id);
            $stmt_update->execute();

            echo "<div class='alert alert-success'>Empréstimo registrado com sucesso!</div>";
        } else {
            echo "<div class='alert alert-danger'>Erro ao registrar empréstimo!</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Este livro não está disponível no momento!</div>";
    }

    $stmt->close();
    $stmt_emprestimo->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Empréstimo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white text-center">
                <h4>Cadastro de Empréstimo</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="aluno_id" class="form-label">Aluno</label>
                        <select class="form-select" name="aluno_id" required>
                            <option value="">Selecione o aluno</option>
                            <?php while ($aluno = $alunos_result->fetch_assoc()) { ?>
                                <option value="<?= $aluno['id'] ?>"><?= $aluno['nome'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="livro_id" class="form-label">Livro</label>
                        <select class="form-select" name="livro_id" required>
                            <option value="">Selecione o livro</option>
                            <?php while ($livro = $livros_result->fetch_assoc()) { ?>
                                <option value="<?= $livro['id'] ?>"><?= $livro['titulo'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="data_emprestimo" class="form-label">Data de Empréstimo</label>
                        <input type="date" class="form-control" name="data_emprestimo" required>
                    </div>
                    <div class="mb-3">
                        <label for="data_devolucao" class="form-label">Data de Devolução</label>
                        <input type="date" class="form-control" name="data_devolucao" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Registrar Empréstimo</button>
                </form>
            </div>
        </div>
    </div>
    <a href="dashboard.php">dashboard</a>
</body>
</html>
