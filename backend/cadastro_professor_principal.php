<?php
session_start();
include '../includes/conn.php';

function validarCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    
    if (strlen($cpf) != 11 || preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }
    return true;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $cpf = trim($_POST['cpf']);
    $senha = $_POST['senha'];

    // Validações básicas
    if (empty($nome) || empty($email) || empty($cpf) || empty($senha)) {
        $_SESSION['erro_cadastro'] = 'Todos os campos são obrigatórios!';
        header("Location: ../frontend/cadastro_professor_principal.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['erro_cadastro'] = 'E-mail inválido!';
        header("Location: ../frontend/cadastro_professor_principal.php");
        exit();
    }

    if (!validarCPF($cpf)) {
        $_SESSION['erro_cadastro'] = 'CPF inválido!';
        header("Location: ../frontend/cadastro_professor_principal.php");
        exit();
    }

    if (strlen($senha) < 8) {
        $_SESSION['erro_cadastro'] = 'A senha deve ter pelo menos 8 caracteres!';
        header("Location: ../frontend/cadastro_professor_principal.php");
        exit();
    }

    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    // Verifica se o email ou CPF já estão cadastrados
    $stmt = $conn->prepare("SELECT id FROM professores WHERE email = ? OR cpf = ?");
    $stmt->bind_param("ss", $email, $cpf);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['erro_cadastro'] = 'Email ou CPF já cadastrados!';
        header("Location: ../frontend/cadastro_professor_principal.php");
        exit();
    }
    $stmt->close();

    // Insere o novo professor como ativo diretamente
    $stmt = $conn->prepare("INSERT INTO professores (nome, email, cpf, senha, ativo) VALUES (?, ?, ?, ?, 1)");
    $stmt->bind_param("ssss", $nome, $email, $cpf, $senha_hash);
    
    if ($stmt->execute()) {
        $_SESSION['sucesso_cadastro'] = 'Cadastro realizado com sucesso! Você já pode fazer login.';
        header("Location: ../frontend/login.php");
    } else {
        $_SESSION['erro_cadastro'] = 'Erro ao cadastrar: ' . $conn->error;
        header("Location: ../frontend/cadastro_professor_principal.php");
    }
    
    $stmt->close();
    $conn->close();
    exit();
}