<?php
session_start();
require 'conn.php';

if (!isset($_SESSION['professor_id'])) {
    header("Location: login.php");
    exit();
}

$professor_id = $_SESSION['professor_id'];

// Obter todas as séries disponíveis
$sql = "SELECT DISTINCT serie FROM alunos WHERE serie IS NOT NULL AND serie <> '' ORDER BY serie";
$classes = $conn->query($sql);

// Registrar empréstimo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['livro_id']) && isset($_POST['aluno_id'])) {
    $livro_id = $_POST['livro_id'];
    $aluno_id = $_POST['aluno_id'];
    $data_emprestimo = date("Y-m-d");
    $data_devolucao = date("Y-m-d", strtotime("+15 days"));

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
        $sql = "INSERT INTO emprestimos (livro_id, aluno_id, professor_id, data_emprestimo, data_devolucao) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiss", $livro_id, $aluno_id, $professor_id, $data_emprestimo, $data_devolucao);
        if ($stmt->execute()) {
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
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Registrar Empréstimo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
    <style>
        body {
    background: linear-gradient(135deg, #f0f4f8, #e0e7ff); /* Fundo suave */
    font-family: 'Arial', sans-serif;
    color: #212121;
}

.container {
    margin-top: 40px;
}

.form-control {
    border-radius: 15px; /* Aumentar o raio da borda */
    transition: box-shadow 0.3s ease-in-out, border-color 0.3s ease; /* Adicionar transição para a borda */
}

.form-control:focus {
    box-shadow: 0 0 10px rgba(0, 121, 107, 0.5); /* Cor da sombra ao focar */
    border-color: #00796b; /* Cor da borda ao focar */
}

.alert {
    margin-top: 15px;
    transition: opacity 0.5s ease-in-out;
    background-color: #c8e6c9; /* Cor de fundo da alerta */
    color: #388e3c; /* Cor do texto da alerta */
    border-radius: 8px; /* Raio da borda */
    padding: 10px; /* Adicionar padding */
}

.card {
    border-radius: 15px; /* Aumentar o raio da borda */
    box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.1); /* Sombra mais suave */
    background: white;
    padding: 20px;
}

.card-header {
    background-color: #00796b; /* Cor de fundo do cabeçalho */
    color: white;
    border-radius: 15px 15px 0 0; /* Aumentar o raio da borda */
    padding: 1rem; /* Adicionar padding */
    font-size: 1.2rem;
    font-weight: bold;
    text-align: center;
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
    transform: scale(1.05); /* Efeito de escala ao passar o mouse */
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
    transform: scale(1.05); /* Efeito de escala ao passar o mouse */
    box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.2); /* Sombra ao passar o mouse */
}

.d-flex {
    display: flex; /* Garantir que o display seja flex */
    justify-content: space-between;
    align-items: center;
}

.table {
    margin-top: 20px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
}

.table th {
    background-color: #00796b;
    color: white;
    text-align: center;
    padding: 12px;
}

.table td {
    text-align: center;
    padding: 10px;
    border-bottom: 1px solid #ddd;
}

    </style>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center">Registrar Empréstimo</h2>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Série</label>
                <select class="form-select" id="filtro_serie">
                    <option value="">Todas</option>
                    <?php while ($classe = $classes->fetch_assoc()) {
                        echo "<option value='" . $classe['serie'] . "'>" . $classe['serie'] . "</option>";
                    } ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Pesquisar Aluno</label>
                <input type="text" id="pesquisar_aluno" class="form-control" placeholder="Digite o nome do aluno">
            </div>
            <div class="mb-3">
                <label class="form-label">Aluno</label>
                <select class="form-select" name="aluno_id" id="lista_alunos" required>
                    <?php
                    $alunos = $conn->query("SELECT id, nome, serie FROM alunos");
                    while ($aluno = $alunos->fetch_assoc()) {
                        echo "<option value='" . $aluno['id'] . "' data-serie='" . $aluno['serie'] . "'>" . $aluno['nome'] . " - " . $aluno['serie'] . "</option>";
                    }
                    ?>
                </select>
            </div>
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
            <button type="submit" class="btn btn-primary w-100">Registrar Empréstimo</button>
        </form>
    </div>
    <script>
        document.getElementById("filtro_serie").addEventListener("change", function() {
            let serieSelecionada = this.value;
            let alunos = document.querySelectorAll("#lista_alunos option");
            alunos.forEach(op => {
                if (serieSelecionada === "" || op.getAttribute("data-serie") === serieSelecionada) {
                    op.style.display = "block";
                } else {
                    op.style.display = "none";
                }
            });
        });
        document.getElementById("pesquisar_aluno").addEventListener("input", function() {
            let termo = this.value.toLowerCase();
            let alunos = document.querySelectorAll("#lista_alunos option");
            alunos.forEach(op => {
                if (op.textContent.toLowerCase().includes(termo)) {
                    op.style.display = "block";
                } else {
                    op.style.display = "none";
                }
            });
        });
    </script>
</body>
</html>