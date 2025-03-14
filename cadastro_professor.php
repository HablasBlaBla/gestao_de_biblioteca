<?php
session_start();

// Verifica se o professor está logado
if (!isset($_SESSION['professor_id'])) {
    header("Location: login.php");
    exit();
}

require 'conn.php'; // Arquivo de conexão com o banco

// Deletar professor
if (isset($_POST['delete'])) {
    $professor_id = $_POST['professor_id'];

    $sql_delete = "DELETE FROM professores WHERE id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $professor_id);

    if ($stmt_delete->execute()) {
        echo "<div class='alert alert-success'>Professor deletado com sucesso!</div>";
    } else {
        echo "<div class='alert alert-danger'>Erro ao deletar professor!</div>";
    }

    $stmt_delete->close();
}

// Cadastrar professor
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['delete'])) {

    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $cpf = $_POST['cpf'];
    $senha = md5($_POST['senha']); // Senha criptografada

    // Verifica se o email do professor já existe
    $sql_check = "SELECT id FROM professores WHERE email = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo "<div class='alert alert-danger'>Erro: Este email já está cadastrado!</div>";
    } else {
        // Inserindo no banco de dados
        $sql = "INSERT INTO professores (nome, email, cpf, senha) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $nome, $email, $cpf, $senha);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Professor cadastrado com sucesso!</div>";
        } else {
            echo "<div class='alert alert-danger'>Erro ao cadastrar professor!</div>";
        }
    }

    $stmt_check->close();
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Professor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-primary text-white text-center">
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
                    require 'conn.php'; // Requer a conexão com o banco

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
