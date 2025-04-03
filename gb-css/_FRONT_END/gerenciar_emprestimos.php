<?php
include('../_BACK-END/gerenciar_emprestimos.php')
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Registrar Empréstimo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="_css/gerenciar_emprestimos.css">
</head>
<body>
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
<?php $conn->close(); ?>
