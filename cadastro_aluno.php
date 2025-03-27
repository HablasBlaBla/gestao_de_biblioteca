<?php
session_start();

// Verifica se o professor está logado
if (!isset($_SESSION['professor_id'])) {
    header("Location: login.php");
    exit();
}

require 'conn.php'; // Arquivo de conexão com o banco

// Cadastro de aluno
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome'];
    $serie = $_POST['serie'];
    $email = $_POST['email'];

    // Valida o formato do e-mail
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<div class='alert alert-danger fade show' role='alert'>
                Erro: Email inválido!
              </div>";
        return;
    }

    $senha = isset($_POST['senha']) ? password_hash($_POST['senha'], PASSWORD_DEFAULT) : null; // Senha criptografada

    // Verifica se o email do aluno já existe
    $sql_check = "SELECT id FROM alunos WHERE email = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo "<div class='alert alert-danger fade show' role='alert'>
                Erro: Este email já está cadastrado!
              </div>";
    } else {
        // Inserindo no banco de dados
        $sql = "INSERT INTO alunos (nome, serie, email, senha) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $nome, $serie, $email, $senha);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success fade show' role='alert'>
                    Aluno cadastrado com sucesso!
                  </div>";
        } else {
            echo "<div class='alert alert-danger fade show' role='alert'>
                    Erro ao cadastrar aluno!
                  </div>";
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
    <title>Cadastro de Aluno</title>
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
}

.card-header {
    background-color: #00796b; /* Cor de fundo do cabeçalho */
    color: white;
    border-radius: 15px 15px 0 0; /* Aumentar o raio da borda */
    padding: 1rem; /* Adicionar padding */
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
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                <div class="d-flex">
                    <h4><i class="fas fa-user-plus"></i> Cadastro de Aluno</h4>
                    <a href="lista_alunos.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-list"></i> Ver Lista de Alunos
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome Completo</label>
                        <input type="text" class="form-control" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="serie" class="form-label">Série</label>
                        <input type="text" class="form-control" name="serie" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" name="senha">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save"></i> Cadastrar
                    </button>
                </form>
            </div>
        </div>
        <br>
                <a href="dashboard.php" class="btn btn-primary w-100" id="voltaDashboardId">Voltar para o Painel</a>
                <br>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
