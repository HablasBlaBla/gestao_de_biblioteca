<?php
session_start();

if (!isset($_SESSION['professor_id'])) {
    header("Location: login.php");
    exit();
}

require 'conn.php';

$alunos_sql = "SELECT id, nome FROM alunos";
$livros_sql = "SELECT id, titulo FROM livros";
$alunos_result = $conn->query($alunos_sql);
$livros_result = $conn->query($livros_sql);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $aluno_id = $_POST['aluno_id'];
    $livro_id = $_POST['livro_id'];
    $data_emprestimo = $_POST['data_emprestimo'];
    $data_devolucao = $_POST['data_devolucao'];

    $livro_check_sql = "SELECT quantidade FROM livros WHERE id = ?";
    $stmt = $conn->prepare($livro_check_sql);
    $stmt->bind_param("i", $livro_id);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($quantidade);
    $stmt->fetch();

    if ($quantidade > 0) {
        $sql = "INSERT INTO emprestimos (aluno_id, livro_id, data_emprestimo, data_devolucao, devolvido) VALUES (?, ?, ?, ?, 0)";
        $stmt_emprestimo = $conn->prepare($sql);
        $stmt_emprestimo->bind_param("iiss", $aluno_id, $livro_id, $data_emprestimo, $data_devolucao);

        if ($stmt_emprestimo->execute()) {
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
    <style>
        :root {
            --primary-color: #00796b;
            --primary-dark: #004d40;
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
            margin-top: 40px;
        }

        .form-control {
            border-radius: 15px;
            transition: box-shadow 0.3s ease-in-out, border-color 0.3s ease;
        }

        .form-control:focus {
            box-shadow: 0 0 10px rgba(0, 121, 107, 0.5);
            border-color: var(--primary-color);
        }

        .alert {
            margin-top: 15px;
            transition: opacity 0.5s ease-in-out;
        }

        .card {
            border-radius: 15px;
            box-shadow: var(--card-shadow);
            background: white;
            padding: 20px;
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 1rem;
            font-size: 1.2rem;
            font-weight: bold;
            text-align: center;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 12px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: scale(1.05);
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2);
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
        <div class="card">
            <div class="card-header">
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
<?php $conn->close(); ?>
