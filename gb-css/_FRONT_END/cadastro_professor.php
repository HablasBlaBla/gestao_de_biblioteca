<?php
include('../_BACK-END/cadastro_professor.php')
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Professor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="_css/cadastro_professor.css">
    <link rel="stylesheet" href="_css/theme.css">
    <script src="_static/theme.js"></script>
</head>
<body>

    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <h4>Cadastro de Professor</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="cpf" class="form-label">CPF</label>
                        <input type="text" class="form-control" name="cpf" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" name="senha" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Cadastrar</button>
                </form>

                <br>
                <h5>Professores Cadastrados:</h5>
                <ul class="list-group">
                    <?php
                    require '../conn.php'; // Requer a conexão com o banco

                    $sql = "SELECT id, nome, email FROM professores";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<li class='list-group-item d-flex justify-content-between align-items-center'>";
                            echo $row['nome'] . " (" . $row['email'] . ")";
                            echo "<form method='POST' class='d-inline-block' action=''>";
                            echo "<input type='hidden' name='professor_id' value='" . $row['id'] . "'>";
                            echo "<button type='submit' name='delete' class='btn btn-danger btn-sm'>Deletar</button>";
                            echo "</form>";
                            echo "</li>";
                        }
                    } else {
                        echo "<li class='list-group-item'>Nenhum professor encontrado.</li>";
                    }

                    $conn->close();
                    ?>
                </ul>
                <br>
                <a href="dashboard.php" class="btn btn-primary w-100" id="voltaDashboardId">Voltar para o Painel</a>
                <br>
            </div>
        </div>
    </div>
</body>
</html>
