<?php
session_start();
include 'conn.php'; // Arquivo de conexão com o banco de dados

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $cpf = trim($_POST['cpf']);
    $senha = md5($_POST['senha']); // Hash da senha com MD5

    // Verifica se o email ou CPF já estão cadastrados
    $stmt = $conn->prepare("SELECT id FROM professores WHERE email = ? OR cpf = ?");
    $stmt->bind_param("ss", $email, $cpf);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Email ou CPF já cadastrados!');</script>";
    } else {
        // Insere o novo professor
        $stmt = $conn->prepare("INSERT INTO professores (nome, email, cpf, senha) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nome, $email, $cpf, $senha);
        if ($stmt->execute()) {
            echo "<script>alert('Cadastro realizado com sucesso!');</script>";
        } else {
            echo "<script>alert('Erro ao cadastrar!');</script>";
        }
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Professores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e0f7fa; /* Fundo suave */
            font-family: Arial, sans-serif;
            color: #212121;
        }
        .form-container {
            max-width: 500px;
            margin: 50px auto;
            background-color: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #006064; /* Azul mais escuro */
            font-size: 24px;
        }
        .form-label {
            font-size: 1.1rem;
            font-weight: bold;
        }
        .form-control {
            border-radius: 8px;
            padding: 12px;
            font-size: 1rem;
        }
        .form-control:focus {
            border-color: #004d40;
            box-shadow: 0 0 8px rgba(0, 100, 64, 0.4);
        }
        .btn-custom {
            background-color: #004d40;
            color: white;
            font-size: 1.1rem;
            padding: 12px 20px;
            border-radius: 8px;
            width: 100%;
        }
        .btn-custom:hover {
            background-color: #00332d;
            transition: background-color 0.3s ease;
        }
        .alert-custom {
            margin-top: 20px;
            background-color: #c8e6c9;
            color: #388e3c;
            padding: 15px;
            border-radius: 8px;
        }
        .hover-effect:hover {
            background-color: #004d40;
            color: white;
            transition: all 0.3s;
        }
        .mt-4 {
            margin-top: 1.5rem;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2 class="text-center mb-4">Cadastro de Professores</h2>
            <form method="POST">
                <div class="mb-4">
                    <label for="nome" class="form-label"><i class="fas fa-user"></i> Nome</label>
                    <input type="text" class="form-control" name="nome" id="nome" required aria-label="Nome" placeholder="Digite seu nome">
                </div>
                <div class="mb-4">
                    <label for="email" class="form-label"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" class="form-control" name="email" id="email" required aria-label="Email" placeholder="Digite seu email">
                </div>
                <div class="mb-4">
                    <label for="cpf" class="form-label"><i class="fas fa-id-card"></i> CPF</label>
                    <input type="text" class="form-control" name="cpf" id="cpf" required aria-label="CPF" placeholder="Digite seu CPF">
                </div>
                <div class="mb-4">
                    <label for="senha" class="form-label"><i class="fas fa-lock"></i> Senha</label>
                    <input type="password" class="form-control" name="senha" id="senha" required aria-label="Senha" placeholder="Digite sua senha">
                </div>
                <button type="submit" class="btn btn-custom">Cadastrar</button>
            </form>
            <p class="text-center mt-4">Já tem uma conta? <a href="login.php" class="hover-effect">Faça login</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
